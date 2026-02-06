<?php
session_start();
require_once dirname(__DIR__) . '/../config/database.php';
require_once dirname(__DIR__) . '/../config/timezone.php';

$isDashboardPage = true;


if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Definir mensagem de toast antes de redirecionar
    $_SESSION['toast_message'] = 'Você precisa estar logado para acessar esta página.';
    $_SESSION['toast_type'] = 'danger';
    header("Location: /index.php"); // Redireciona para a página de login
    exit;
}

$conn = Database::getInstance();

// ========================================
// ESTATÍSTICAS GERAIS
// ========================================

// Total de equipamentos
$total_equipamentos = $conn->query("SELECT COUNT(*) FROM impressoras")->fetchColumn();

// Equipamentos por status
$stmt = $conn->query("
    SELECT status, COUNT(*) as total 
    FROM impressoras 
    GROUP BY status
");
$por_status = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$total_completos = $por_status['equipamento_completo'] ?? 0;
$total_manutencao = $por_status['equipamento_manutencao'] ?? 0;
$total_inativos = $por_status['inativo'] ?? 0;

// Total de páginas impressas
$total_paginas = $conn->query("SELECT SUM(contagem_paginas) FROM impressoras")->fetchColumn();

// Equipamentos por marca
$stmt = $conn->query("
    SELECT marca, COUNT(*) as total 
    FROM impressoras 
    GROUP BY marca 
    ORDER BY total DESC
");
$por_marca = $stmt->fetchAll();

// Total de peças retiradas
$total_pecas = $conn->query("SELECT COUNT(*) FROM pecas_retiradas")->fetchColumn();

// Peças mais retiradas (top 5)
$stmt = $conn->query("
    SELECT nome_peca, COUNT(*) as total 
    FROM pecas_retiradas 
    GROUP BY nome_peca 
    ORDER BY total DESC 
    LIMIT 5
");
$pecas_mais_usadas = $stmt->fetchAll();

// Últimas manutenções
$stmt = $conn->query("
    SELECT p.nome_peca, p.quantidade, p.data_retirada, 
           i.modelo
    FROM pecas_retiradas p
    JOIN impressoras i ON p.impressora_id = i.id
    ORDER BY p.data_retirada DESC
    LIMIT 5
");
$ultimas_manutencoes = $stmt->fetchAll();

// Média de páginas por equipamento
$media_paginas = $total_equipamentos > 0 ? round($total_paginas / $total_equipamentos) : 0;

include dirname(__DIR__) . '/../includes/header.php';
?>

<div id="sidebar">
    <div class="sidebar-header">
        <h3>Menu</h3>
    </div>
    <ul class="list-unstyled components">
        <li>
            <a href="/pages/printers/dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="/pages/printers/inventory.php">
                <i class="bi bi-list-ul"></i> Ver Todos Equipamentos
            </a>
        </li>
        <li>
            <a href="/pages/users/register.php">
                <i class="bi bi-person-plus"></i> Cadastrar Usuário
            </a>
        </li>
        <!-- Adicione mais opções de menu aqui -->
    </ul>
    <div class="sidebar-footer">
        <?php if (isset($_SESSION['user_name'])): ?>
            <a href="/pages/auth/processaLogout.php" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
        <?php endif; ?>
    </div>
</div>

<div id="main-content" class="container-fluid mt-4">
    
    <!-- Cabeçalho do Dashboard -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
            <p class="text-muted mb-0">Visão geral do sistema</p>
        </div>
        <div class="text-end">
            <small class="text-muted">Última atualização: <?= date('d/m/Y H:i') ?></small><br>
            <a href="/pages/printers/inventory.php" class="btn btn-outline-primary btn-sm mt-2">
                <i class="bi bi-list-ul"></i> Ver Todos Equipamentos
            </a>
        </div>
    </div>

    <!-- ========================================
         CARDS DE ESTATÍSTICAS PRINCIPAIS
         ======================================== -->
    
    <div class="row g-2 mb-5">
        <!-- Total de Equipamentos -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card stat-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total de Equipamentos</p>
                            <h2 class="mb-0 fw-bold"><?= number_format($total_equipamentos, 0, ',', '.') ?></h2>
                        </div>
                        <div class="stat-icon bg-primary">
                            <i class="bi bi-printer"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-bar-chart"></i> 
                            Média: <?= number_format($media_paginas, 0, ',', '.') ?> páginas/equip.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipamentos Completos -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card stat-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Equipamentos Completos</p>
                            <h2 class="mb-0 fw-bold text-success"><?= $total_completos ?></h2>
                        </div>
                        <div class="stat-icon bg-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i> 
                            <?= $total_equipamentos > 0 ? round(($total_completos / $total_equipamentos) * 100, 1) : 0 ?>% do total
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Em Manutenção -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card stat-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Em Manutenção</p>
                            <h2 class="mb-0 fw-bold text-warning"><?= $total_manutencao ?></h2>
                        </div>
                        <div class="stat-icon bg-warning">
                            <i class="bi bi-tools"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Requer atenção
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inativos -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card stat-secondary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Inativos</p>
                            <h2 class="mb-0 fw-bold text-secondary"><?= $total_inativos ?></h2>
                        </div>
                        <div class="stat-icon bg-secondary">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-secondary">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Requer atenção
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================
         GRÁFICOS E TABELAS
         ======================================== -->

   <div class="row g-2 mb-4">
    <!-- ========================================
         EQUIPAMENTOS POR MARCA
         ======================================== -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-building"></i> Por Marca
                </h5>
            </div>
            <div class="card-body">
                <div class="mt-3">
                    <?php foreach($por_marca as $marca): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><?= htmlspecialchars($marca['marca']) ?></span>
                        <span class="badge bg-primary"><?= $marca['total'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================
         PEÇAS MAIS UTILIZADAS
         ======================================== -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-box-seam"></i> Peças Mais Utilizadas
                </h5>
                <span class="badge bg-secondary"><?= number_format($total_pecas, 0, ',', '.') ?> total</span>
            </div>
            <div class="card-body">
                <?php if(count($pecas_mais_usadas) > 0): ?>
                    <?php foreach($pecas_mais_usadas as $peca): ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-box text-primary"></i>
                            <strong><?= htmlspecialchars($peca['nome_peca']) ?></strong>
                        </div>
                        <span class="badge bg-primary"><?= $peca['total'] ?> vezes</span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-3">Nenhuma peça retirada ainda</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ========================================
         ÚLTIMAS MANUTENÇÕES
         ======================================== -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history"></i> Últimas Manutenções
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if(count($ultimas_manutencoes) > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach($ultimas_manutencoes as $manutencao): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong><?= htmlspecialchars($manutencao['nome_peca']) ?></strong>
                                    <small class="text-muted d-block"> 
                                        <?= htmlspecialchars($manutencao['modelo']) ?>
                                    </small>
                                </div>
                                <small class="text-muted">
                                    <?= date('d/m/Y', strtotime($manutencao['data_retirada'])) ?>
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4">Nenhuma manutenção registrada</p>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center">
                <a href="manutencoes.php" class="btn btn-sm btn-outline-primary">
                    Ver Todas Manutenções <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

</div>
<script src="/assets/sidebar.js"></script>
<?php include dirname(__DIR__) . '/../includes/footer.php'; ?>