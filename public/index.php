<?php
session_start();

// Define o caminho base para os arquivos da aplicação
define('BASE_PATH', dirname(__DIR__));

// ===== ROTEAMENTO BÁSICO =====
// Lista de páginas permitidas para evitar inclusão de arquivos arbitrários
$allowed_pages = [
    'auth/login' => ['path' => BASE_PATH . '/pages/auth/login.php', 'auth' => false],
    'auth/logout' => ['path' => BASE_PATH . '/pages/auth/processaLogout.php', 'auth' => true],
    'printers/dashboard' => ['path' => BASE_PATH . '/pages/printers/dashboard.php', 'auth' => true],
    'printers/inventory' => ['path' => BASE_PATH . '/pages/printers/inventory.php', 'auth' => true],
    'printers/register' => ['path' => BASE_PATH . '/pages/printers/register.php', 'auth' => true],
    'printers/edit' => ['path' => BASE_PATH . '/pages/printers/edit.php', 'auth' => true],
    'printers/details' => ['path' => BASE_PATH . '/pages/printers/details.php', 'auth' => true],
    'printers/delete' => ['path' => BASE_PATH . '/pages/printers/delete.php', 'auth' => true],
    'users/register' => ['path' => BASE_PATH . '/pages/users/register.php', 'auth' => true, 'admin' => true],
];

// Define a página padrão
$page_key = $_GET['page'] ?? 'auth/login';

// Se o usuário estiver logado, a página padrão é o dashboard
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    $page_key = $_GET['page'] ?? 'printers/dashboard';
}

// Verifica se a página solicitada é válida
if (!array_key_exists($page_key, $allowed_pages)) {
    // Pode-se criar uma página 404 dedicada
    http_response_code(404);
    echo "<h1>Erro 404 - Página não encontrada</h1>";
    exit;
}

$page = $allowed_pages[$page_key];

// ===== CONTROLE DE ACESSO =====
$is_logged_in = isset($_SESSION['logado']) && $_SESSION['logado'] === true;
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// Se a página requer autenticação e o usuário não está logado, redireciona para o login
if ($page['auth'] && !$is_logged_in) {
    $_SESSION['toast_message'] = 'Você precisa estar logado para acessar esta página.';
    $_SESSION['toast_type'] = 'danger';
    header("Location: index.php?page=auth/login");
    exit;
}

// Se a página requer admin e o usuário não é admin, redireciona para o dashboard
if (isset($page['admin']) && $page['admin'] && !$is_admin) {
    $_SESSION['toast_message'] = 'Acesso negado. Você não tem permissão para acessar esta página.';
    $_SESSION['toast_type'] = 'warning';
    header("Location: index.php?page=printers/dashboard");
    exit;
}

// Carrega as dependências de configuração (pode ser otimizado com autoloading no futuro)
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/timezone.php';


// ===== RENDERIZAÇÃO DA PÁGINA =====
// Inclui o cabeçalho
include BASE_PATH . '/includes/header.php';

// Inclui o conteúdo da página
include $page['path'];

// Inclui o rodapé
include BASE_PATH . '/includes/footer.php';
