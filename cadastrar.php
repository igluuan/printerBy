<?php
require_once 'config/database.php';
require_once 'config/timezone.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->connect();
    
    $sql = "INSERT INTO impressoras (modelo, marca, numero_serie, localizacao, status, contagem_paginas) 
            VALUES (:modelo, :marca, :numero_serie, :localizacao, :status, :contagem_paginas)";
    
    $stmt = $conn->prepare($sql);
    
    try {
        $stmt->execute([
            ':modelo' => $_POST['modelo'],
            ':marca' => $_POST['marca'],
            ':numero_serie' => $_POST['numero_serie'],
            ':localizacao' => $_POST['localizacao'],
            ':status' => $_POST['status'],
            ':contagem_paginas' => $_POST['contagem_paginas']
        ]);
        header('Location: index.php');
        exit;
    } catch(PDOException $e) {
        $erro = "Erro ao cadastrar: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<?php if(isset($erro)): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h4 style="font-size: clamp(1rem, 2vw, 1.25rem);">➕ Cadastrar Nova Impressora</h4>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Modelo *</label>
                    <input type="text" name="modelo" class="form-control" placeholder="Ex: LaserJet Pro M404" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Marca *</label>
                    <input type="text" name="marca" class="form-control" placeholder="Ex: HP" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Número de Série *</label>
                    <input type="text" name="numero_serie" class="form-control" placeholder="Ex: SN001HP2024" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Localização</label>
                    <input type="text" name="localizacao" class="form-control" placeholder="Ex: Sala 101">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="ativo">✓ Ativo</option>
                        <option value="manutencao">⚙️ Manutenção</option>
                        <option value="inativo">✗ Inativo</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contagem de Páginas</label>
                    <input type="number" name="contagem_paginas" class="form-control" value="0" placeholder="0">
                </div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-success">✓ Cadastrar</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
