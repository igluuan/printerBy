<?php
require_once 'config/database.php';
require_once 'config/timezone.php';

$conn = Database::getInstance();

$id = $_GET['id'] ?? 0;

// Buscar impressora para confirmar existência
$stmt = $conn->prepare("SELECT * FROM impressoras WHERE id = :id");
$stmt->execute([':id' => $id]);
$impressora = $stmt->fetch();

if (!$impressora) {
    header('Location: index.php');
    exit;
}

// Deletar impressora (CASCADE já apaga as peças relacionadas)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $sql = "DELETE FROM impressoras WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        header('Location: index.php?msg=Impressora deletada com sucesso');
        exit;
    } catch(PDOException $e) {
        $erro = "Erro ao deletar: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="card border-danger">
    <div class="card-header bg-danger">
        <h4 class="text-white" style="font-size: clamp(1rem, 2vw, 1.25rem); margin: 0;">⚠️ Confirmação de Exclusão</h4>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <strong>⚠️ Atenção!</strong> Esta ação é <strong>irreversível</strong>. Você está deletando:
        </div>
        
        <div class="mb-4 p-3 bg-light rounded" style="border-left: 4px solid #dc3545;">
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
            </div>
        </div>

        <div class="alert alert-info">
            <strong>ℹ️ Nota:</strong> Todas as peças associadas também serão deletadas.
        </div>
        
        <div class="button-group" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <form method="POST" style="flex: 1; min-width: 120px;">
                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Tem certeza que deseja deletar?')">🗑️ Deletar</button>
            </form>
            <a href="detalhes.php?id=<?= $id ?>" class="btn btn-secondary" style="flex: 1; min-width: 120px;">← Cancelar</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
