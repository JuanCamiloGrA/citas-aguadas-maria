<?php
require_once __DIR__ . '/auth.php';
require_admin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $user = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($name === '' || $user === '' || $password === '') {
        $error = 'Completa todos los campos.';
    } else {
        try {
            $pdo = get_pdo(true);
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM admins WHERE usuario = ?');
            $stmt->execute(array($user));

            if ((int) $stmt->fetchColumn() > 0) {
                $error = 'Ese usuario ya existe.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO admins (nombre, usuario, password_hash) VALUES (?, ?, ?)');
                $stmt->execute(array($name, $user, $hash));
                $message = 'Administrador creado correctamente.';
            }
        } catch (Exception $exception) {
            $error = 'No se pudo crear el administrador.';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo admin - Hospital San José</title>
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
            <a href="index.php">Citas</a>
            <a href="../index.php">Vista pública</a>
            <a href="logout.php">Salir</a>
        </nav>
    </header>

    <main class="admin-shell narrow">
        <section class="admin-form-card">
            <p class="eyebrow">Usuarios admin</p>
            <h1>Registrar administrador</h1>
            <p class="muted">Crea una cuenta simple para otro usuario del panel.</p>

            <?php if ($message !== '') { ?>
                <div class="alert alert-success"><?php echo h($message); ?></div>
            <?php } ?>
            <?php if ($error !== '') { ?>
                <div class="alert alert-error"><?php echo h($error); ?></div>
            <?php } ?>

            <form method="post" class="form-grid">
                <label>
                    Nombre
                    <input type="text" name="nombre" required>
                </label>
                <label>
                    Usuario
                    <input type="text" name="usuario" required>
                </label>
                <label>
                    Contraseña
                    <input type="password" name="password" required>
                </label>
                <button class="button primary" type="submit">Crear administrador</button>
            </form>
        </section>
    </main>
</body>
</html>
