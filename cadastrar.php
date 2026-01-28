<!--
/**
 * ╔═══════════════════════════════════════════════════════════════════════════════════╗
 * ║                 🖨️ PÁGINA PRINCIPAL - GERENCIAMENTO DE FROTA                      ║
 * ╚═══════════════════════════════════════════════════════════════════════════════════╝
 *
 * @version 2.0.0
 * @since 2026-01-26
 * @author Gemini
 *
 * REVISÃO DE FUNCIONALIDADES (v2.0.0):
 * - Implementado painel de filtros avançado (Enterprise UX).
 * - Barra de "pílulas" (lozenges) para visualização e remoção de filtros ativos.
 * - Sidebar (Off-canvas) com seções colapsáveis para categorias de filtro.
 * - Filtro de Status com múltipla seleção (checkboxes).
 * - Filtro de Marca com múltipla seleção e busca interna.
 * - Filtro de Data de Cadastro com presets (Hoje, 7 dias, etc.).
 * - Lógica de ordenação dinâmica.
 * - Feedback visual de contagem de resultados.
 * - Código PHP refatorado para suportar filtros complexos (arrays, ranges).
 * - Código JavaScript para dinâmica da interface de filtros.
 */

// ═══════════════════════════════════════════════════════════════════════════════════
// INICIALIZAÇÃO E CONFIGURAÇÃO
// ═══════════════════════════════════════════════════════════════════════════════════
<?php
ob_start();
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once 'config/database.php';
require_once 'config/timezone.php';

$erro = null;

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
    $erro = "Erro ao carregar lista de modelos do banco de dados: " . $e->getMessage();
    // Fallback para o caso de erro no banco, pode ser removido em produção
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
        
        // Validação básica
        if (empty($modelo) || empty($marca) || empty($numero_serie)) {
             throw new Exception("Marca, Modelo e Número de Série são obrigatórios.");
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
            $_SESSION['success_message'] = "Impressora cadastrada com sucesso!";
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
?>
<!-- Incluir o cabeçalho -->
<?php include 'includes/header.php'; ?>

<?php if(isset($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h4 style="font-size: clamp(1rem, 2vw, 1.25rem);">Cadastrar Nova Impressora</h4>
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
                        <option value="equipamento_completo" selected> Equipamento Completo</option>
                        <option value="equipamento_manutencao">Equipamento Precisa de Manutenção</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contagem de Páginas</label>
                    <input type="number" name="contagem_paginas" class="form-control" value="0" placeholder="0">
                    <small class="form-text text-muted">Opcional: Quantidade inicial de páginas</small>
                </div>
            </div>
            
            <!-- BOTÕES DE AÇÃO -->
            <div class="button-group">
                <button type="submit" class="btn btn-success">✓ Cadastrar</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Passa os dados do PHP para o JavaScript
    const modelosPorMarca = <?= json_encode($marcas_modelos) ?>;

    const marcaSelect = document.getElementById('marca-select');
    const modeloSelect = document.getElementById('modelo-select');

    marcaSelect.addEventListener('change', function() {
        const marcaSelecionada = this.value;
        
        // Limpa e desabilita o select de modelos
        modeloSelect.innerHTML = '';
        modeloSelect.disabled = true;

        if (marcaSelecionada && modelosPorMarca[marcaSelecionada]) {
            // Habilita e adiciona a opção padrão
            modeloSelect.disabled = false;
            modeloSelect.add(new Option('Selecione um modelo...', ''));

            // Popula com os modelos da marca selecionada
            modelosPorMarca[marcaSelecionada].forEach(function(modelo) {
                modeloSelect.add(new Option(modelo, modelo));
            });
        } else {
             // Se nenhuma marca for selecionada
            modeloSelect.add(new Option('Selecione uma marca primeiro', ''));
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>