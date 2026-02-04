<!DOCTYPE html>
<html lang="pt-BR" id="theme-switcher">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Gerenciador de Impressoras - Sistema para controle de estoque">
    <meta name="theme-color" content="#212529">
    <title>Inventário de Impressoras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../public/assets/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../public/assets/style.css">
    <style>
        /* Otimizações adicionais para touch/mobile */
        @media (max-width: 576px) {
            button, a.btn {
                -webkit-tap-highlight-color: transparent;
                -webkit-touch-callout: none;
            }
            
            input, select, textarea {
                font-size: 16px !important; /* Evita zoom em inputs */
            }
        }
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1050;
        }
    </style>
</head>
<body>
<div class="toast-container">
    <?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['toast_message'])) {
        $toastType = $_SESSION['toast_type'] ?? 'info'; // Default to info if not set
        $toastMessage = htmlspecialchars($_SESSION['toast_message']);
        echo "
        <div class='toast align-items-center text-white bg-{$toastType} border-0' role='alert' aria-live='assertive' aria-atomic='true' data-bs-delay='3000'>
            <div class='d-flex'>
                <div class='toast-body'>
                    {$toastMessage}
                </div>
                <button type='button' class='btn-close btn-close-white me-2 m-auto' data-bs-dismiss='toast' aria-label='Close'></button>
            </div>
        </div>";
        unset($_SESSION['toast_message']);
        unset($_SESSION['toast_type']);
    }
    ?>
</div>
<nav class="navbar navbar-dark bg-dark mb-3 mb-md-4">
    <div class="container-fluid px-2 px-sm-3">
        <a href="../pages/dashboard.php" class="navbar-brand mb-0 h1">🖨️ Inventário de impressoras</a>
        <button id="dark-mode-toggle" class="btn btn-dark ms-auto" aria-label="Toggle dark mode">
            <i class="bi bi-moon-fill"></i>
        </button>
        <?php if (isset($_SESSION['user_name']) && empty($hideLogout)): ?>
            <button class="btn btn-primary ms-2">
                <a href="../auth/processaLogout.php" class="text-white text-decoration-none">Sair <i class="bi bi-box-arrow-right"></i></a>
            </button>
        <?php endif; ?>
    </div>
</nav>
<main class="container-fluid" style="padding-top: 0;">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl)
        })
        toastList.forEach(toast => toast.show())
    });
</script>