<?php
/**
 * ╔═══════════════════════════════════════════════════════════════════════════════════╗
 * ║                      ✏️ EDIÇÃO DE IMPRESSORA                                      ║
 * ║                                                                                   ║
 * ║ Arquivo: editar.php                                                               ║
 * ║ Descrição: Página para atualizar informações de uma impressora existente          ║
 * ║ Funcionalidades:                                                                  ║
 * ║   - Buscar impressora pelo ID via parâmetro GET                                   ║
 * ║   - Pré-popular formulário com dados atuais                                       ║
 * ║   - Validar e sanitizar alterações via POST                                       ║
 * ║   - Atualizar registro no banco de dados                                          ║
 * ║   - Redirecionar para detalhes após sucesso                                       ║
 * ║   - Verificar existência antes de proceder                                        ║
 * ║                                                                                   ║
 * ║ Parâmetros GET: ?id=X (ID da impressora a editar)                                 ║
 * ║ Método HTTP: POST (para salvar alterações)                                        ║
 * ║ Autor: Sistema de Gerenciamento                                                   ║
 * ║ Data: 26/01/2026                                                                  ║
 * ╚═══════════════════════════════════════════════════════════════════════════════════╝
 */

// ═══════════════════════════════════════════════════════════════════════════════════
// INICIALIZAÇÃO
// ═══════════════════════════════════════════════════════════════════════════════════

ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Carregar configurações
    require_once 'config/database.php';
    require_once 'config/timezone.php';

    // Conectar ao banco
    $conn = Database::getInstance();

    // ═══════════════════════════════════════════════════════════════════════════════
    // VALIDAR PARÂMETRO GET E BUSCAR IMPRESSORA
    // ═══════════════════════════════════════════════════════════════════════════════

    // Obter ID da impressora a editar (parâmetro GET)
    $id = $_GET['id'] ?? 0;

    // Buscar impressora no banco
    $stmt = $conn->prepare("SELECT * FROM impressoras WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $impressora = $stmt->fetch();

    // Se não encontrar, redirecionar para listagem
    if (!$impressora) {
        ob_end_clean();
        header('Location: index.php');
        exit;
    }

    // ═══════════════════════════════════════════════════════════════════════════════
    // PROCESSAR FORMULÁRIO (POST)
    // ═══════════════════════════════════════════════════════════════════════════════

    // Variável para armazenar erros
    $erro = null;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Preparar statement UPDATE
        $sql = "UPDATE impressoras SET modelo = :modelo, marca = :marca, numero_serie = :numero_serie, 
                localizacao = :localizacao, status = :status, contagem_paginas = :contagem_paginas 
                WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        
        try {
            // Executar UPDATE com dados sanitizados
            $stmt->execute([
                ':modelo' => $_POST['modelo'],
                ':marca' => $_POST['marca'],
                ':numero_serie' => $_POST['numero_serie'],
                ':localizacao' => $_POST['localizacao'],
                ':status' => $_POST['status'],
                ':contagem_paginas' => $_POST['contagem_paginas'],
                ':id' => $id
            ]);
            
            // Redirecionar para página de detalhes após sucesso
            ob_end_clean();
            header('Location: detalhes.php?id=' . $id);
            exit;
        } catch(Exception $e) {
            // Capturar erro e exibir mensagem
            $erro = "Erro ao atualizar: " . $e->getMessage();
        }
    }

    // Limpar buffer e incluir template
    ob_end_clean();
    include 'includes/header.php';
    
} catch(Exception $e) {
    // Erro crítico
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

<!-- ═══════════════════════════════════════════════════════════════════════════════════
     EXIBIR MENSAGEM DE ERRO (se houver)
     ═══════════════════════════════════════════════════════════════════════════════════ -->
<?php if(isset($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════════════════════════
     FORMULÁRIO DE EDIÇÃO
     ═══════════════════════════════════════════════════════════════════════════════════ -->

<div class="card">
    <div class="card-header">
        <h4 style="font-size: clamp(1rem, 2vw, 1.25rem);">✏️ Editar Impressora</h4>
    </div>
    <div class="card-body">
        <form method="POST">
            <!-- LINHA 1: Modelo e Marca -->
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
            
            <!-- LINHA 2: Número de Série e Localização -->
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
            
            <!-- LINHA 3: Status e Contagem de Páginas -->
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
            
            <!-- BOTÕES DE AÇÃO -->
            <div class="button-group">
                <button type="submit" class="btn btn-success">✓ Salvar</button>
                <a href="detalhes.php?id=<?= $id ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
