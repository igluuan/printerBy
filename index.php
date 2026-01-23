<?php
require_once 'config/database.php';
require_once 'config/timezone.php';
include 'includes/header.php';

$conn = Database::getInstance();

// Capturar filtros
$busca = $_GET['busca'] ?? '';
$marca = $_GET['marca'] ?? '';
$status = $_GET['status'] ?? '';
$pagina = max(1, (int)($_GET['page'] ?? 1));
$por_pagina = 25;
$offset = ($pagina - 1) * $por_pagina;

// Montar query dinâmica (sem LIMIT para contagem)
$sql_base = "FROM impressoras WHERE 1=1";
$params = [];

if ($busca) {
    $sql_base .= " AND (modelo LIKE :busca OR numero_serie LIKE :busca OR localizacao LIKE :busca)";
    $params[':busca'] = "%$busca%";
}

if ($marca) {
    $sql_base .= " AND marca = :marca";
    $params[':marca'] = $marca;
}

if ($status) {
    $sql_base .= " AND status = :status";
    $params[':status'] = $status;
}

// Contar total de registros
$stmt_count = $conn->prepare("SELECT COUNT(*) as total " . $sql_base);
$stmt_count->execute($params);
$total_registros = $stmt_count->fetch()['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// Buscar impressoras com paginação
$sql = "SELECT * " . $sql_base . " ORDER BY data_cadastro DESC LIMIT :limit OFFSET :offset";
$params[':limit'] = $por_pagina;
$params[':offset'] = $offset;

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$impressoras = $stmt->fetchAll();

// Buscar marcas únicas para filtro (com cache simples)
if (empty($_SESSION['marcas_cache']) || time() - ($_SESSION['marcas_cache_time'] ?? 0) > 3600) {
    $_SESSION['marcas_cache'] = $conn->query("SELECT DISTINCT marca FROM impressoras WHERE marca IS NOT NULL ORDER BY marca")->fetchAll(PDO::FETCH_COLUMN);
    $_SESSION['marcas_cache_time'] = time();
}
$marcas = $_SESSION['marcas_cache'];
?>

<!-- FORM DE FILTROS -->
<div class="card mb-4">
    <div class="card-header">
        <h5 style="margin: 0; font-size: 1rem;">🔍 Filtros</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="filter-container">
            <div>
                <label class="form-label" style="margin-bottom: 0.35rem;">Buscar</label>
                <input type="text" name="busca" class="form-control" placeholder="Modelo, série ou localização..." value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div>
                <label class="form-label" style="margin-bottom: 0.35rem;">Marca</label>
                <select name="marca" class="form-select">
                    <option value="">Todas as marcas</option>
                    <?php foreach($marcas as $m): ?>
                        <option value="<?= $m ?>" <?= $marca == $m ? 'selected' : '' ?>><?= $m ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" style="margin-bottom: 0.35rem;">Status</label>
                <select name="status" class="form-select">
                    <option value="">Todos os status</option>
                    <option value="equipamento_completo" <?= $status == 'equipamento_completo' ? 'selected' : '' ?>>✓ Equipamento Completo</option>
                    <option value="equipamento_manutencao" <?= $status == 'equipamento_manutencao' ? 'selected' : '' ?>>⚙️ Equipamento Precisa de Manutenção</option>
                    <option value="inativo" <?= $status == 'inativo' ? 'selected' : '' ?>>✗ Inativo</option>
                </select>
            </div>
            <div class="button-group mt-2">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="index.php" class="btn btn-secondary">Limpar</a>
            </div>
        </form>
    </div>
</div>

<!-- TABELA DE IMPRESSORAS -->
<div class="card">
    <div class="card-header">
        <h5 style="margin: 0; font-size: 1rem;">📋 Impressoras Cadastradas</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Modelo</th>
                    <th class="d-none d-sm-table-cell">Marca</th>
                    <th class="d-none d-md-table-cell">Série</th>
                    <th class="d-none d-lg-table-cell">Local</th>
                    <th>Status</th>
                    <th class="d-none d-sm-table-cell">Pág.</th>
                    <th style="text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($impressoras) > 0): ?>
                    <?php foreach($impressoras as $imp): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($imp['modelo']) ?></strong>
                            <br><small class="text-muted d-sm-none"><?= htmlspecialchars($imp['marca']) ?></small>
                        </td>
                        <td class="d-none d-sm-table-cell"><?= htmlspecialchars($imp['marca']) ?></td>
                        <td class="d-none d-md-table-cell"><code style="font-size: 0.75rem;"><?= htmlspecialchars(substr($imp['numero_serie'], 0, 8)) ?></code></td>
                        <td class="d-none d-lg-table-cell"><?= htmlspecialchars($imp['localizacao']) ?></td>
                        <td>
                            <span class="badge bg-<?= in_array($imp['status'], ['equipamento_completo', 'ativo']) ? 'success' : (in_array($imp['status'], ['equipamento_manutencao', 'manutencao']) ? 'warning' : 'secondary') ?>">
                                <?= $imp['status'] == 'equipamento_completo' || $imp['status'] == 'ativo' ? 'Completo' : ($imp['status'] == 'equipamento_manutencao' || $imp['status'] == 'manutencao' ? 'Manutenção' : 'Inativo') ?>
                            </span>
                        </td>
                        <td class="d-none d-sm-table-cell"><?= number_format($imp['contagem_paginas'], 0, ',', '.') ?></td>
                        <td style="text-align: center;">
                            <div class="btn-group btn-group-sm" role="group" style="display: flex; gap: 0.25rem; justify-content: center; flex-wrap: wrap;">
                                <a href="detalhes.php?id=<?= $imp['id'] ?>" class="btn btn-info" title="Ver detalhes">👁️</a>
                                <a href="editar.php?id=<?= $imp['id'] ?>" class="btn btn-warning" title="Editar">✏️</a>
                                <a href="deletar.php?id=<?= $imp['id'] ?>" class="btn btn-danger" title="Excluir" onclick="return confirm('Confirma exclusão?')">🗑️</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Nenhuma impressora encontrada</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- PAGINAÇÃO -->
    <?php if ($total_paginas > 1): ?>
    <nav class="navbar bg-light border-top" style="padding: 0.75rem 1rem;">
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; font-size: 0.9rem;">
            <span class="text-muted">Página <?= $pagina ?> de <?= $total_paginas ?> (<?= $total_registros ?> total)</span>
            
            <div style="display: flex; gap: 0.25rem; margin-left: auto;">
                <!-- Primeira página -->
                <?php if ($pagina > 1): ?>
                    <a href="?page=1&busca=<?= urlencode($busca) ?>&marca=<?= urlencode($marca) ?>&status=<?= urlencode($status) ?>" class="btn btn-sm btn-outline-secondary" title="Primeira">«</a>
                    <a href="?page=<?= $pagina - 1 ?>&busca=<?= urlencode($busca) ?>&marca=<?= urlencode($marca) ?>&status=<?= urlencode($status) ?>" class="btn btn-sm btn-outline-secondary" title="Anterior">‹</a>
                <?php else: ?>
                    <button class="btn btn-sm btn-outline-secondary" disabled>«</button>
                    <button class="btn btn-sm btn-outline-secondary" disabled>‹</button>
                <?php endif; ?>
                
                <!-- Números de página -->
                <?php 
                $inicio = max(1, $pagina - 2);
                $fim = min($total_paginas, $pagina + 2);
                
                if ($inicio > 1) echo '<span class="text-muted" style="padding: 0 0.25rem;">...</span>';
                
                for ($i = $inicio; $i <= $fim; $i++) {
                    if ($i == $pagina) {
                        echo '<button class="btn btn-sm btn-secondary" disabled>' . $i . '</button>';
                    } else {
                        echo '<a href="?page=' . $i . '&busca=' . urlencode($busca) . '&marca=' . urlencode($marca) . '&status=' . urlencode($status) . '" class="btn btn-sm btn-outline-secondary">' . $i . '</a>';
                    }
                }
                
                if ($fim < $total_paginas) echo '<span class="text-muted" style="padding: 0 0.25rem;">...</span>';
                ?>
                
                <!-- Última página -->
                <?php if ($pagina < $total_paginas): ?>
                    <a href="?page=<?= $pagina + 1 ?>&busca=<?= urlencode($busca) ?>&marca=<?= urlencode($marca) ?>&status=<?= urlencode($status) ?>" class="btn btn-sm btn-outline-secondary" title="Próxima">›</a>
                    <a href="?page=<?= $total_paginas ?>&busca=<?= urlencode($busca) ?>&marca=<?= urlencode($marca) ?>&status=<?= urlencode($status) ?>" class="btn btn-sm btn-outline-secondary" title="Última">»</a>
                <?php else: ?>
                    <button class="btn btn-sm btn-outline-secondary" disabled>›</button>
                    <button class="btn btn-sm btn-outline-secondary" disabled>»</button>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
