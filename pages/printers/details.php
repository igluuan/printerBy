<?php
ob_start();

$id = $_GET['id'] ?? 0;

// Lógica para lidar com o POST de adicionar peça
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_part') {
    $conn = Database::getInstance();
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
    
    $_SESSION['toast_message'] = 'Peça adicionada com sucesso!';
    $_SESSION['toast_type'] = 'success';

    ob_end_clean();
    header("Location: index.php?page=printers/details&id=$id");
    exit;
}

$conn = Database::getInstance();

// Busca os detalhes da impressora
$stmt = $conn->prepare("SELECT * FROM impressoras WHERE id = :id");
$stmt->execute([':id' => $id]);
$impressora = $stmt->fetch();

if (!$impressora) {
    $_SESSION['toast_message'] = 'Impressora não encontrada.';
    $_SESSION['toast_type'] = 'danger';
    ob_end_clean();
    header('Location: index.php?page=printers/inventory');
    exit;
}

// Busca o histórico de peças
$stmt = $conn->prepare("SELECT * FROM pecas_retiradas WHERE impressora_id = :id ORDER BY data_retirada DESC");
$stmt->execute([':id' => $id]);
$pecas = $stmt->fetchAll();



ob_end_clean();
?>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informações do Equipamento</h5>
                 <span class="badge bg-primary">ID: <?= $impressora['id'] ?></span>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <strong class="d-block text-muted">Modelo</strong>
                        <span><?= htmlspecialchars($impressora['modelo']) ?></span>
                    </div>
                    <div>
                        <strong class="d-block text-muted">Marca</strong>
                        <span><?= htmlspecialchars($impressora['marca']) ?></span>
                    </div>
                    <div class="grid-span-2">
                        <strong class="d-block text-muted">Nº Série</strong>
                        <code class="font-monospace"><?= htmlspecialchars($impressora['numero_serie']) ?></code>
                    </div>
                    <div>
                        <strong class="d-block text-muted">Localização</strong>
                        <span><?= htmlspecialchars($impressora['localizacao']) ?></span>
                    </div>
                    <div>
                        <strong class="d-block text-muted">Páginas Impressas</strong>
                        <span><?= number_format($impressora['contagem_paginas'], 0, ',', '.') ?></span>
                    </div>
                     <div>
                        <strong class="d-block text-muted">Cadastrado em</strong>
                        <span><?= formatarDataHora($impressora['data_cadastro']) ?></span>
                    </div>
                    <div>
                        <strong class="d-block text-muted">Status</strong>
                        <?php
                        $status_list = [
                            'equipamento_completo' => ['label' => 'Equipamento Completo', 'class' => 'success'],
                            'ativo' => ['label' => 'Equipamento Completo', 'class' => 'success'],
                            'equipamento_manutencao' => ['label' => 'Requer Manutenção', 'class' => 'warning'],
                            'manutencao' => ['label' => 'Requer Manutenção', 'class' => 'warning'],
                            'inativo' => ['label' => 'Inativo', 'class' => 'secondary']
                        ];
                        $status_info = $status_list[$impressora['status']] ?? ['label' => 'Desconhecido', 'class' => 'dark'];
                        ?>
                        <span class="badge bg-<?= $status_info['class'] ?>">
                            <?= $status_info['label'] ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-start gap-2">
                <a href="index.php?page=printers/inventory" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar para Lista
                </a>
                <a href="index.php?page=printers/edit&id=<?= $impressora['id'] ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <a href="index.php?page=printers/delete&id=<?= $impressora['id'] ?>" class="btn btn-danger ms-auto" onclick="return confirm('Tem certeza que deseja excluir este equipamento? Esta ação não pode ser desfeita.')">
                    <i class="bi bi-trash"></i> Excluir
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Adicionar Peça Retirada</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="add_part">
                    <div class="mb-3">
                        <label class="form-label">Nome da Peça *</label>
                        <input type="text" name="nome_peca" class="form-control" placeholder="Ex: Unidade de Fusor" required>
                        <small class="text-muted">Identificação clara da peça retirada</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Quantidade *</label>
                            <input type="number" name="quantidade" class="form-control" value="1" required min="1">
                        </div>
                        
                        <div class="col-6 mb-3">
                            <label class="form-label">Data Retirada *</label>
                            <input type="date" name="data_retirada" class="form-control" value="<?= dataHoje() ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea name="observacao" class="form-control" placeholder="Ex: Peça retirada para reparo" style="min-height: 80px;"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Adicionar ao Histórico</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Histórico de Peças Retiradas</h5>
    </div>
    <div class="card-body p-0">
        <?php if (count($pecas) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Peça</th>
                            <th class="text-center">Qtd</th>
                            <th>Data</th>
                            <th class="d-none d-md-table-cell">Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pecas as $peca): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($peca['nome_peca']) ?></strong></td>
                            <td class="text-center"><span class="badge bg-primary"><?= $peca['quantidade'] ?></span></td>
                            <td><?= formatarData($peca['data_retirada']) ?></td>
                            <td class="d-none d-md-table-cell">
                                <small class="text-muted"><?= htmlspecialchars($peca['observacao']) ?></small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4">Nenhuma peça retirada registrada para este equipamento.</p>
        <?php endif; ?>
    </div>
</div>

