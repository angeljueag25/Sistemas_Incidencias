<?php
session_start();
include 'config/conexion.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['usuario']);
    $pass = trim($_POST['password']);

    $stmt = $conexion->prepare("SELECT id, password FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {
        if (password_verify($pass, $row['password'])) {
            $_SESSION['admin'] = $user;
            header("Location: admin/dashboard.php");
            exit();
        }
    }
    $error = "Usuario o contraseña incorrectos.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Administrador - Control de Incidencias</title>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados (Modo Enfoque / Azul Oscuro) -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center py-4" style="height: 100vh;">
    <div class="container" style="max-width: 420px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center py-3">
                <i class="bi bi-shield-lock-fill fs-3 d-block mb-1"></i>
                <h4 class="mb-0 fs-5">Acceso Administrativo</h4>
            </div>
            <div class="card-body p-4">
                <?php if($error): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?= $error ?></div>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person-fill me-1"></i> Usuario</label>
                        <input type="text" name="usuario" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-key-fill me-1"></i> Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar al Panel
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary w-100 btn-sm mt-2">
                        <i class="bi bi-arrow-left me-1"></i> Volver al sitio público
                    </a>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>