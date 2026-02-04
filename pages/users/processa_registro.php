<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($name) || empty($email) || empty($password)) {
        session_start();
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

        header("Location: ../../public/index.php?status=user_created");
        exit;
    } catch (PDOException $e) {
        // Em um ambiente de produção, logue o erro em vez de exibi-lo
        error_log($e->getMessage());
        session_start();
        $_SESSION['toast_message'] = 'Erro ao cadastrar usuário. Por favor, tente novamente.';
        $_SESSION['toast_type'] = 'danger';
        header("Location: register.php");
        exit;
    }
}
