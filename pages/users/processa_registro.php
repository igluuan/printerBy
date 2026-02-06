<?php
session_start();
require_once '../../config/database.php';

// Verifica se o usuário está logado e se é admin
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    $_SESSION['toast_message'] = 'Acesso negado.';
    $_SESSION['toast_type'] = 'danger';
    header("Location: /pages/printers/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['toast_message'] = 'Por favor, preencha todos os campos obrigatórios.';
        $_SESSION['toast_type'] = 'danger';
        header("Location: register.php");
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $isAdmin]);

        $_SESSION['toast_message'] = 'Usuário cadastrado com sucesso!';
        $_SESSION['toast_type'] = 'success';
        header("Location: /pages/printers/inventory.php");
        exit;
    } catch (PDOException $e) {
        // Em um ambiente de produção, logue o erro em vez de exibi-lo
        error_log($e->getMessage());
        $_SESSION['toast_message'] = 'Erro ao cadastrar usuário. Por favor, tente novamente.';
        $_SESSION['toast_type'] = 'danger';
        header("Location: register.php");
        exit;
    }
}
