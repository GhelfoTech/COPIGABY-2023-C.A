<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Iniciar Sesión</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --navy:      #1a2340;
      --navy-dark: #111827;
      --orange:    #f5a623;
      --orange-dk: #d4891a;
      --white:     #ffffff;
      --gray-100:  #f3f4f6;
      --gray-400:  #9ca3af;
      --gray-600:  #4b5563;
      --red:       #ef4444;
      --radius:    14px;
      --shadow:    0 20px 60px rgba(0,0,0,.35);
    }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--navy-dark);
      font-family: 'Nunito', sans-serif;
      overflow-y: auto;
    }

    /* Fondo animado */
    body::before {
      content: '';
      position: fixed; inset: 0;
      background:
        radial-gradient(ellipse 80% 60% at 20% 10%, rgba(245,166,35,.18) 0%, transparent 60%),
        radial-gradient(ellipse 60% 80% at 80% 90%, rgba(26,35,64,.9) 0%, transparent 70%);
      pointer-events: none;
    }

    /* Círculos decorativos de fondo */
    .bg-circles { position: fixed; inset: 0; overflow: hidden; pointer-events: none; }
    .bg-circles span {
      position: absolute;
      border-radius: 50%;
      border: 2px solid rgba(245,166,35,.12);
      animation: float 8s ease-in-out infinite;
    }
    .bg-circles span:nth-child(1) { width:420px; height:420px; top:-120px; left:-120px; animation-delay:0s; }
    .bg-circles span:nth-child(2) { width:280px; height:280px; bottom:-80px; right:-60px; animation-delay:2s; }
    .bg-circles span:nth-child(3) { width:160px; height:160px; top:40%; right:8%; animation-delay:4s; }

    @keyframes float {
      0%,100% { transform: translateY(0) scale(1); }
      50%      { transform: translateY(-18px) scale(1.04); }
    }

    /* Tarjeta principal */
    .card {
      position: relative; z-index: 10;
      background: rgba(255,255,255,.04);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      border: 1px solid rgba(255,255,255,.10);
      border-radius: 24px;
      padding: 48px 44px 44px;
      width: 100%; max-width: 440px;
      box-shadow: var(--shadow);
      animation: slideUp .5s cubic-bezier(.22,1,.36,1) both;
    }

    @keyframes slideUp {
      from { opacity:0; transform: translateY(32px); }
      to   { opacity:1; transform: translateY(0); }
    }

    /* Logo */
    .logo-wrap {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      margin-bottom: 32px;
    }

    .logo-badge {
      width: 90px; height: 90px;
      background: var(--navy);
      border-radius: 50%;
      border: 4px solid var(--orange);
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 0 0 6px rgba(245,166,35,.18);
    }

    .logo-badge svg { width:44px; height:44px; }

    .logo-name {
      font-family: 'Pacifico', cursive;
      font-size: 2rem;
      color: var(--white);
      letter-spacing: .5px;
    }

    .logo-name span { color: var(--orange); }

    .logo-tagline {
      font-size: .78rem;
      font-weight: 700;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      color: var(--orange);
      opacity: .85;
    }

    /* Título */
    .card h2 {
      color: var(--white);
      font-size: 1.25rem;
      font-weight: 800;
      text-align: center;
      margin-bottom: 28px;
    }

    /* Error */
    .alert-error {
      background: rgba(239,68,68,.15);
      border: 1px solid rgba(239,68,68,.4);
      color: #fca5a5;
      border-radius: 10px;
      padding: 12px 16px;
      font-size: .875rem;
      font-weight: 600;
      margin-bottom: 20px;
      display: flex; align-items: center; gap: 8px;
    }

    /* Formulario */
    .field { margin-bottom: 18px; }

    .field label {
      display: block;
      color: rgba(255,255,255,.7);
      font-size: .8rem;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .input-wrap { position: relative; }

    .input-wrap svg {
      position: absolute; left: 14px; top: 50%;
      transform: translateY(-50%);
      width: 18px; height: 18px;
      color: var(--gray-400);
      pointer-events: none;
    }

    .input-wrap input {
      width: 100%;
      background: rgba(255,255,255,.07);
      border: 1.5px solid rgba(255,255,255,.12);
      border-radius: 10px;
      padding: 13px 14px 13px 44px;
      color: var(--white);
      font-family: 'Nunito', sans-serif;
      font-size: .95rem;
      font-weight: 600;
      outline: none;
      transition: border-color .2s, background .2s;
    }

    .input-wrap input::placeholder { color: rgba(255,255,255,.3); font-weight: 400; }

    .input-wrap input:focus {
      border-color: var(--orange);
      background: rgba(245,166,35,.08);
    }

    /* Botón */
    .btn-login {
      width: 100%;
      margin-top: 8px;
      padding: 15px;
      background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dk) 100%);
      color: var(--navy-dark);
      font-family: 'Nunito', sans-serif;
      font-size: 1rem;
      font-weight: 900;
      letter-spacing: .5px;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: transform .15s, box-shadow .15s, filter .15s;
      box-shadow: 0 4px 20px rgba(245,166,35,.35);
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 28px rgba(245,166,35,.5);
      filter: brightness(1.06);
    }

    .btn-login:active { transform: translateY(0); }

    /* Footer */
    .card-footer {
      margin-top: 28px;
      text-align: center;
      color: rgba(255,255,255,.35);
      font-size: .78rem;
      font-weight: 600;
      letter-spacing: .5px;
    }

    /* Credenciales demo */
    .demo-hint {
      margin-top: 20px;
      background: rgba(245,166,35,.08);
      border: 1px dashed rgba(245,166,35,.3);
      border-radius: 10px;
      padding: 10px 14px;
      font-size: .75rem;
      color: rgba(255,255,255,.5);
      text-align: center;
      line-height: 1.6;
    }
    .demo-hint strong { color: var(--orange); }

    /* Responsividad */
    @media (max-width: 480px) {
      body {
        align-items: flex-start;
        padding: 40px 16px;
      }
      .card {
        padding: 32px 24px;
      }
      .logo-badge {
        width: 75px;
        height: 75px;
      }
      .logo-name {
        font-size: 1.7rem;
      }
    }
  </style>
</head>
<body>

  <div class="bg-circles">
    <span></span><span></span><span></span>
  </div>

  <div class="card">

    <!-- Logo -->
    <div class="logo-wrap">
      <div class="logo-badge">
        <img src="assets/img/logo.jpeg" alt="Logo CopiGaby" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
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
          <input
            type="text"
            id="username"
            name="username"
            placeholder="Tu nombre de usuario"
            autocomplete="username"
            required
          />
        </div>
      </div>

      <div class="field">
        <label for="password">Contraseña</label>
        <div class="input-wrap">
          <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="••••••••"
            autocomplete="current-password"
            required
          />
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
