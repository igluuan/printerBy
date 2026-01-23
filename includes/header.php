<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Gerenciador de Impressoras - Sistema para controle de estoque">
    <meta name="theme-color" content="#212529">
    <title>Gerenciador de Impressoras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
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
        <a href="index.php" class="navbar-brand mb-0 h1">🖨️ Controle de impressoras</a>
        <a href="cadastrar.php" class="btn btn-success btn-sm" style="white-space: nowrap;">
            <span class="d-none d-sm-inline">Novo equipamento</span>
            <span class="d-sm-none">+</span>
        </a>
    </div>
</nav>
<main class="container-fluid" style="padding-top: 0;">

