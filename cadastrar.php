<?php
session_start();
require_once 'config/database.php';
require_once 'config/timezone.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->connect();
    
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
    
    try {
        $result = $stmt->execute([
            ':modelo' => $modelo,
            ':marca' => $marca,
            ':numero_serie' => $numero_serie,
            ':localizacao' => $localizacao,
            ':status' => $status,
            ':contagem_paginas' => $contagem_paginas
        ]);
        
        if ($result) {
            $_SESSION['debug_logs'] = $logs;
            header('Location: index.php');
            exit;
        } else {
            $erro = "Erro: Falha ao inserir dados no banco.";
            $logs[] = "Falha ao inserir: Nenhuma exceção mas execute retornou false";
        }
    } catch(PDOException $e) {
        $erro = "Erro ao cadastrar: " . $e->getMessage();
    }
    
    // Guardar logs na sessão para exibir
    $_SESSION['debug_logs'] = $logs;
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
