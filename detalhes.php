<?php
/**
 * ╔═══════════════════════════════════════════════════════════════════════════════════╗
 * ║                    👁️ PÁGINA DE DETALHES DA IMPRESSORA                           ║
 * ║                                                                                   ║
 * ║ Arquivo: detalhes.php                                                             ║
 * ║ Descrição: Exibe informações completas de uma impressora e gerencia peças         ║
 * ║ Funcionalidades:                                                                  ║
 * ║   - Buscar impressora pelo ID                                                     ║
 * ║   - Exibir todas as informações do equipamento                                    ║
 * ║   - Adicionar peças retiradas para manutenção                                     ║
 * ║   - Exibir histórico completo de peças retiradas                                  ║
 * ║   - Botões para editar ou voltar                                                  ║
 * ║                                                                                   ║
 * ║ Parâmetros GET: ?id=X (ID da impressora)                                          ║
 * ║ Método HTTP: POST (para adicionar nova peça)                                      ║
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

    // Obter ID da impressora a visualizar
    $id = $_GET['id'] ?? 0;

    // ═══════════════════════════════════════════════════════════════════════════════
    // PROCESSAR ADIÇÃO DE PEÇA (POST)
    // ═══════════════════════════════════════════════════════════════════════════════

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Inserir nova peça na tabela pecas_retiradas
        $sql = "INSERT INTO pecas_retiradas (impressora_id, nome_peca, quantidade, data_retirada, observacao) 
                VALUES (:impressora_id, :nome_peca, :quantidade, :data_retirada, :observacao)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':impressora_id' => $id,
            ':nome_peca' => $_POST['nome_peca'],
            ':quantidade' => $_POST['quantidade'],
            ':data_retirada' => $_POST['data_retirada'],
            ':observacao' => $_POST['observacao']
        ]);
        
        // Redirecionar para recarregar a página com novo dado
        ob_end_clean();
        header("Location: detalhes.php?id=$id");
        exit;
    }

    // ═══════════════════════════════════════════════════════════════════════════════
    // BUSCAR DADOS DA IMPRESSORA
    // ═══════════════════════════════════════════════════════════════════════════════

    // Buscar impressora por ID
    $stmt = $conn->prepare("SELECT * FROM impressoras WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $impressora = $stmt->fetch();

    // Se não encontrar, redirecionar
    if (!$impressora) {
        ob_end_clean();
        header('Location: index.php');
        exit;
    }

    // ═══════════════════════════════════════════════════════════════════════════════
    // BUSCAR HISTÓRICO DE PEÇAS
    // ═══════════════════════════════════════════════════════════════════════════════

    // Buscar todas as peças retiradas desta impressora
    $stmt = $conn->prepare("SELECT * FROM pecas_retiradas WHERE impressora_id = :id ORDER BY data_retirada DESC");
    $stmt->execute([':id' => $id]);
    $pecas = $stmt->fetchAll();

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
     LAYOUT RESPONSIVO: 2 COLUNAS EM DESKTOP, 1 COLUNA EM MOBILE
     ═══════════════════════════════════════════════════════════════════════════════════ -->

<div class="row">
    <!-- COLUNA ESQUERDA: INFORMAÇÕES GERAIS -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 style="font-size: 1rem; margin: 0;">📋 Informações</h5>
            </div>
            <div class="card-body">
                <!-- Grid com informações principais -->
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
                    <div>
                        <strong style="display: block; font-size: 0.85rem; color: #666;">Status</strong>
                        <span class="badge bg-<?= in_array($impressora['status'], ['equipamento_completo', 'ativo']) ? 'success' : (in_array($impressora['status'], ['equipamento_manutencao', 'manutencao']) ? 'warning' : 'secondary') ?>" style="margin-top: 0.25rem;">
                            <?= in_array($impressora['status'], ['equipamento_completo', 'ativo']) ? 'Equipamento Completo' : (in_array($impressora['status'], ['equipamento_manutencao', 'manutencao']) ? 'Equipamento Precisa de Manutenção' : 'Inativo') ?>
                        </span>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.85rem; color: #666;">Páginas Impressas</strong>
                        <span><?= number_format($impressora['contagem_paginas'], 0, ',', '.') ?></span>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.85rem; color: #666;">Cadastrado em</strong>
                        <span><?= formatarDataHora($impressora['data_cadastro']) ?></span>
                    </div>
                </div>
                
                <!-- Botões de Ação -->
                <div class="button-group" style="margin-top: 1rem;">
                    <a href="editar.php?id=<?= $impressora['id'] ?>" class="btn btn-warning">✏️ Editar</a>
                    <a href="deletar.php?id=<?= $impressora['id'] ?>" class="btn btn-danger btn-danger flex-grow-1" style="font-size: 0.8rem; padding: 0.3rem 0.4rem;" title="Excluir" onclick="return confirm('Confirma exclusão?')">🗑️ Excluir</a>
                    <a href="index.php" class="btn btn-secondary">←  Voltar</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- COLUNA DIREITA: FORMULÁRIO PARA ADICIONAR PEÇA -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 style="font-size: 1rem; margin: 0;">➕ Adicionar Peça</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <!-- Nome da Peça -->
                    <div class="mb-3">
                        <label class="form-label">Nome da Peça *</label>
                        <input type="text" name="nome_peca" class="form-control" placeholder="Ex: Unidade de Fusor" required>
                        <small class="text-muted">Identificação clara da peça retirada</small>
                    </div>
                    
                    <!-- Quantidade -->
                    <div class="mb-3">
                        <label class="form-label">Quantidade *</label>
                        <input type="number" name="quantidade" class="form-control" value="1" required>
                    </div>
                    
                    <!-- Data de Retirada -->
                    <div class="mb-3">
                        <label class="form-label">Data Retirada *</label>
                        <input type="date" name="data_retirada" class="form-control" value="<?= dataHoje() ?>" required>
                    </div>
                    
                    <!-- Observações -->
                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea name="observacao" class="form-control" placeholder="Ex: Peça retirada para reparo" style="min-height: 80px;"></textarea>
                    </div>
                    
                    <!-- Botão de Submissão -->
                    <button type="submit" class="btn btn-primary w-100">✓ Adicionar Peça</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════════════
     HISTÓRICO DE PEÇAS RETIRADAS
     ═══════════════════════════════════════════════════════════════════════════════════ -->

<div class="card">
    <div class="card-header">
        <h5 style="font-size: 1rem; margin: 0;">📦 Histórico de Peças Retiradas</h5>
    </div>
    <div class="card-body">
        <?php if (count($pecas) > 0): ?>
            <!-- Tabela responsiva com scroll em mobile -->
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 40%;">Peça</th>
                            <th style="width: 20%;">Qtd</th>
                            <th style="width: 20%;">Data</th>
                            <th style="width: 20%;" class="d-none d-md-table-cell">Obs.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pecas as $peca): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($peca['nome_peca']) ?></strong></td>
                            <td><span class="badge bg-info"><?= $peca['quantidade'] ?></span></td>
                            <td><small><?= formatarData($peca['data_retirada']) ?></small></td>
                            <td class="d-none d-md-table-cell">
                                <small class="text-muted" style="display: block; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($peca['observacao']) ?>
                                </small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Mensagem quando não há peças -->
            <p class="text-muted text-center" style="padding: 1rem;">📦 Nenhuma peça retirada registrada</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
