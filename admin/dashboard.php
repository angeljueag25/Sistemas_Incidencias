<?php
session_start();
include '../config/conexion.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

// Eliminar incidencia
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conexion->prepare("DELETE FROM incidencias WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

// Cambiar estado rápido
if (isset($_GET['cambiar_estado']) && isset($_GET['estado'])) {
    $id = intval($_GET['cambiar_estado']);
    $nuevo_estado = $_GET['estado'];
    $stmt = $conexion->prepare("UPDATE incidencias SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_estado, $id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

$resultado = $conexion->query("SELECT * FROM incidencias ORDER BY fecha_registro DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Control de Incidencias</title>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados (Modo Enfoque / Azul Oscuro) -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <script>
        function confirmarEliminacion(id) {
            if (confirm("¿Estás seguro de que deseas eliminar esta incidencia?")) {
                window.location.href = "dashboard.php?eliminar=" + id;
            }
        }
    </script>
</head>
<body>
    <!-- Barra de Navegación Superior -->
    <nav class="navbar navbar-expand-lg bg-primary mb-4" data-bs-theme="dark">
        <div class="container">
            <span class="navbar-brand mb-0 h1"><i class="bi bi-speedometer2 me-2"></i>Panel Admin - Soporte Técnico</span>
            <div class="d-flex">
                <a href="../index.php" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-house-door-fill me-1"></i> Ver Sitio Público</a>
                <a href="../logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-list-check me-2"></i> Gestión Avanzada de Incidencias</span>
                <span class="badge bg-light text-dark">Total: <?= $resultado->num_rows ?></span>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Laboratorio / Equipo</th>
                            <th>Reportó</th>
                            <th>Descripción</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado->num_rows > 0): ?>
                            <?php while($row = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><small><?= $row['fecha_registro'] ?></small></td>
                                    <td><strong><?= htmlspecialchars($row['laboratorio']) ?></strong><br><small class="text-info"><?= htmlspecialchars($row['equipo_afectado']) ?></small></td>
                                    <td><?= htmlspecialchars($row['nombre_reporta']) ?></td>
                                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $row['prioridad']=='Alta'?'danger':($row['prioridad']=='Media'?'warning text-dark':'info text-dark') ?>">
                                            <?= $row['prioridad'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <?= $row['estado'] ?>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="dashboard.php?cambiar_estado=<?= $row['id'] ?>&estado=Pendiente"><i class="bi bi-clock me-1 text-secondary"></i> Pendiente</a></li>
                                                <li><a class="dropdown-item" href="dashboard.php?cambiar_estado=<?= $row['id'] ?>&estado=En proceso"><i class="bi bi-arrow-repeat me-1 text-primary"></i> En proceso</a></li>
                                                <li><a class="dropdown-item" href="dashboard.php?cambiar_estado=<?= $row['id'] ?>&estado=Resuelta"><i class="bi bi-check-circle me-1 text-success"></i> Resuelta</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm mb-1 text-dark fw-semibold"><i class="bi bi-pencil-square"></i></a>
                                        <button onclick="confirmarEliminacion(<?= $row['id'] ?>)" class="btn btn-danger btn-sm mb-1"><i class="bi bi-trash-fill"></i></button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-4 d-block mb-1"></i> No hay incidencias registradas para gestionar.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>