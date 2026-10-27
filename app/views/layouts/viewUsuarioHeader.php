<?php
$nombreUsuario = $_SESSION['username'] ?? 'USUARIO';
$nombreCompleto = strtoupper($nombreUsuario);
$inicial = strtoupper(substr($nombreUsuario, 0, 1));
?>
<div class="flex items-center">
  <button type="button" id="btnOpenCredenciales" class="flex items-center gap-3 bg-slate-900 rounded-xl px-4 py-2 border border-slate-700 shadow-lg hover:bg-slate-800 transition-colors">
    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 border-orange-500 bg-orange-100 text-sm font-black text-orange-600">
      <?= htmlspecialchars($inicial) ?>
    </div>
    <span class="text-xs font-black text-slate-300 uppercase tracking-wide">BIENVENIDO, <?= htmlspecialchars($nombreCompleto) ?></span>
  </button>
</div>

<div id="modalCredenciales" class="fixed inset-0 z-[200] hidden overflow-y-auto">
  <div class="flex min-h-screen items-center justify-center px-4">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" id="overlayCredenciales"></div>
      <div class="relative w-full max-w-lg bg-white shadow-2xl rounded-2xl">
        <div class="absolute top-4 right-4">
          <button type="button" id="btnCancelarCredenciales" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
      <div class="px-6 py-5 border-b border-gray-100">
        <h3 class="text-center text-lg font-black text-slate-900 uppercase tracking-tight">Gestión de Credenciales de Usuario</h3>
        <p class="mt-1 text-center text-xs font-medium text-gray-500">Usted está visualizando los datos de: <?= htmlspecialchars($nombreCompleto) ?></p>
      </div>
      <form id="formCredenciales" class="p-6 space-y-4">
        <input type="hidden" name="username_actual" value="<?= htmlspecialchars($nombreCompleto) ?>">
        <div>
          <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Contraseña Actual</label>
          <div class="relative">
            <input type="password" name="password_actual" placeholder="••••••••" class="w-full px-4 py-2 pr-10 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-slate-900 outline-none focus:border-orange-500">
            <button type="button" class="toggle-pw absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700" aria-label="Mostrar contraseña">
              <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/></svg>
            </button>
          </div>
        </div>
        <div>
          <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nuevo Nombre de Usuario</label>
          <input type="text" name="nuevo_nombre" value="<?= htmlspecialchars($nombreCompleto) ?>" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-slate-900 outline-none focus:border-orange-500">
        </div>
        <div>
          <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nueva Contraseña</label>
          <div class="relative">
            <input type="password" name="nueva_password" placeholder="••••••••" class="w-full px-4 py-2 pr-10 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-slate-900 outline-none focus:border-orange-500">
            <button type="button" class="toggle-pw absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700" aria-label="Mostrar contraseña">
              <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/></svg>
            </button>
          </div>
        </div>
        <div>
          <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Confirmar Nueva Contraseña</label>
          <div class="relative">
            <input type="password" name="confirmar_password" placeholder="••••••••" class="w-full px-4 py-2 pr-10 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-slate-900 outline-none focus:border-orange-500">
            <button type="button" class="toggle-pw absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700" aria-label="Mostrar contraseña">
              <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/></svg>
            </button>
          </div>
        </div>
        <div class="flex justify-end gap-3 pt-4">
          <button type="button" id="btnCancelarCredenciales" class="px-5 py-2 text-xs font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">CANCELAR</button>
          <button type="submit" class="px-6 py-2 text-xs font-black text-white bg-orange-500 hover:bg-orange-600 rounded-lg shadow-md transition-colors uppercase tracking-wide">GUARDAR CAMBIOS</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function() {
  const modal = document.getElementById('modalCredenciales');
  const btnOpen = document.getElementById('btnOpenCredenciales');
  const btnCancel = document.getElementById('btnCancelarCredenciales');
  const overlay = document.getElementById('overlayCredenciales');
  const form = document.getElementById('formCredenciales');

  if (!modal || !btnOpen) return;

  function openModal() { modal.classList.remove('hidden'); }
  function closeModal() { modal.classList.add('hidden'); }

  btnOpen.addEventListener('click', openModal);
  btnCancel.addEventListener('click', closeModal);
  overlay.addEventListener('click', closeModal);

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('?url=user&type=credenciales', {
      method: 'POST',
      body: new FormData(form),
      headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
      if (data.status === 'success') {
        const nuevoNombre = new FormData(form).get('nuevo_nombre');
        if (nuevoNombre) {
          document.querySelector('#btnOpenCredenciales span').textContent = 'BIENVENIDO, ' + String(nuevoNombre).toUpperCase();
        }
        alert(data.message);
        closeModal();
        form.reset();
      } else {
        alert(data.message || 'No se pudo actualizar las credenciales.');
      }
    })
    .catch(() => alert('Error de conexión. Intente nuevamente.'));
  });

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  modal.querySelectorAll('.toggle-pw').forEach(function(btn) {
    if (btn.dataset.bound === '1') return;
    btn.dataset.bound = '1';

    btn.addEventListener('click', function () {
      const wrap = btn.closest('.relative');
      if (!wrap) return;
      const input = wrap.querySelector('input');
      const eyeOpen = btn.querySelector('.eye-open');
      const eyeClosed = btn.querySelector('.eye-closed');
      if (!input || !eyeOpen || !eyeClosed) return;

      const isPassword = input.type === 'password';
      input.type = isPassword ? 'text' : 'password';
      eyeOpen.style.display = isPassword ? 'none' : '';
      eyeClosed.style.display = isPassword ? '' : 'none';
      btn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');

      if (!prefersReducedMotion) {
        btn.style.transform = 'scale(0.92)';
        setTimeout(function () { btn.style.transform = ''; }, 120);
      }
    });
  });

  const observer = new MutationObserver(function() {
    modal.querySelectorAll('.toggle-pw').forEach(function(btn) {
      if (btn.dataset.bound !== '1') {
        const wrap = btn.closest('.relative');
        if (!wrap) return;
        const input = wrap.querySelector('input');
        const eyeOpen = btn.querySelector('.eye-open');
        const eyeClosed = btn.querySelector('.eye-closed');
        if (!input || !eyeOpen || !eyeClosed) return;

        btn.addEventListener('click', function () {
          const isPassword = input.type === 'password';
          input.type = isPassword ? 'text' : 'password';
          eyeOpen.style.display = isPassword ? 'none' : '';
          eyeClosed.style.display = isPassword ? '' : 'none';
          btn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');

          if (!prefersReducedMotion) {
            btn.style.transform = 'scale(0.92)';
            setTimeout(function () { btn.style.transform = ''; }, 120);
          }
        });
        btn.dataset.bound = '1';
      }
    });
  });

  observer.observe(modal, { childList: true, subtree: true });
})();
</script>
