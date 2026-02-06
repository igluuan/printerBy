<?php
ob_start();
session_start();

$hideLogout = false;
$isDashboardPage = true;


if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Definir mensagem de toast antes de redirecionar
    $_SESSION['toast_message'] = 'Você precisa estar logado para acessar esta página.';
    $_SESSION['toast_type'] = 'danger';
    header("Location: ../../public/index.php"); // Redireciona para a página de login
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once '../../config/database.php';
require_once '../../config/timezone.php';

$impressoras = [];
$marcas = [];
$total_registros = 0;
$total_paginas = 0;

try {
    $conn = Database::getInstance();
} catch (Exception $e) {
    $_SESSION['toast_message'] = 'Erro de conexão com o banco de dados: ' . $e->getMessage();
    $_SESSION['toast_type'] = 'danger';
    ob_end_clean();
    header("Location: ../../public/index.php"); // Redireciona em caso de erro crítico de conexão
    exit;
}

if ($conn) {
    try {
        $busca = trim($_GET['busca'] ?? '');
        
        $status_filter = isset($_GET['status']) && is_array($_GET['status']) ? $_GET['status'] : [];
        $marca_filter = isset($_GET['marca']) && is_array($_GET['marca']) ? $_GET['marca'] : [];

        $data_de = trim($_GET['data_de'] ?? '');
        $data_ate = trim($_GET['data_ate'] ?? '');

        $ordenar_por = trim($_GET['ordenar_por'] ?? 'data_cadastro_desc');

        $pagina = max(1, (int)($_GET['page'] ?? 1));
        $por_pagina = 25;
        $offset = ($pagina - 1) * $por_pagina;

        $sql_base = "FROM impressoras WHERE 1=1";
        $params = [];
        $filter_tags = [];

        if ($busca) {
            $sql_base .= " AND (modelo LIKE :busca OR numero_serie LIKE :busca OR localizacao LIKE :busca)";
            $params[':busca'] = "%$busca%";
            $filter_tags['busca'] = $busca;
        }

        if (!empty($status_filter)) {
            $placeholders = [];
            foreach ($status_filter as $i => $status) {
                $key = ":status{$i}";
                $placeholders[] = $key;
                $params[$key] = $status;
                $filter_tags['status'][$status] = $status;
            }
            $sql_base .= " AND status IN (" . implode(',', $placeholders) . ")";
        }
        
        if (!empty($marca_filter)) {
            $placeholders = [];
            foreach ($marca_filter as $i => $marca) {
                $key = ":marca{$i}";
                $placeholders[] = $key;
                $params[$key] = $marca;
                $filter_tags['marca'][$marca] = $marca;
            }
            $sql_base .= " AND marca IN (" . implode(',', $placeholders) . ")";
        }

        if ($data_de && $data_ate) {
            $sql_base .= " AND DATE(data_cadastro) BETWEEN :data_de AND :data_ate";
            $params[':data_de'] = $data_de;
            $params[':data_ate'] = $data_ate;
            $filter_tags['data_de'] = $data_de;
            $filter_tags['data_ate'] = $data_ate;
        }

        $stmt_count = $conn->prepare("SELECT COUNT(*) as total " . $sql_base);
        $stmt_count->execute($params);
        $total_registros = ($stmt_count->fetch()['total'] ?? 0);
        $total_paginas = max(1, ceil($total_registros / $por_pagina));

        $order_map = [
            'data_cadastro_desc' => 'data_cadastro DESC',
            'data_cadastro_asc' => 'data_cadastro ASC',
            'contagem_paginas_desc' => 'contagem_paginas DESC',
            'contagem_paginas_asc' => 'contagem_paginas ASC',
            'modelo_asc' => 'modelo ASC',
            'modelo_desc' => 'modelo DESC',
        ];
        $order_sql = $order_map[$ordenar_por] ?? 'data_cadastro DESC';
        $sql_base .= " ORDER BY {$order_sql}";

        $sql = "SELECT * " . $sql_base . " LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }
        
        $stmt->execute();
        $impressoras = $stmt->fetchAll();
        
        if (empty($_SESSION['marcas_cache']) || time() - ($_SESSION['marcas_cache_time'] ?? 0) > 3600) {
            $marcas_stmt = $conn->query("SELECT DISTINCT marca FROM impressoras WHERE marca IS NOT NULL AND marca != '' ORDER BY marca ASC");
            $_SESSION['marcas_cache'] = $marcas_stmt->fetchAll(PDO::FETCH_COLUMN);
            $_SESSION['marcas_cache_time'] = time();
        }
        $marcas = $_SESSION['marcas_cache'];
        
        $status_list = [
            'equipamento_completo' => 'Equipamento Completo',
            'equipamento_manutencao' => 'Requer Manutenção',
            'inativo' => 'Inativo'
        ];

    } catch (Exception $e) {
        error_log('Erro em index.php: ' . $e->getMessage());
        $_SESSION['toast_message'] = 'Erro ao carregar dados das impressoras: ' . htmlspecialchars($e->getMessage());
        $_SESSION['toast_type'] = 'danger';
        ob_end_clean();
        header("Location: inventory.php"); // Redireciona para a própria página para exibir o toast
        exit;
    }
}
?>
<?php include '../../includes/header.php'; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    
    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFilters" aria-controls="offcanvasFilters">
        <i class="bi bi-funnel-fill me-1"></i> Filtros
    </button>
    
    <div class="d-flex align-items-center gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-sort-down"></i> Ordenar
            </button>
            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                <li><a class="dropdown-item" href="#" data-sort="data_cadastro_desc">Mais Recentes</a></li>
                <li><a class="dropdown-item" href="#" data-sort="data_cadastro_asc">Mais Antigas</a></li>
                <li><a class="dropdown-item" href="#" data-sort="contagem_paginas_desc">Contador (Maior)</a></li>
                <li><a class="dropdown-item" href="#" data-sort="contagem_paginas_asc">Contador (Menor)</a></li>
                <li><a class="dropdown-item" href="#" data-sort="modelo_asc">Modelo (A-Z)</a></li>
                <li><a class="dropdown-item" href="#" data-sort="modelo_desc">Modelo (Z-A)</a></li>
            </ul>
        </div>
        
        <a href="cadastrar.php" class="btn btn-success" style="white-space: nowrap;">
            <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Adicionar</span>
        </a>
    </div>
</div>

<div id="filter-pills-bar" class="d-none card card-body bg-light mb-3">
    <div class="d-flex flex-wrap align-items-center gap-2">
        <strong class="me-2">Filtros Ativos:</strong>
        <div id="pills-container" class="d-flex flex-wrap gap-2"></div>
        <a href="#" id="clear-all-filters" class="btn btn-sm btn-outline-danger ms-auto">
            <i class="bi bi-trash me-1"></i> Limpar Tudo
        </a>
    </div>
    <hr class="my-2">
    <div id="results-feedback" class="text-muted small"></div>
</div>

<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasFilters" aria-labelledby="offcanvasFiltersLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasFiltersLabel"><i class="bi bi-funnel"></i> Painel de Filtros</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="filter-form">
            <div class="mb-3">
                <label for="filter-busca" class="form-label">Busca Rápida</label>
                <input type="text" id="filter-busca" name="busca" class="form-control" placeholder="Modelo, série, local..." value="<?= htmlspecialchars($busca) ?>">
            </div>

            <div class="accordion" id="filter-accordion">
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-status">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-status" aria-expanded="false" aria-controls="collapse-status">
                            Status
                        </button>
                    </h2>
                    <div id="collapse-status" class="accordion-collapse collapse" aria-labelledby="heading-status">
                        <div class="accordion-body">
                            <?php foreach($status_list as $key => $label): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status[]" value="<?= $key ?>" id="status-<?= $key ?>">
                                <label class="form-check-label" for="status-<?= $key ?>"><?= $label ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-marca">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-marca" aria-expanded="false" aria-controls="collapse-marca">
                            Marca
                        </button>
                    </h2>
                    <div id="collapse-marca" class="accordion-collapse collapse" aria-labelledby="heading-marca">
                        <div class="accordion-body">
                            <input type="text" id="marca-search" class="form-control form-control-sm mb-2" placeholder="Buscar marca...">
                            <div id="marca-list" style="max-height: 200px; overflow-y: auto;">
                                <?php foreach($marcas as $marca_item): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="marca[]" value="<?= $marca_item ?>" id="marca-<?= str_replace(' ', '', $marca_item) ?>">
                                    <label class="form-check-label" for="marca-<?= str_replace(' ', '', $marca_item) ?>"><?= $marca_item ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-data">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-data" aria-expanded="false" aria-controls="collapse-data">
                            Data de Cadastro
                        </button>
                    </h2>
                    <div id="collapse-data" class="accordion-collapse collapse" aria-labelledby="heading-data">
                        <div class="accordion-body">
                            <div class="btn-group btn-group-sm w-100 mb-2" role="group">
                                <button type="button" class="btn btn-outline-secondary" data-preset="hoje">Hoje</button>
                                <button type="button" class="btn btn-outline-secondary" data-preset="semana">7 dias</button>
                                <button type="button" class="btn btn-outline-secondary" data-preset="mes">Este Mês</button>
                            </div>
                            <div class="mb-2">
                                <label for="filter-data-de" class="form-label">De:</label>
                                <input type="date" id="filter-data-de" name="data_de" class="form-control" value="<?= htmlspecialchars($data_de) ?>">
                            </div>
                            <div>
                                <label for="filter-data-ate" class="form-label">Até:</label>
                                <input type="date" id="filter-data-ate" name="data_ate" class="form-control" value="<?= htmlspecialchars($data_ate) ?>">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <div class="offcanvas-footer p-3 border-top bg-light">
        <div class="d-grid gap-2">
            <button id="apply-filters" class="btn btn-primary"><i class="bi bi-check-lg"></i> Aplicar Filtros</button>
            <button id="reset-sidebar-filters" class="btn btn-outline-secondary">Limpar e Fechar</button>
        </div>
    </div>
</div>

<div id="impressoras-container">
    <?php if (count($impressoras) > 0): ?>
        
        <!-- ===== VERSÃO MOBILE (Cards) - Só aparece em telas pequenas ===== -->
        <div class="d-md-none">
            <div class="row g-2">
                <?php foreach($impressoras as $imp): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-0"><?= htmlspecialchars($imp['modelo']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($imp['marca']) ?></small>
                                </div>
                                <span class="badge bg-<?= $imp['status'] == 'equipamento_completo' ? 'success' : ($imp['status'] == 'equipamento_manutencao' ? 'warning' : 'secondary') ?>">
                                    <?= $status_list[$imp['status']] ?? 'Desconhecido' ?>
                                </span>
                            </div>
                            
                            <div class="small mb-2">
                                <div class="mb-1">
                                    <i class="bi bi-geo-alt text-muted"></i> 
                                    <strong><?= htmlspecialchars($imp['localizacao']) ?></strong>
                                </div>
                                <div class="mb-1">
                                    <i class="bi bi-upc text-muted"></i> 
                                    <?= htmlspecialchars($imp['numero_serie']) ?>
                                </div>
                                <div>
                                    <i class="bi bi-file-text text-muted"></i> 
                                    <?= number_format($imp['contagem_paginas'], 0, ',', '.') ?> páginas
                                </div>
                            </div>
                            
                            <a href="detalhes.php?id=<?= $imp['id'] ?>" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-eye"></i> Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="alert alert-info mt-3 small">
                <i class="bi bi-info-circle"></i>
                Total: <strong><?= count($impressoras) ?></strong> equipamentos
            </div>
        </div>
        
        <!-- ===== VERSÃO DESKTOP (Tabela) - Só aparece em telas médias ou maiores ===== -->
        <div class="d-none d-md-block">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul"></i> Lista de Equipamentos
                    </h5>
                    <span class="badge bg-light text-dark">
                        <?= count($impressoras) ?> <?= count($impressoras) == 1 ? 'equipamento' : 'equipamentos' ?>
                    </span>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th width="60" class="text-center">#</th>
                                    <th>Modelo</th>
                                    <th width="120">Marca</th>
                                    <th>Nº Série</th>
                                    <th>Localização</th>
                                    <th width="110" class="text-end">Páginas</th>
                                    <th width="150" class="text-center">Status</th>
                                    <th width="120" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($impressoras as $imp): ?>
                                <tr>
                                    <td class="text-center text-muted">
                                        <strong><?= $imp['id'] ?></strong>
                                    </td>
                                    
                                    <td>
                                        <strong><?= htmlspecialchars($imp['modelo']) ?></strong>
                                    </td>
                                    
                                    <td>
                                        <?= htmlspecialchars($imp['marca']) ?>
                                    </td>
                                    
                                    <td>
                                        <small class="text-muted font-monospace">
                                            <?= htmlspecialchars($imp['numero_serie']) ?>
                                        </small>
                                    </td>
                                    
                                    <td>
                                        <i class="bi bi-geo-alt text-muted"></i>
                                        <?= htmlspecialchars($imp['localizacao']) ?>
                                    </td>
                                    
                                    <td class="text-end">
                                        <strong><?= number_format($imp['contagem_paginas'], 0, ',', '.') ?></strong>
                                    </td>
                                    
                                    <td class="text-center">
                                        <?php
                                        $badge_class = match($imp['status']) {
                                            'equipamento_completo' => 'success',
                                            'equipamento_manutencao' => 'warning',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badge_class ?>">
                                            <?= $status_list[$imp['status']] ?? 'Desconhecido' ?>
                                        </span>
                                    </td>
                                    
                                    <td class="text-center">
                                        <a href="detalhes.php?id=<?= $imp['id'] ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Detalhes
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card-footer text-muted d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-info-circle"></i>
                        Mostrando <strong><?= count($impressoras) ?></strong> 
                        <?= count($impressoras) == 1 ? 'equipamento' : 'equipamentos' ?>
                    </span>
                    
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Mensagem quando não há dados -->
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
            <h3 class="text-muted mt-3">Nenhuma impressora encontrada</h3>
            <p>Tente ajustar seus filtros ou <a href="inventory.php">limpar a busca</a>.</p>
        </div>
    <?php endif; ?>
</div>

<?php if ($total_paginas > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= ($pagina <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="#" data-page="<?= $pagina - 1 ?>">Anterior</a>
        </li>

        <?php 
        $inicio = max(1, $pagina - 2);
        $fim = min($total_paginas, $pagina + 2);
        if ($inicio > 1) {
            echo '<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>';
            if ($inicio > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        for ($i = $inicio; $i <= $fim; $i++) {
            echo '<li class="page-item ' . ($i == $pagina ? 'active' : '') . '"><a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a></li>';
        }
        if ($fim < $total_paginas) {
            if ($fim < $total_paginas - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            echo '<li class="page-item"><a class="page-link" href="#" data-page="' . $total_paginas . '">' . $total_paginas . '</a></li>';
        }
        ?>
        
        <li class="page-item <?= ($pagina >= $total_paginas) ? 'disabled' : '' ?>">
            <a class="page-link" href="#" data-page="<?= $pagina + 1 ?>">Próximo</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filter-form');
    const pillsContainer = document.getElementById('pills-container');
    const pillsBar = document.getElementById('filter-pills-bar');
    const resultsFeedback = document.getElementById('results-feedback');
    
    const filterNames = {
        busca: 'Busca',
        status: 'Status',
        marca: 'Marca',
        data_de: 'Data Inicial',
        data_ate: 'Data Final'
    };
    
    const statusLabels = <?= json_encode($status_list) ?>;

    function applyFilters() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();

        const status = formData.getAll('status[]');
        if (status.length > 0) status.forEach(s => params.append('status[]', s));
        
        const marca = formData.getAll('marca[]');
        if (marca.length > 0) marca.forEach(m => params.append('marca[]', m));

        if (formData.get('busca')) params.set('busca', formData.get('busca'));
        if (formData.get('data_de')) params.set('data_de', formData.get('data_de'));
        if (formData.get('data_ate')) params.set('data_ate', formData.get('data_ate'));
        
        const currentParams = new URLSearchParams(window.location.search);
        if (currentParams.get('ordenar_por')) params.set('ordenar_por', currentParams.get('ordenar_por'));
        
        window.location.search = params.toString();
    }
    
    function renderPills() {
        const params = new URLSearchParams(window.location.search);
        pillsContainer.innerHTML = '';
        let hasFilters = false;

        params.forEach((value, key) => {
            if (!value) return;
            hasFilters = true;
            
            const name = key.replace('[]', '');
            let label = filterNames[name] || name;
            let displayValue = value;
            
            if (name === 'status' && statusLabels[value]) {
                displayValue = statusLabels[value].replace(/✓ |⚙️ |✗ /g, '');
            }
            if (name === 'data_de' || name === 'data_ate') {
                displayValue = new Date(value + 'T00:00:00').toLocaleDateString('pt-BR');
            }

            const pill = document.createElement('span');
            pill.className = 'badge bg-secondary d-flex align-items-center gap-2';
            pill.innerHTML = `
                ${label}: <strong>${displayValue}</strong>
                <button type="button" class="btn-close btn-close-white" aria-label="Remover" data-key="${key}" data-value="${value}"></button>
            `;
            pillsContainer.appendChild(pill);
        });

        pillsBar.classList.toggle('d-none', !hasFilters);
        resultsFeedback.textContent = `Mostrando <?= count($impressoras) ?> de <?= $total_registros ?> impressoras encontradas.`;
    }

    function initializeForm() {
        const params = new URLSearchParams(window.location.search);
        
        document.getElementById('filter-busca').value = params.get('busca') || '';
        document.getElementById('filter-data-de').value = params.get('data_de') || '';
        document.getElementById('filter-data-ate').value = params.get('data_ate') || '';
        
        params.getAll('status[]').forEach(val => {
            const el = document.getElementById(`status-${val}`);
            if (el) el.checked = true;
        });

        params.getAll('marca[]').forEach(val => {
            const el = document.getElementById(`marca-${val.replace(' ', '')}`);
            if (el) el.checked = true;
        });

        const sortKey = params.get('ordenar_por') || 'data_cadastro_desc';
        const sortOption = document.querySelector(`.dropdown-item[data-sort="${sortKey}"]`);
        if (sortOption) {
            document.getElementById('sortDropdown').innerHTML = `<i class="bi bi-sort-down"></i> ${sortOption.textContent}`;
        }
    }

    document.getElementById('apply-filters').addEventListener('click', applyFilters);
    
    document.getElementById('clear-all-filters').addEventListener('click', (e) => {
        e.preventDefault();
        const currentParams = new URLSearchParams(window.location.search);
        const newParams = new URLSearchParams();
        if (currentParams.has('ordenar_por')) {
            newParams.set('ordenar_por', currentParams.get('ordenar_por'));
        }
        window.location.search = newParams.toString();
    });

    document.getElementById('reset-sidebar-filters').addEventListener('click', () => {
        filterForm.reset();
        const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasFilters'));
        offcanvas.hide();
    });
    
    pillsContainer.addEventListener('click', function(e) {
        if (e.target.matches('.btn-close')) {
            const key = e.target.dataset.key;
            const value = e.target.dataset.value;
            
            const params = new URLSearchParams(window.location.search);
            
            if (key.endsWith('[]')) {
                const allValues = params.getAll(key);
                params.delete(key);
                allValues.filter(v => v !== value).forEach(v => params.append(key, v));
            } else if (key === 'data_de' || key === 'data_ate') {
                params.delete('data_de');
                params.delete('data_ate');
            } else {
                params.delete(key);
            }
            window.location.search = params.toString();
        }
    });

    document.getElementById('marca-search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('#marca-list .form-check').forEach(item => {
            const label = item.querySelector('label').textContent.toLowerCase();
            item.style.display = label.includes(searchTerm) ? '' : 'none';
        });
    });

    document.querySelectorAll('[data-preset]').forEach(button => {
        button.addEventListener('click', function() {
            const preset = this.dataset.preset;
            const hoje = new Date();
            hoje.setHours(0,0,0,0);
            const y = hoje.getFullYear();
            const m = String(hoje.getMonth() + 1).padStart(2, '0');
            const d = String(hoje.getDate()).padStart(2, '0');
            
            const de = document.getElementById('filter-data-de');
            const ate = document.getElementById('filter-data-ate');
            
            ate.value = `${y}-${m}-${d}`;

            if (preset === 'hoje') {
                de.value = ate.value;
            } else if (preset === 'semana') {
                const semanaAtras = new Date(hoje.getTime() - 6 * 24 * 60 * 60 * 1000);
                de.value = semanaAtras.toISOString().split('T')[0];
            } else if (preset === 'mes') {
                de.value = `${y}-${m}-01`;
            }
        });
    });

    document.querySelectorAll('.dropdown-item[data-sort]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sortValue = this.dataset.sort;
            const params = new URLSearchParams(window.location.search);
            params.set('ordenar_por', sortValue);
            window.location.search = params.toString();
        });
    });

    document.querySelectorAll('.pagination .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (this.parentElement.classList.contains('disabled')) return;
            const page = this.dataset.page;
            const params = new URLSearchParams(window.location.search);
            params.set('page', page);
            window.location.search = params.toString();
        });
    });

    initializeForm();
    renderPills();
});
</script>

<?php include '../../includes/footer.php'; ?>
