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
    <link rel="shortcut icon" href="/assets/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/assets/style.css">
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
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-3 mb-md-4">
    <div class="container-fluid px-2 px-sm-3">
        <a href="index.php" class="navbar-brand mb-0 h1">🖨️ Inventário de impressoras</a>
        <button id="dark-mode-toggle" class="btn btn-dark ms-auto" aria-label="Toggle dark mode">
            <i class="bi bi-moon-fill"></i>
        </button>
    </div>
</nav>
<main class="container-fluid" style="padding-top: 0;">