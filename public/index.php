<?php
session_start(); // Start session at the very beginning

$hideLogout = true;

include '../includes/header.php';
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6">
            <?php
            if (isset($_SESSION['login_error'])) {
                $_SESSION['toast_message'] = $_SESSION['login_error'];
                $_SESSION['toast_type'] = 'danger';
                unset($_SESSION['login_error']); // Clear the error after displaying
            }
            ?>
            <h2 class="mb-4 text-center">Faça login</h2>
            <form action="../pages/auth/processaLogin.php" method="post">
                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="input-group-text" id="togglePassword">
                            <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button type="submit" class="btn btn-primary">Login</button>
                    <div>
                        <a href="remember-password.php" class="text-decoration-none">Esqueceu a senha?</a>
                        <span class="mx-2">|</span>
                        <a href="../pages/users/register.php" class="text-decoration-none">Cadastrar-se</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye / eye slash icon
            if (type === 'password') {
                togglePasswordIcon.classList.remove('bi-eye');
                togglePasswordIcon.classList.add('bi-eye-slash');
            } else {
                togglePasswordIcon.classList.remove('bi-eye-slash');
                togglePasswordIcon.classList.add('bi-eye');
            }
        });
    });
</script>
<?php include '../includes/footer.php'; ?>