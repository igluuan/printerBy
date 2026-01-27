<?php
/**
 * ╔═══════════════════════════════════════════════════════════════════════════════════╗
 * ║                     🗑️ PÁGINA DE CONFIRMAÇÃO DE EXCLUSÃO                         ║
 * ║                                                                                   ║
 * ║ Arquivo: deletar.php                                                              ║
 * ║ Descrição: Exibe página de confirmação antes de deletar uma impressora            ║
 * ║ Funcionalidades:                                                                  ║
 * ║   - Buscar impressora pelo ID                                                     ║
 * ║   - Exibir dados a serem deletados para confirmação                               ║
 * ║   - Avisar sobre exclusão de peças relacionadas (CASCADE)                          ║
 * ║   - Requer confirmação do usuário antes de proceder                               ║
 * ║   - Deletar impressora e dados relacionados do banco                              ║
 * ║   - Redirecionar para listagem após sucesso                                       ║
 * ║                                                                                   ║
 * ║ Parâmetros GET: ?id=X (ID da impressora a deletar)                                ║
 * ║ Método HTTP: POST (para confirmar e executar exclusão)                            ║
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
    // VALIDAR PARÂMETRO E BUSCAR IMPRESSORA
    // ═══════════════════════════════════════════════════════════════════════════════

    // Obter ID da impressora a deletar
    $id = $_GET['id'] ?? 0;

    // Buscar impressora para confirmar existência e exibir dados
    $stmt = $conn->prepare("SELECT * FROM impressoras WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $impressora = $stmt->fetch();

    // Se não encontrar, redirecionar para segurança
    if (!$impressora) {
        ob_end_clean();
        header('Location: index.php');
        exit;
    }

    // ═══════════════════════════════════════════════════════════════════════════════
    // PROCESSAR EXCLUSÃO (POST)
    // ═══════════════════════════════════════════════════════════════════════════════

    // Variável para armazenar erros
    $erro = null;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Preparar e executar DELETE
            // A restrição CASCADE apagará automaticamente as peças relacionadas
            $sql = "DELETE FROM impressoras WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            // Redirecionar para listagem com mensagem de sucesso
            ob_end_clean();
            header('Location: index.php?msg=Impressora deletada com sucesso');
            exit;
        } catch(Exception $e) {
            // Capturar erro de exclusão
            $erro = "Erro ao deletar: " . $e->getMessage();
        }
    }

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
     CARD DE CONFIRMAÇÃO COM DESIGN DE ALERTA
     ═══════════════════════════════════════════════════════════════════════════════════ -->

<div class="card border-danger">
    <div class="card-header bg-danger">
        <h4 class="text-white" style="font-size: clamp(1rem, 2vw, 1.25rem); margin: 0;">⚠️ Confirmação de Exclusão</h4>
    </div>
    <div class="card-body">
        <!-- AVISO PRINCIPAL -->
        <div class="alert alert-warning">
            <strong>⚠️ Atenção!</strong> Esta ação é <strong>irreversível</strong>. Você está deletando:
        </div>
        
        <!-- EXIBIR DADOS A SEREM DELETADOS -->
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

        <!-- AVISO SOBRE CASCATA (deletar peças associadas) -->
        <div class="alert alert-info">
            <strong>ℹ️ Nota:</strong> Todas as peças associadas também serão deletadas.
        </div>
        
        <!-- BOTÕES DE AÇÃO -->
        <div class="button-group" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <!-- Botão DELETE (POST form) -->
            <form method="POST" style="flex: 1; min-width: 120px;">
                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Tem certeza que deseja deletar?')">🗑️ Deletar</button>
            </form>
            <!-- Botão CANCELAR -->
            <a href="detalhes.php?id=<?= $id ?>" class="btn btn-secondary" style="flex: 1; min-width: 120px;">← Cancelar</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
