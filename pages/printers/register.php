<?php
ob_start();

$marcas_modelos = [];
try {
    $conn = Database::getInstance();

    // Fetch brands from the 'marcas' table
    $stmt_marcas = $conn->query("SELECT nome_marca FROM marcas ORDER BY nome_marca ASC");
    $marcas_db = $stmt_marcas->fetchAll(PDO::FETCH_COLUMN);

    foreach ($marcas_db as $marca_nome) {
        $marcas_modelos[strtoupper($marca_nome)] = [];
        $stmt_modelos = $conn->prepare("SELECT modelo FROM modelos_conhecidos WHERE marca = :marca ORDER BY modelo ASC");
        $stmt_modelos->execute([':marca' => $marca_nome]);
        $modelos_db = $stmt_modelos->fetchAll(PDO::FETCH_COLUMN);
        $marcas_modelos[strtoupper($marca_nome)] = $modelos_db;
    }
    
} catch (Exception $e) {
    $_SESSION['toast_message'] = "Erro ao carregar lista de modelos: " . $e->getMessage();
    $_SESSION['toast_type'] = 'danger';
    $marcas_modelos = [
        'HP' => [], 'BROTHER' => [], 'SAMSUNG' => [], 'OKIDATA' => [], 
        'KYOCERA' => [], 'CANON' => [], 'RICOH' => [], 'XEROX' => []
    ];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn = Database::getInstance();
        
        $modelo = trim($_POST['modelo'] ?? '');
        $marca = trim($_POST['marca'] ?? '');
        $numero_serie = trim($_POST['numero_serie'] ?? '');
        $localizacao = trim($_POST['localizacao'] ?? '');
        $status = trim($_POST['status'] ?? 'equipamento_completo');
        $contagem_paginas = isset($_POST['contagem_paginas']) ? (int)$_POST['contagem_paginas'] : 0;
        
        if (empty($modelo) || empty($marca) || empty($numero_serie)) {
             $_SESSION['toast_message'] = "Marca, Modelo e Número de Série são obrigatórios.";
             $_SESSION['toast_type'] = 'danger';
             ob_end_clean();
             header('Location: index.php?page=printers/register');
             exit;
        }

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
            $_SESSION['toast_message'] = "Impressora cadastrada com sucesso!";
            $_SESSION['toast_type'] = 'success';
            ob_end_clean();
            header('Location: index.php?page=printers/inventory');
            exit;
        } else {
            $_SESSION['toast_message'] = "Erro: Falha ao inserir dados no banco.";
            $_SESSION['toast_type'] = 'danger';
            ob_end_clean();
            header('Location: index.php?page=printers/register');
            exit;
        }
    } catch(Exception $e) {
        $_SESSION['toast_message'] = "Erro ao cadastrar: " . $e->getMessage();
        $_SESSION['toast_type'] = 'danger';
        ob_end_clean();
        header('Location: index.php?page=printers/register');
        exit;
    }
}
ob_end_clean();
?>

<div class="card">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-printer-fill"></i> Cadastrar Nova Impressora</h4>
    </div>
    <div class="card-body">
        <form method="POST">
            <!-- LINHA 1: Marca e Modelo -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="marca-select" class="form-label">Marca *</label>
                    <select id="marca-select" name="marca" class="form-select" required>
                        <option value="">Selecione uma marca...</option>
                        <?php foreach (array_keys($marcas_modelos) as $marca_option): ?>
                            <option value="<?= htmlspecialchars($marca_option) ?>"><?= htmlspecialchars($marca_option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="modelo-select" class="form-label">Modelo *</label>
                    <select id="modelo-select" name="modelo" class="form-select" required disabled>
                        <option value="">Selecione uma marca primeiro</option>
                    </select>
                    <small class="form-text text-muted">O modelo da impressora.</small>
                </div>
            </div>
            
            <!-- LINHA 2: Número de Série e Localização -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Número de Série *</label>
                    <input type="text" name="numero_serie" class="form-control" placeholder="Ex: SN001HP2024" required>
                    <small class="form-text text-muted">Identificador único do equipamento</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Localização</label>
                    <input type="text" name="localizacao" class="form-control" placeholder="Ex: Sala 101">
                    <small class="form-text text-muted">Opcional: Prédio, andar ou setor</small>
                </div>
            </div>
            
            <!-- LINHA 3: Status e Contagem de Páginas -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="">-- Selecione um status --</option>
                        <option value="equipamento_completo" selected>✓ Equipamento Completo</option>
                        <option value="equipamento_manutencao">⚙️ Requer Manutenção</option>
                        <option value="inativo">✗ Inativo</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contagem de Páginas</label>
                    <input type="number" name="contagem_paginas" class="form-control" value="0" placeholder="0" min="0">
                </div>
            </div>
            
            <div class="card-footer bg-light d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg"></i> Cadastrar
                </button>
                <a href="index.php?page=printers/inventory" class="btn btn-secondary ms-auto">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modelosPorMarca = <?= json_encode($marcas_modelos) ?>;

    const marcaSelect = document.getElementById('marca-select');
    const modeloSelect = document.getElementById('modelo-select');

    marcaSelect.addEventListener('change', function() {
        const marcaSelecionada = this.value.toUpperCase();
        
        modeloSelect.innerHTML = '';
        modeloSelect.disabled = true;

        if (marcaSelecionada && modelosPorMarca[marcaSelecionada]) {
            modeloSelect.disabled = false;
            modeloSelect.add(new Option('Selecione um modelo...', ''));

            modelosPorMarca[marcaSelecionada].forEach(function(modelo) {
                modeloSelect.add(new Option(modelo, modelo));
            });
        } else {
            modeloSelect.add(new Option('Selecione uma marca primeiro', ''));
        }
    });
});
</script>