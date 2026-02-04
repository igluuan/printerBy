<?php
session_start();
require '../../config/database.php';

$pdo = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    $_SESSION['toast_message'] = 'Acesso inválido.';
    $_SESSION['toast_type'] = 'danger';
    header("Location: ../../public/index.php");
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

if (empty($email)|| empty($password)){
    $_SESSION['toast_message'] = 'Por favor, preencha todos os campos.';
    $_SESSION['toast_type'] = 'danger';
    header("Location: ../../public/index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * from users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])){
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['logado'] = true;
    header("Location: /pages/printers/inventory.php");
    exit;
} else{
    $_SESSION['toast_message'] = 'Email ou senha incorretos.';
    $_SESSION['toast_type'] = 'danger';
    header("Location: ../../public/index.php");
    exit;
}