<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-header">
            <h4 class="mb-0"><i class="bi bi-person-plus-fill"></i> Cadastrar Novo Usuário</h4>
        </div>
        <div class="card-body">
            <form action="processa_registro.php" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nome:</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" id="is_admin" name="is_admin" class="form-check-input" value="1">
                    <label for="is_admin" class="form-check-label">Tornar Administrador</label>
                </div>
                <div class="card-footer bg-light d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Cadastrar
                    </button>
                     <a href="index.php?page=printers/dashboard" class="btn btn-secondary ms-auto">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
