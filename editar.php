<?php
require_once 'config/database.php';
require_once 'config/timezone.php';

$db = new Database();
$conn = $db->connect();

$id = $_GET['id'] ?? 0;

// Buscar impressora
$stmt = $conn->prepare("SELECT * FROM impressoras WHERE id = :id");
$stmt->execute([':id' => $id]);
$impressora = $stmt->fetch();

if (!$impressora) {
    header('Location: index.php');
    exit;
}

// Atualizar impressora
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "UPDATE impressoras SET modelo = :modelo, marca = :marca, numero_serie = :numero_serie, 
            localizacao = :localizacao, status = :status, contagem_paginas = :contagem_paginas 
            WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    
    try {
        $stmt->execute([
            ':modelo' => $_POST['modelo'],
            ':marca' => $_POST['marca'],
            ':numero_serie' => $_POST['numero_serie'],
            ':localizacao' => $_POST['localizacao'],
            ':status' => $_POST['status'],
            ':contagem_paginas' => $_POST['contagem_paginas'],
            ':id' => $id
        ]);
        header('Location: detalhes.php?id=' . $id);
        exit;
    } catch(PDOException $e) {
        $erro = "Erro ao atualizar: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<?php if(isset($erro)): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h4 style="font-size: clamp(1rem, 2vw, 1.25rem);">✏️ Editar Impressora</h4>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Modelo *</label>
                    <input type="text" name="modelo" class="form-control" value="<?= htmlspecialchars($impressora['modelo']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Marca *</label>
                    <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($impressora['marca']) ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Número de Série *</label>
                    <input type="text" name="numero_serie" class="form-control" value="<?= htmlspecialchars($impressora['numero_serie']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Localização</label>
                    <input type="text" name="localizacao" class="form-control" value="<?= htmlspecialchars($impressora['localizacao']) ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="ativo" <?= $impressora['status'] == 'ativo' ? 'selected' : '' ?>>✓ Ativo</option>
                        <option value="manutencao" <?= $impressora['status'] == 'manutencao' ? 'selected' : '' ?>>⚙️ Manutenção</option>
                        <option value="inativo" <?= $impressora['status'] == 'inativo' ? 'selected' : '' ?>>✗ Inativo</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contagem de Páginas</label>
                    <input type="number" name="contagem_paginas" class="form-control" value="<?= $impressora['contagem_paginas'] ?>">
                </div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-success">✓ Salvar</button>
                <a href="detalhes.php?id=<?= $id ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
