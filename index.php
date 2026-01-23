<?php
// Ativar buffer de saída ANTES de qualquer coisa
ob_start();

// Configurar error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    // Iniciar sessão
    session_start();

    // Variáveis padrão
    $impressoras = [];
    $marcas = [];
    $total_registros = 0;
    $total_paginas = 0;
    $error_message = null;
    $conn = null;

    // Carregar configurações
    require_once 'config/database.php';
    require_once 'config/timezone.php';

    // Tentar conectar ao banco
    try {
        $conn = Database::getInstance();
    } catch(Exception $e) {
        $error_message = $e->getMessage();
    }

    // Limpar buffer e enviar headers
    ob_end_clean();
    
    // Incluir header (agora é 100% seguro)
    include 'includes/header.php';


    // Se conexão está OK, carregar dados
    try {

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
        $result = $stmt_count->fetch();
        $total_registros = $result['total'] ?? 0;
        $total_paginas = max(1, ceil($total_registros / $por_pagina));

        // Buscar impressoras com paginação
        $sql = "SELECT * " . $sql_base . " ORDER BY data_cadastro DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $conn->prepare($sql);
        
        // Executar cada parâmetro com seus tipos específicos
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        // Vincular LIMIT e OFFSET com tipos inteiros explícitos
        $stmt->bindValue(':limit', (int)$por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $impressoras = $stmt->fetchAll();

        // Buscar marcas únicas para filtro (com cache simples)
        if (empty($_SESSION['marcas_cache']) || time() - ($_SESSION['marcas_cache_time'] ?? 0) > 3600) {
            $marcas_stmt = $conn->query("SELECT DISTINCT marca FROM impressoras WHERE marca IS NOT NULL ORDER BY marca");
            $_SESSION['marcas_cache'] = $marcas_stmt->fetchAll(PDO::FETCH_COLUMN);
            $_SESSION['marcas_cache_time'] = time();
        }
        $marcas = $_SESSION['marcas_cache'];
    } catch(Exception $e) {
        error_log('Erro em index.php: ' . $e->getMessage());
        // Inicializar variáveis com valores vazios para evitar erros
        $impressoras = [];
        $marcas = [];
        $total_registros = 0;
        $total_paginas = 0;
        $error_message = 'Erro ao carregar impressoras: ' . htmlspecialchars($e->getMessage());
    }

} catch(Exception $e) {
    // Capturar QUALQUER erro não previsto
    ob_end_clean();
    header('Content-Type: text/html; charset=utf-8');
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Erro 500</title>
        <style>
            body { font-family: Arial; margin: 20px; background: #f5f5f5; }
            .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 4px; }
        </style>
    </head>
    <body>
        <div class="error">
            <h1>❌ Erro Interno do Servidor (500)</h1>
            <p><strong>Erro:</strong> <?= htmlspecialchars($e->getMessage()) ?></p>
            <hr/>
            <p><a href="/">← Voltar</a></p>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>

<?php if (isset($error_message) && $error_message !== null): ?>
<div class="alert alert-danger mb-4">
    <strong>❌ Erro ao Carregar Impressoras:</strong> <?= $error_message ?>
    <hr/>
    <small>
        <p><strong>Possíveis causas:</strong></p>
        <ul style="margin-bottom: 0;">
            <li>Servidor MySQL não está acessível</li>
            <li>Credenciais incorretas no arquivo .env</li>
            <li>Problema de conectividade de rede</li>
            <li>Servidor offline</li>
        </ul>
    </small>
</div>
<?php endif; ?>

<!-- FORM DE FILTROS -->
<div class="card mb-4">
    <div class="card-header">
        <h5 style="margin: 0; font-size: 1rem;">🔍 Filtros</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="filter-container">
            <div>
                <label class="form-label" style="margin-bottom: 0.35rem;">Buscar</label>
                <input type="text" name="busca" class="form-control" placeholder="Modelo, série ou localização..." value="<?= htmlspecialchars($busca ?? '') ?>">
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
                    <option value="equipamento_completo" <?= ($status ?? '') == 'equipamento_completo' ? 'selected' : '' ?>>✓ Equipamento Completo</option>
                    <option value="equipamento_manutencao" <?= ($status ?? '') == 'equipamento_manutencao' ? 'selected' : '' ?>>⚙️ Equipamento Precisa de Manutenção</option>
                    <option value="inativo" <?= ($status ?? '') == 'inativo' ? 'selected' : '' ?>>✗ Inativo</option>
                </select>
            </div>
            <div class="button-group mt-2">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="index.php" class="btn btn-secondary">Limpar</a>
            </div>
        </form>
    </div>
</div>

<!-- CARDS DE IMPRESSORAS -->
<div style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
        <h5 style="margin: 0; font-size: 1.1rem;">📋 Impressoras Cadastradas</h5>
        <span class="badge bg-secondary"><?= $total_registros ?> total</span>
    </div>
    
    <?php if (count($impressoras) > 0): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
            <?php foreach($impressoras as $imp): ?>
            <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                
                <!-- BODY DO CARD -->
                <div class="card-body" style="flex: 1; padding: 1.5rem; background: #0858d1;">
                    <h6 style="margin: 0 0 0.5rem 0; font-weight: bold; font-size: 1.1rem;">
                        <?= htmlspecialchars($imp['modelo']) ?>
                    </h6>
                    <small class="text-muted" style="display: block; margin-bottom: 1rem;">
                        <?= htmlspecialchars($imp['marca']) ?>
                    </small>
                    
                    <div style="font-size: 0.95rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <small class="text-muted d-block">📍 Local</small>
                            <strong><?= htmlspecialchars($imp['localizacao']) ?></strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">📄 Páginas</small>
                            <strong><?= number_format($imp['contagem_paginas'], 0, ',', '.') ?></strong>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding-top: 1rem; border-top: 1px solid #e9ecef;">
                        <div style="flex: 1;">
                            <small class="text-muted d-block">🔢 Série</small>
                            <code style="font-size: 0.85rem; word-break: break-all;">
                                <?= htmlspecialchars($imp['numero_serie']) ?>
                            </code>
                        </div>
                        <span class="badge bg-<?= in_array($imp['status'], ['equipamento_completo', 'ativo']) ? 'success' : (in_array($imp['status'], ['equipamento_manutencao', 'manutencao']) ? 'warning' : 'secondary') ?>" style="white-space: nowrap;">
                            <?= $imp['status'] == 'equipamento_completo' || $imp['status'] == 'ativo' ? '✓ Completo' : ($imp['status'] == 'equipamento_manutencao' || $imp['status'] == 'manutencao' ? '⚙️ Manutenção' : '✗ Inativo') ?>
                        </span>
                    </div>
                </div>
                
                <!-- FOOTER DO CARD (AÇÕES) -->
                <div class="card-footer" style="background: #f8f9fa; border-top: 1px solid #dee2e6; display: flex; gap: 0.5rem; padding: 0.75rem;">
                    <a href="detalhes.php?id=<?= $imp['id'] ?>" class="btn btn-sm btn-outline-info flex-grow-1" title="Ver detalhes">👁️ Ver</a>
                    <a href="editar.php?id=<?= $imp['id'] ?>" class="btn btn-sm btn-outline-warning flex-grow-1" title="Editar">✏️ Editar</a>
                    <a href="deletar.php?id=<?= $imp['id'] ?>" class="btn btn-sm btn-outline-danger flex-grow-1" title="Excluir" onclick="return confirm('Confirma exclusão?')">🗑️ Excluir</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <h5>📭 Nenhuma impressora encontrada</h5>
            <p class="mb-0">Tente ajustar seus filtros ou <a href="cadastrar.php">criar uma nova impressora</a></p>
        </div>
    <?php endif; ?>
    
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
