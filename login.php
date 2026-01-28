<?php
session_start(); // Start session at the very beginning

include 'includes/header.php';
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6">
            <?php
            if (isset($_SESSION['login_error'])) {
                echo '<div class="alert alert-danger text-center mb-3">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                unset($_SESSION['login_error']); // Clear the error after displaying
            }
            ?>
            <h2 class="mb-4 text-center">Faça login</h2>
            <form method="post">
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
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button type="submit" class="btn btn-primary">Login</button>
                    <a href="remember-password.php" class="text-decoration-none">Esqueceu a senha?</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>