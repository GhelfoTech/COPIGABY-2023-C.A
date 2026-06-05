<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Iniciar Sesión</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

  <div class="bg-circles">
    <span></span><span></span><span></span>
  </div>

  <div class="card">
    <div class="logo-wrap">
      <div class="logo-badge">
        <img src="assets/img/logo.jpeg" alt="Logo CopiGaby">
      </div>
      <div class="logo-name">Copi<span>Gaby</span></div>
      <div class="logo-tagline">¡Somos la diferencia!</div>
    </div>

    <h2>Iniciar Sesión</h2>

    <?php if (!empty($error)): ?>
      <div class="alert-error">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="?url=login" autocomplete="off">
      <div class="field">
        <label for="username">Usuario</label>
        <div class="input-wrap">
          <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
          <input type="text" id="username" name="username" placeholder="Tu nombre de usuario" autocomplete="username" required />
        </div>
      </div>

      <div class="field">
        <label for="password">Contraseña</label>
        <div class="input-wrap">
          <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
          <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required />
        </div>
      </div>

      <button type="submit" class="btn-login">Ingresar al Sistema</button>
    </form>

    <div class="demo-hint">
      Demo: <strong>admin</strong> / <strong>admin123</strong> &nbsp;·&nbsp; <strong>gaby</strong> / <strong>gaby2024</strong>
    </div>

    <div class="card-footer">
      CopiGaby 2025 &nbsp;·&nbsp; RIF V-9622717-1
    </div>
  </div>

</body>
</html>
