<?php
ob_start();

$conn = Database::getInstance();
$id = $_GET['id'] ?? 0;

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

$erro = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Futuramente, ideal seria usar transações se houver mais tabelas relacionadas
        $sql_pecas = "DELETE FROM pecas_retiradas WHERE impressora_id = :id";
        $stmt_pecas = $conn->prepare($sql_pecas);
        $stmt_pecas->execute([':id' => $id]);

        $sql = "DELETE FROM impressoras WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $_SESSION['toast_message'] = 'Impressora e seu histórico foram deletados com sucesso!';
        $_SESSION['toast_type'] = 'success';
        ob_end_clean();
        header('Location: index.php?page=printers/inventory');
        exit;
    } catch(Exception $e) {
        $erro = "Erro ao deletar: " . $e->getMessage();
    }
}
ob_end_clean();
?>

<div class="card border-danger">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0"><i class="bi bi-exclamation-triangle-fill"></i> Confirmação de Exclusão</h4>
    </div>
    <div class="card-body">
        <?php if ($erro): ?>
            <div class="alert alert-danger">
                <strong>Erro:</strong> <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-warning">
            <strong>Atenção!</strong> Esta ação é <strong>irreversível</strong>. Você está prestes a deletar permanentemente o equipamento abaixo e todo o seu histórico de peças.
        </div>
        
        <div class="mb-4 p-3 bg-light rounded border">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <strong class="d-block text-muted">Modelo</strong>
                    <span><?= htmlspecialchars($impressora['modelo']) ?></span>
                </div>
                <div>
                    <strong class="d-block text-muted">Marca</strong>
                    <span><?= htmlspecialchars($impressora['marca']) ?></span>
                </div>
                <div style="grid-column: 1 / -1;">
                    <strong class="d-block text-muted">Nº Série</strong>
                    <code class="font-monospace"><?= htmlspecialchars($impressora['numero_serie']) ?></code>
                </div>
                <div>
                    <strong class="d-block text-muted">Localização</strong>
                    <span><?= htmlspecialchars($impressora['localizacao']) ?></span>
                </div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <a href="index.php?page=printers/details&id=<?= $id ?>" class="btn btn-secondary">
                <i class="bi bi-x-lg"></i> Cancelar
            </a>
            <form method="POST" class="ms-auto">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash-fill"></i> Confirmar Exclusão
                </button>
            </form>
        </div>
    </div>
</div>
