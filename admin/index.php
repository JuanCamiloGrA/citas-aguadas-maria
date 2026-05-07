<?php
require_once __DIR__ . '/auth.php';
require_admin();

$message = '';
$error = '';

try {
    $pdo = get_pdo(true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
        $cancelId = (int) $_POST['cancel_id'];
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND status = 'reserved'");
        $stmt->execute(array($cancelId));
        $message = 'La cita fue cancelada.';
    }

    $stmt = $pdo->query("SELECT id, patient_name, phone, email, appointment_date,
        TIME_FORMAT(appointment_time, '%H:%i') AS appointment_time, status, created_at
        FROM appointments
        ORDER BY appointment_date DESC, appointment_time DESC, id DESC");
    $appointments = $stmt->fetchAll();
} catch (Exception $exception) {
    $appointments = array();
    $error = 'No se pudieron cargar las citas. Ejecuta install.php primero.';
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel admin - Hospital San José</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/apple-touch-icon.png">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="admin-page">
    <header class="site-header admin-header">
        <a class="brand" href="index.php">
            <span class="brand-mark">
                <img src="../assets/img/hospital-logo.png" alt="Logo Hospital San José">
            </span>
            <span>
                <strong>Panel administrativo</strong>
                <small><?php echo h(current_admin_name()); ?></small>
            </span>
        </a>
        <nav>
            <a href="../index.php">Vista pública</a>
            <a href="create-admin.php">Nuevo admin</a>
            <a href="logout.php">Salir</a>
        </nav>
    </header>

    <main class="admin-shell">
        <section class="admin-title">
            <p class="eyebrow">Citas registradas</p>
            <h1>Agenda Hospital San José</h1>
            <p>Consulta reservas activas y cancela horarios para liberarlos nuevamente.</p>
        </section>

        <?php if ($message !== '') { ?>
            <div class="alert alert-success"><?php echo h($message); ?></div>
        <?php } ?>
        <?php if ($error !== '') { ?>
            <div class="alert alert-error"><?php echo h($error); ?></div>
        <?php } ?>

        <section class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Contacto</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($appointments) === 0) { ?>
                        <tr>
                            <td colspan="6" class="empty-cell">No hay citas registradas.</td>
                        </tr>
                    <?php } ?>

                    <?php foreach ($appointments as $appointment) { ?>
                        <tr>
                            <td>
                                <strong><?php echo h($appointment['patient_name']); ?></strong>
                                <?php if ($appointment['email'] !== null && $appointment['email'] !== '') { ?>
                                    <small><?php echo h($appointment['email']); ?></small>
                                <?php } ?>
                            </td>
                            <td><?php echo h($appointment['phone']); ?></td>
                            <td><?php echo h($appointment['appointment_date']); ?></td>
                            <td><?php echo h(format_time_12h($appointment['appointment_time'])); ?></td>
                            <td>
                                <span class="pill <?php echo $appointment['status'] === 'reserved' ? 'pill-success' : 'pill-muted'; ?>">
                                    <?php echo $appointment['status'] === 'reserved' ? 'Reservada' : 'Cancelada'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($appointment['status'] === 'reserved') { ?>
                                    <form method="post" class="inline-form">
                                        <input type="hidden" name="cancel_id" value="<?php echo (int) $appointment['id']; ?>">
                                        <button class="button danger small" type="submit">Cancelar</button>
                                    </form>
                                <?php } else { ?>
                                    <span class="muted">Sin acción</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
