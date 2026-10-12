<?php
session_start();
include '../config/conexion.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
$stmt = $conexion->prepare("SELECT * FROM incidencias WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$incidencia = $stmt->get_result()->fetch_assoc();

if (!$incidencia) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre_reporta']);
    $lab = trim($_POST['laboratorio']);
    $equipo = trim($_POST['equipo_afectado']);
    $desc = trim($_POST['descripcion']);
    $prioridad = $_POST['prioridad'];
    $estado = $_POST['estado'];

    $update = $conexion->prepare("UPDATE incidencias SET nombre_reporta=?, laboratorio=?, equipo_afectado=?, descripcion=?, prioridad=?, estado=? WHERE id=?");
    $update->bind_param("ssssssi", $nombre, $lab, $equipo, $desc, $prioridad, $estado, $id);
    $update->execute();
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Incidencia - Panel Admin</title>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados (Modo Enfoque / Azul Oscuro) -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center py-4" style="min-height: 100vh;">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white py-3">
                <i class="bi bi-pencil-square me-2"></i> Editar Incidencia #<?= $incidencia['id'] ?>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person-fill me-1"></i> Nombre del reportante</label>
                        <input type="text" name="nombre_reporta" class="form-control" value="<?= htmlspecialchars($incidencia['nombre_reporta']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-door-open-fill me-1"></i> Laboratorio</label>
                        <input type="text" name="laboratorio" class="form-control" value="<?= htmlspecialchars($incidencia['laboratorio']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-pc-display me-1"></i> Equipo Afectado</label>
                        <input type="text" name="equipo_afectado" class="form-control" value="<?= htmlspecialchars($incidencia['equipo_afectado']) ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-flag-fill me-1"></i> Prioridad</label>
                            <select name="prioridad" class="form-select">
                                <option value="Baja" <?= $incidencia['prioridad']=='Baja'?'selected':'' ?>>Baja</option>
                                <option value="Media" <?= $incidencia['prioridad']=='Media'?'selected':'' ?>>Media</option>
                                <option value="Alta" <?= $incidencia['prioridad']=='Alta'?'selected':'' ?>>Alta</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-check2-circle me-1"></i> Estado</label>
                            <select name="estado" class="form-select">
                                <option value="Pendiente" <?= $incidencia['estado']=='Pendiente'?'selected':'' ?>>Pendiente</option>
                                <option value="En proceso" <?= $incidencia['estado']=='En proceso'?'selected':'' ?>>En proceso</option>
                                <option value="Resuelta" <?= $incidencia['estado']=='Resuelta'?'selected':'' ?>>Resuelta</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-card-text me-1"></i> Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" required><?= htmlspecialchars($incidencia['descripcion']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2 mb-2">
                        <i class="bi bi-check-lg me-1"></i> Guardar Cambios
                    </button>
                    <a href="dashboard.php" class="btn btn-outline-secondary w-100 btn-sm">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>