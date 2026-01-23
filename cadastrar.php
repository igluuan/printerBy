<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    session_start();
    require_once 'config/database.php';
    require_once 'config/timezone.php';

    $erro = null;
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $conn = Database::getInstance();
            
            // Sanitizar e validar todos os campos
            $modelo = trim($_POST['modelo'] ?? '');
            $marca = trim($_POST['marca'] ?? '');
            $numero_serie = trim($_POST['numero_serie'] ?? '');
            $localizacao = trim($_POST['localizacao'] ?? '');
            $status = trim($_POST['status'] ?? 'equipamento_completo');
            $contagem_paginas = isset($_POST['contagem_paginas']) ? (int)$_POST['contagem_paginas'] : 0;
              
            $sql = "INSERT INTO impressoras (modelo, marca, numero_serie, localizacao, status, contagem_paginas) 
                    VALUES (:modelo, :marca, :numero_serie, :localizacao, :status, :contagem_paginas)";
            
            $stmt = $conn->prepare($sql);
            
            $result = $stmt->execute([
                ':modelo' => $modelo,
                ':marca' => $marca,
                ':numero_serie' => $numero_serie,
                ':localizacao' => $localizacao,
                ':status' => $status,
                ':contagem_paginas' => $contagem_paginas
            ]);
            
            if ($result) {
                ob_end_clean();
                header('Location: index.php');
                exit;
            } else {
                $erro = "Erro: Falha ao inserir dados no banco.";
            }
        } catch(Exception $e) {
            $erro = "Erro ao cadastrar: " . $e->getMessage();
        }
    }
    
    ob_end_clean();
    include 'includes/header.php';
    
} catch(Exception $e) {
    ob_end_clean();
    header('Content-Type: text/html; charset=utf-8');
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html><head><title>Erro</title></head><body>
    <h1>Erro 500</h1>
    <p><?= htmlspecialchars($e->getMessage()) ?></p>
    </body></html>
    <?php
    exit;
}
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
                    <select name="marca" class="form-select" required>
                        <option value="">Selecione uma marca...</option>
                        <option value="HP">HP</option>
                        <option value="BROTHER">BROTHER</option>
                        <option value="SAMSUNG">SAMSUNG</option>
                        <option value="OKIDATA">OKIDATA</option>
                        <option value="KYOCERA">KYOCERA</option>
                        <option value="CANON">CANON</option>
                        <option value="RICOH">RICOH</option>
                    </select>
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
                        <option value="">-- Selecione um status --</option>
                        <option value="equipamento_completo" selected>✓ Equipamento Completo</option>
                        <option value="equipamento_manutencao">⚙️ Equipamento Precisa de Manutenção</option>
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
