<?php
require_once __DIR__ . '/auth.php';

if (admin_is_logged_in()) {
    redirect_to('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($user === '' || $password === '') {
        $error = 'Ingresa usuario y contraseña.';
    } else {
        try {
            $pdo = get_pdo(true);
            $stmt = $pdo->prepare('SELECT id, nombre, usuario, password_hash FROM admins WHERE usuario = ? LIMIT 1');
            $stmt->execute(array($user));
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['nombre'];
                $_SESSION['admin_user'] = $admin['usuario'];
                redirect_to('index.php');
            }

            $error = 'Credenciales incorrectas.';
        } catch (Exception $exception) {
            $error = 'No se pudo iniciar sesión. Ejecuta install.php primero.';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Hospital San José</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/apple-touch-icon.png">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="admin-page">
    <main class="login-shell">
        <section class="login-card">
            <img class="page-logo" src="../assets/img/hospital-logo.png" alt="Logo Hospital San José">
            <p class="eyebrow">Panel protegido</p>
            <h1>Administración Hospital San José</h1>
            <p class="muted">Ingresa para revisar citas y registrar administradores.</p>

            <?php if ($error !== '') { ?>
                <div class="alert alert-error"><?php echo h($error); ?></div>
            <?php } ?>

            <form method="post" class="form-grid">
                <label>
                    Usuario
                    <input type="text" name="usuario" required autofocus>
                </label>
                <label>
                    Contraseña
                    <input type="password" name="password" required>
                </label>
                <button class="button primary" type="submit">Entrar al panel</button>
            </form>

            <a class="return-link" href="../index.php">Volver a agendar una cita</a>
        </section>
    </main>
</body>
</html>
