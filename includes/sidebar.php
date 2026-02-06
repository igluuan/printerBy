<div id="sidebar">
    <div class="sidebar-header">
        <h3>Menu</h3>
    </div>
    <ul class="list-unstyled components">
        <li>
            <a href="/pages/printers/dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="/pages/printers/inventory.php">
                <i class="bi bi-list-ul"></i> Ver Todos Equipamentos
            </a>
        </li>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
        <li>
            <a href="/pages/users/register.php">
                <i class="bi bi-person-plus"></i> Cadastrar Usuário
            </a>
        </li>
        <?php endif; ?>
        <!-- Adicione mais opções de menu aqui -->
    </ul>
    <div class="sidebar-footer">
        <?php if (isset($_SESSION['user_name'])): ?>
            <a href="/pages/auth/processaLogout.php" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
        <?php endif; ?>
    </div>
</div>
