<?php
require __DIR__ . '/includes/auth.php';

// Si ya inició sesión, redirige directo a su panel
if (isset($_SESSION['usuario'])) {
    redirigirSegunRol($_SESSION['usuario']['rol']);
}

$errorLogin = $_SESSION['error_login'] ?? '';
$errorRegistro = $_SESSION['error_registro'] ?? '';
$tabActiva = $_SESSION['tab_activa'] ?? 'login';
unset($_SESSION['error_login'], $_SESSION['error_registro'], $_SESSION['tab_activa']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>IMPRIMEX · Registro / Inicio de sesión</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="topbar">
  <h1>IMPRIMEX · Plataforma de Gestión de Pedidos</h1>
  <p>Impresión de interiores y fachadas</p>
</div>

<div class="login-wrap">
  <div class="login-card">
    <h2 style="margin-bottom:4px;">Registro e inicio de sesión</h2>
    <p style="color:var(--text-muted); font-size:14px; margin-top:0;">
      Punto de entrada único. El sistema detecta tu rol (cliente, empleado o administrador) al iniciar sesión y te redirige al panel correspondiente.
    </p>

    <div class="card">
      <div class="tabs">
        <button type="button" class="tab-btn <?= $tabActiva === 'login' ? 'active' : '' ?>" data-tab="login">Iniciar sesión</button>
        <button type="button" class="tab-btn <?= $tabActiva === 'registro' ? 'active' : '' ?>" data-tab="registro">Crear cuenta</button>
      </div>

      <!-- LOGIN -->
      <div id="panel-login" class="tab-panel" style="<?= $tabActiva !== 'login' ? 'display:none;' : '' ?>">
        <?php if ($errorLogin): ?>
          <div class="alert alert-error"><?= htmlspecialchars($errorLogin) ?></div>
        <?php endif; ?>
        <form action="login_process.php" method="post">
          <div class="form-group">
            <label>Correo electrónico</label>
            <input type="email" name="correo" placeholder="cliente@correo.com" required>
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="••••••••" required>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;">Entrar</button>
        </form>
        <p style="text-align:center; font-size:13px; color:var(--text-muted); margin-top:16px;">
          ¿Nuevo cliente? Usa la pestaña "Crear cuenta" para registrarte y hacer seguimiento a tus pedidos.
        </p>
      </div>

      <!-- REGISTRO -->
      <div id="panel-registro" class="tab-panel" style="<?= $tabActiva !== 'registro' ? 'display:none;' : '' ?>">
        <?php if ($errorRegistro): ?>
          <div class="alert alert-error"><?= htmlspecialchars($errorRegistro) ?></div>
        <?php endif; ?>
        <form action="register_process.php" method="post">
          <div class="form-group">
            <label>Nombre completo</label>
            <input type="text" name="nombre" placeholder="Tu nombre" required>
          </div>
          <div class="form-group">
            <label>Correo electrónico</label>
            <input type="email" name="correo" placeholder="cliente@correo.com" required>
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="Mínimo 6 caracteres" minlength="6" required>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;">Crear cuenta</button>
        </form>
        <p style="text-align:center; font-size:13px; color:var(--text-muted); margin-top:16px;">
          Las cuentas nuevas se crean como <strong>cliente</strong>. Los accesos de empleado y administrador los crea el administrador del sistema.
        </p>
      </div>
    </div>

    <p class="footer-note" style="margin-top:24px;">
      Demo: admin@imprimex.com / admin123 · andrea@imprimex.com / empleado123 · cliente@correo.com / cliente123
    </p>
  </div>
</div>

<script>
document.querySelectorAll('.tab-btn').forEach(function (btn) {
  btn.addEventListener('click', function () {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
    btn.classList.add('active');
    document.getElementById('panel-' + btn.dataset.tab).style.display = 'block';
  });
});
</script>
</body>
</html>
