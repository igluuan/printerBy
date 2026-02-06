<?php
ob_start();
session_start(); // Adicionado session_start()
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once '../../config/database.php'; // Caminho corrigido
    require_once '../../config/timezone.php' ;

    $conn = Database::getInstance();

    $id = $_GET['id'] ?? 0;

    $stmt = $conn->prepare("SELECT * FROM impressoras WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $impressora = $stmt->fetch();

    if (!$impressora) {
        $_SESSION['toast_message'] = 'Impressora não encontrada.';
        $_SESSION['toast_type'] = 'danger';
        ob_end_clean();
        header('Location: inventory.php'); // Redireciona para inventory.php
        exit;
    }

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
            
            $_SESSION['toast_message'] = 'Impressora atualizada com sucesso!';
            $_SESSION['toast_type'] = 'success';
            ob_end_clean();
            header('Location: details.php?id=' . $id);
            exit;
        } catch(Exception $e) {
            $_SESSION['toast_message'] = "Erro ao atualizar impressora: " . $e->getMessage();
            $_SESSION['toast_type'] = 'danger';
            ob_end_clean();
            header('Location: edit.php?id=' . $id); // Redireciona de volta para a página de edição
            exit;
        }
    }

    include '../../includes/header.php'; // Incluído aqui para exibir o toast se houver

} catch(Exception $e) {
    $_SESSION['toast_message'] = "Erro inesperado: " . $e->getMessage();
    $_SESSION['toast_type'] = 'danger';
    ob_end_clean();
    header('Location: inventory.php'); // Redireciona para inventory.php em caso de erro crítico
    exit;
}
?>

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
                    <select name="marca" class="form-select" required>
                        <option value="">Selecione uma marca...</option>
                        <option value="HP" <?= $impressora['marca'] == 'HP' ? 'selected' : '' ?>>HP</option>
                        <option value="BROTHER" <?= $impressora['marca'] == 'BROTHER' ? 'selected' : '' ?>>BROTHER</option>
                        <option value="SAMSUNG" <?= $impressora['marca'] == 'SAMSUNG' ? 'selected' : '' ?>>SAMSUNG</option>
                        <option value="OKIDATA" <?= $impressora['marca'] == 'OKIDATA' ? 'selected' : '' ?>>OKIDATA</option>
                        <option value="KYOCERA" <?= $impressora['marca'] == 'KYOCERA' ? 'selected' : '' ?>>KYOCERA</option>
                        <option value="CANON" <?= $impressora['marca'] == 'CANON' ? 'selected' : '' ?>>CANON</option>
                        <option value="RICOH" <?= $impressora['marca'] == 'RICOH' ? 'selected' : '' ?>>RICOH</option>
                    </select>
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
                        <option value="equipamento_completo" <?= $impressora['status'] == 'equipamento_completo' ? 'selected' : '' ?>>✓ Equipamento Completo</option>
                        <option value="equipamento_manutencao" <?= $impressora['status'] == 'equipamento_manutencao' ? 'selected' : '' ?>>⚙️ Equipamento Precisa de Manutenção</option>
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
                <a href="details.php?id=<?= $id ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
