<?php
require_once 'config/database.php';
require_once 'config/timezone.php';

$db = new Database();
$conn = $db->connect();

$id = $_GET['id'] ?? 0;

// Adicionar peça
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "INSERT INTO pecas_retiradas (impressora_id, nome_peca, quantidade, data_retirada, observacao) 
            VALUES (:impressora_id, :nome_peca, :quantidade, :data_retirada, :observacao)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':impressora_id' => $id,
        ':nome_peca' => $_POST['nome_peca'],
        ':quantidade' => $_POST['quantidade'],
        ':data_retirada' => $_POST['data_retirada'],
        ':observacao' => $_POST['observacao']
    ]);
    
    header("Location: detalhes.php?id=$id");
    exit;
}

// Buscar impressora
$stmt = $conn->prepare("SELECT * FROM impressoras WHERE id = :id");
$stmt->execute([':id' => $id]);
$impressora = $stmt->fetch();

if (!$impressora) {
    header('Location: index.php');
    exit;
}

// Buscar peças
$stmt = $conn->prepare("SELECT * FROM pecas_retiradas WHERE impressora_id = :id ORDER BY data_retirada DESC");
$stmt->execute([':id' => $id]);
$pecas = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 style="font-size: 1rem; margin: 0;">📋 Informações</h5>
            </div>
            <div class="card-body">
                <div style="display: grid; gap: 0.75rem;">
                    <div>
                        <strong style="display: block; font-size: 0.85rem; color: #666;">Modelo</strong>
                        <span><?= htmlspecialchars($impressora['modelo']) ?></span>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.85rem; color: #666;">Marca</strong>
                        <span><?= htmlspecialchars($impressora['marca']) ?></span>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.85rem; color: #666;">Nº Série</strong>
                        <code style="font-size: 0.75rem;"><?= htmlspecialchars($impressora['numero_serie']) ?></code>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.85rem; color: #666;">Localização</strong>
                        <span><?= htmlspecialchars($impressora['localizacao']) ?></span>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.85rem; color: #666;">Status</strong>
                        <span class="badge bg-<?= $impressora['status'] == 'ativo' ? 'success' : ($impressora['status'] == 'manutencao' ? 'warning' : 'secondary') ?>" style="margin-top: 0.25rem;">
                            <?= ucfirst($impressora['status']) ?>
                        </span>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.85rem; color: #666;">Páginas Impressas</strong>
                        <span><?= number_format($impressora['contagem_paginas'], 0, ',', '.') ?></span>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.85rem; color: #666;">Cadastrado em</strong>
                        <span><?= formatarDataHora($impressora['data_cadastro']) ?></span>
                    </div>
                </div>
                <div class="button-group" style="margin-top: 1rem;">
                    <a href="editar.php?id=<?= $impressora['id'] ?>" class="btn btn-warning">✏️ Editar</a>
                    <a href="index.php" class="btn btn-secondary">←  Voltar</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 style="font-size: 1rem; margin: 0;">➕ Adicionar Peça</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nome da Peça *</label>
                        <input type="text" name="nome_peca" class="form-control" placeholder="Ex: Unidade de Fusor" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantidade *</label>
                        <input type="number" name="quantidade" class="form-control" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data Retirada *</label>
                        <input type="date" name="data_retirada" class="form-control" value="<?= dataHoje() ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea name="observacao" class="form-control" placeholder="Ex: Peça retirada para reparo" style="min-height: 80px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">✓ Adicionar Peça</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 style="font-size: 1rem; margin: 0;">📦 Histórico de Peças Retiradas</h5>
    </div>
    <div class="card-body">
        <?php if (count($pecas) > 0): ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 40%;">Peça</th>
                            <th style="width: 20%;">Qtd</th>
                            <th style="width: 20%;">Data</th>
                            <th style="width: 20%;" class="d-none d-md-table-cell">Obs.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pecas as $peca): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($peca['nome_peca']) ?></strong></td>
                            <td><span class="badge bg-info"><?= $peca['quantidade'] ?></span></td>
                            <td><small><?= formatarData($peca['data_retirada']) ?></small></td>
                            <td class="d-none d-md-table-cell"><small class="text-muted" style="display: block; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= htmlspecialchars($peca['observacao']) ?>
                            </small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center" style="padding: 1rem;">📦 Nenhuma peça retirada registrada</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
