<?php include 'includes/header.php'; ?>
<form method="post">
    <div class="container w-25">
        <h2 style="margin-bottom: 20px; text-align: center;">Faça login</h2>
        <div class="form-group">
            <label for="email">Email:</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
    </div>

      <div class="form-group mt-3">
        <label for="password">Password:</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>

      <div class="form-group mt-3" style="display: flex; justify-content: space-between; align-items: center;">
          <button type="submit" class="btn btn-primary">Login</button>
          <a href="remember-password.php" class="">Esqueceu a senha?</a>
      </div>
     </div>
 </div>
</form>
<?php include 'includes/footer.php'; ?>