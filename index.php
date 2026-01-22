<?php
require_once 'config/database.php';
require_once 'config/timezone.php';
include 'includes/header.php';

$db = new Database();
$conn = $db->connect();

// Capturar filtros
$busca = $_GET['busca'] ?? '';
$marca = $_GET['marca'] ?? '';
$status = $_GET['status'] ?? '';

// Montar query dinâmica
$sql = "SELECT * FROM impressoras WHERE 1=1";
$params = [];

if ($busca) {
    $sql .= " AND (modelo LIKE :busca OR numero_serie LIKE :busca OR localizacao LIKE :busca)";
    $params[':busca'] = "%$busca%";
}

if ($marca) {
    $sql .= " AND marca = :marca";
    $params[':marca'] = $marca;
}

if ($status) {
    $sql .= " AND status = :status";
    $params[':status'] = $status;
}

$sql .= " ORDER BY data_cadastro DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$impressoras = $stmt->fetchAll();

// Buscar marcas únicas para filtro
$marcas = $conn->query("SELECT DISTINCT marca FROM impressoras ORDER BY marca")->fetchAll(PDO::FETCH_COLUMN);
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
</div>

<?php include 'includes/footer.php'; ?>
