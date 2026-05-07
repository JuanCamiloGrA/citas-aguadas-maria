<?php
require_once __DIR__ . '/config/helpers.php';

$messages = array();
$error = '';
$installed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminName = trim($_POST['admin_name']);
    $adminUser = trim($_POST['admin_user']);
    $adminPass = trim($_POST['admin_pass']);

    if ($adminName === '' || $adminUser === '' || $adminPass === '') {
        $error = 'Completa nombre, usuario y contraseña para crear el administrador inicial.';
    } else {
        try {
            $pdoRoot = get_pdo(false);
            $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $messages[] = 'Base de datos creada o verificada: ' . DB_NAME;

            $pdo = get_pdo(true);
            $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(120) NOT NULL,
                usuario VARCHAR(80) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                patient_name VARCHAR(160) NOT NULL,
                phone VARCHAR(40) NOT NULL,
                email VARCHAR(160) NULL,
                appointment_date DATE NOT NULL,
                appointment_time TIME NOT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'reserved',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_appointment_date (appointment_date),
                INDEX idx_appointment_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            $messages[] = 'Tablas principales creadas o verificadas.';

            $stmt = $pdo->prepare('SELECT id FROM admins WHERE usuario = ? LIMIT 1');
            $stmt->execute(array($adminUser));
            $existingAdmin = $stmt->fetch();

            if ($existingAdmin) {
                $messages[] = 'El usuario administrador ya existía, no se creó duplicado.';
            } else {
                $hash = password_hash($adminPass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO admins (nombre, usuario, password_hash) VALUES (?, ?, ?)');
                $stmt->execute(array($adminName, $adminUser, $hash));
                $messages[] = 'Administrador inicial creado correctamente.';
            }

            $installed = true;
        } catch (Exception $exception) {
            $error = 'No se pudo instalar la app: ' . $exception->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instalar - Hospital San José</title>
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="setup-page">
    <main class="setup-shell">
        <section class="setup-card">
            <img class="page-logo" src="assets/img/hospital-logo.png" alt="Logo Hospital San José">
            <p class="eyebrow">App demo</p>
            <h1>Sistema De Agendamiento de Citas Hospital San José</h1>
            <p class="muted">Este instalador crea la base de datos, las tablas y el primer administrador.</p>

            <?php if ($error !== '') { ?>
                <div class="alert alert-error"><?php echo h($error); ?></div>
            <?php } ?>

            <?php if ($installed) { ?>
                <div class="alert alert-success">
                    <?php foreach ($messages as $message) { ?>
                        <p><?php echo h($message); ?></p>
                    <?php } ?>
                </div>
                <div class="setup-actions">
                    <a class="button primary" href="index.php">Ir a agendar citas</a>
                    <a class="button secondary" href="admin/login.php">Ir al panel admin</a>
                </div>
            <?php } else { ?>
                <form method="post" class="form-grid">
                    <label>
                        Nombre del administrador
                        <input type="text" name="admin_name" value="Administrador Hospital San José" required>
                    </label>
                    <label>
                        Usuario
                        <input type="text" name="admin_user" value="admin" required>
                    </label>
                    <label>
                        Contraseña
                        <input type="password" name="admin_pass" value="admin123" required>
                    </label>
                    <button class="button primary" type="submit">Instalar app demo</button>
                </form>
            <?php } ?>
        </section>
    </main>
</body>
</html>
