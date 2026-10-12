<?php
include 'config/conexion.php';
session_start();

// Procesar Registro de Incidencia
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar'])) {
    $nombre = trim($_POST['nombre_reporta']);
    $lab = trim($_POST['laboratorio']);
    $equipo = trim($_POST['equipo_afectado']);
    $desc = trim($_POST['descripcion']);
    $prioridad = $_POST['prioridad'];

    if (!empty($nombre) && !empty($lab) && !empty($equipo) && !empty($desc)) {
        $stmt = $conexion->prepare("INSERT INTO incidencias (nombre_reporta, laboratorio, equipo_afectado, descripcion, prioridad) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $lab, $equipo, $desc, $prioridad);
        $stmt->execute();
        header("Location: index.php");
        exit();
    }
}

// Filtros de búsqueda
$filtro_lab = $_GET['laboratorio'] ?? '';
$filtro_estado = $_GET['estado'] ?? '';
$filtro_prioridad = $_GET['prioridad'] ?? '';

$sql = "SELECT * FROM incidencias WHERE 1=1";
$params = [];
$types = "";

if (!empty($filtro_lab)) {
    $sql .= " AND laboratorio LIKE ?";
    $params[] = "%$filtro_lab%";
    $types .= "s";
}
if (!empty($filtro_estado)) {
    $sql .= " AND estado = ?";
    $params[] = $filtro_estado;
    $types .= "s";
}
if (!empty($filtro_prioridad)) {
    $sql .= " AND prioridad = ?";
    $params[] = $filtro_prioridad;
    $types .= "s";
}

$sql .= " ORDER BY fecha_registro DESC";
$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Incidencias - Laboratorios</title>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados (Modo Enfoque / Azul Oscuro) -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de Navegación Superior -->
    <nav class="navbar navbar-expand-lg bg-primary mb-4" data-bs-theme="dark">
        <div class="container">
            <span class="navbar-brand mb-0 h1"><i class="bi bi-cpu-fill me-2"></i>Sistemas - Control de Incidencias</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarColor01">
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['admin'])): ?>
                        <li class="nav-item">
                            <a href="admin/dashboard.php" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-speedometer2 me-1"></i> Panel Admin</a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="login.php" class="btn btn-outline-light btn-sm"><i class="bi bi-shield-lock-fill me-1"></i> Acceso Admin</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <!-- Formulario de Registro -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Reportar Incidencia
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="registrar" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-person-fill me-1"></i> Nombre del reportante</label>
                                <input type="text" name="nombre_reporta" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-door-open-fill me-1"></i> Laboratorio</label>
                                <input type="text" name="laboratorio" class="form-control" placeholder="Ej. Lab 1" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-pc-display me-1"></i> Equipo Afectado</label>
                                <input type="text" name="equipo_afectado" class="form-control" placeholder="Ej. PC-12" required>
                            </div>
                            
                            <!-- Prioridad con ComboBox Personalizado -->
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-flag-fill me-1"></i> Prioridad</label>
                                <input type="hidden" name="prioridad" id="prioridad_input" value="Media">

                                <div class="dropdown">
                                    <button class="form-control combobox-toggle d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="combobox-value">Media</span>
                                        <svg class="combobox-caret" width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.46967 5.46967C0.762563 5.17678 1.23744 5.17678 1.53033 5.46967L5 8.93934L8.46967 5.46967C8.76256 5.17678 9.23744 5.17678 9.53033 5.46967C9.82322 5.76256 9.82322 6.23744 9.53033 6.53033L5.53033 10.5303C5.23744 10.8232 4.76256 10.8232 4.46967 10.5303L0.46967 6.53033C0.176777 6.23744 0.176777 5.76256 0.46967 5.46967Z" fill="currentcolor"/>
                                        </svg>
                                    </button>
                                    <ul class="dropdown-menu w-100">
                                        <li><button class="dropdown-item menu-item" type="button" data-bs-value="Baja">Baja</button></li>
                                        <li><button class="dropdown-item menu-item" type="button" data-bs-value="Media">Media</button></li>
                                        <li><button class="dropdown-item menu-item" type="button" data-bs-value="Alta">Alta</button></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-card-text me-1"></i> Descripción del problema</label>
                                <textarea name="descripcion" class="form-control" rows="3" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle-fill me-2"></i> Registrar Incidencia
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Listado y Filtros -->
            <div class="col-md-8">
                <!-- Tarjeta de Filtros -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <input type="text" name="laboratorio" class="form-control" placeholder="Filtrar por Lab" value="<?= htmlspecialchars($filtro_lab) ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="estado" class="form-select">
                                    <option value="">Estado...</option>
                                    <option value="Pendiente" <?= $filtro_estado=='Pendiente'?'selected':'' ?>>Pendiente</option>
                                    <option value="En proceso" <?= $filtro_estado=='En proceso'?'selected':'' ?>>En proceso</option>
                                    <option value="Resuelta" <?= $filtro_estado=='Resuelta'?'selected':'' ?>>Resuelta</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="prioridad" class="form-select">
                                    <option value="">Prioridad...</option>
                                    <option value="Baja" <?= $filtro_prioridad=='Baja'?'selected':'' ?>>Baja</option>
                                    <option value="Media" <?= $filtro_prioridad=='Media'?'selected':'' ?>>Media</option>
                                    <option value="Alta" <?= $filtro_prioridad=='Alta'?'selected':'' ?>>Alta</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="bi bi-search"></i> Filtrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Resultados -->
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <i class="bi bi-list-task me-2"></i> Listado de Incidencias
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Lab / Equipo</th>
                                    <th>Reportó</th>
                                    <th>Descripción</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($resultado->num_rows > 0): ?>
                                    <?php while($row = $resultado->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><small><?= $row['fecha_registro'] ?></small></td>
                                            <td><strong><?= htmlspecialchars($row['laboratorio']) ?></strong><br><small><?= htmlspecialchars($row['equipo_afectado']) ?></small></td>
                                            <td><?= htmlspecialchars($row['nombre_reporta']) ?></td>
                                            <td><?= htmlspecialchars($row['descripcion']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $row['prioridad']=='Alta'?'danger':($row['prioridad']=='Media'?'warning text-dark':'info text-dark') ?>">
                                                    <?= $row['prioridad'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $row['estado']=='Resuelta'?'success':($row['estado']=='En proceso'?'primary':'secondary') ?>">
                                                    <?= $row['estado'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-4 d-block mb-1"></i> No hay incidencias registradas.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para el funcionamiento del ComboBox de prioridad -->
    <script>
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelector('.combobox-value').textContent = this.textContent;
                document.getElementById('prioridad_input').value = this.getAttribute('data-bs-value');
            });
        });
    </script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>