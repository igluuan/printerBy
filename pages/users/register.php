<?php
session_start();
// Verifica se o usuário está logado e se é admin
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    $_SESSION['toast_message'] = 'Acesso negado. Apenas administradores podem cadastrar novos usuários.';
    $_SESSION['toast_type'] = 'danger';
    header("Location: /pages/printers/dashboard.php");
    exit;
}

$pageTitle = "Cadastrar Usuário";
require_once '../../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="text-center">Cadastrar Novo Usuário</h1>
    <form action="processa_registro.php" method="POST" class="mt-4">
        <div class="mb-3">
            <label for="name" class="form-label">Nome:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Senha:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" id="is_admin" name="is_admin" class="form-check-input" value="1">
            <label for="is_admin" class="form-check-label">Administrador</label>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
