<?php
$currentUrl = $_REQUEST['url'] ?? '';
$pedidoOpen = in_array($currentUrl, ['pedido', 'cliente'], true);
$productoOpen = in_array($currentUrl, ['producto', 'categoria'], true);
$configOpen = in_array($currentUrl, ['usuario', 'rol', 'metodopago', 'moneda', 'iva', 'medida'], true);

function navLinkClass(string $url, string $current): string {
    $base = 'nav-link';
    return $url === $current ? $base . ' nav-link-active' : $base;
}

function navSubLinkClass(string $url, string $current): string {
    $base = 'nav-sublink block';
    return $url === $current ? $base . ' nav-sublink-active' : $base;
}

function navToggleClass(bool $isOpen, bool $isActive): string {
    $base = 'nav-toggle';
    if ($isActive || $isOpen) {
        return $base . ' nav-toggle-active';
    }
    return $base;
}
?>
<aside class="fixed inset-y-0 left-0 w-[260px] bg-navy-dark flex flex-col z-[100] shrink-0 sidebar-scroll overflow-y-auto">
  <div class="p-[20px_20px_16px] border-b border-white/10">
    <div class="flex items-center gap-[10px]">
      <div class="w-10 h-10 bg-navy rounded-full border-2 border-orange flex items-center justify-center shrink-0 shadow-[0_0_0_4px_rgba(245,166,35,0.1)]">
        <img src="assets/img/logo.jpeg" alt="Logo CopiGaby" class="w-full h-full rounded-full object-cover">
      </div>
      <div>
        <div class="font-['Pacifico'] text-white text-[1.15rem] leading-tight">Copi<span class="text-orange">Gaby</span></div>
        <div class="text-[0.65rem] font-bold tracking-[2px] uppercase text-white/35">Sistema 2025</div>
      </div>
    </div>
  </div>

  <div class="p-[22px_20px_8px] text-[0.65rem] font-extrabold tracking-[2px] uppercase text-white/30">Menú Principal</div>

  <a href="?url=dashboard" class="<?= navLinkClass('dashboard', $currentUrl) ?>">
    <svg class="w-[18px] h-[18px] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
    Inicio
  </a>

  <div class="dropdown-parent<?= $pedidoOpen ? ' open' : '' ?>">
    <button type="button" class="<?= navToggleClass($pedidoOpen, in_array($currentUrl, ['pedido', 'cliente'], true)) ?> dropdown-toggle">
      <svg class="w-[18px] h-[18px] shrink-0<?= $pedidoOpen ? ' text-orange' : '' ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"/></svg>
      Pedido
      <svg class="nav-arrow<?= $pedidoOpen ? ' rotate-90' : '' ?>" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
    </button>
    <div class="dropdown-menu flex flex-col bg-black/20">
      <a href="?url=pedido" class="<?= navSubLinkClass('pedido', $currentUrl) ?>">Pedidos</a>
      <a href="?url=cliente" class="<?= navSubLinkClass('cliente', $currentUrl) ?>">Clientes</a>
    </div>
  </div>

  <a href="?url=compra" class="<?= navLinkClass('compra', $currentUrl) ?>">
    <svg class="w-[18px] h-[18px] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4z" clip-rule="evenodd"/></svg>
    Compras
  </a>

  <div class="dropdown-parent<?= $productoOpen ? ' open' : '' ?>">
    <button type="button" class="<?= navToggleClass($productoOpen, in_array($currentUrl, ['producto', 'categoria'], true)) ?> dropdown-toggle">
      <svg class="w-[18px] h-[18px] shrink-0<?= $productoOpen ? ' text-orange' : '' ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
      Producto
      <svg class="nav-arrow<?= $productoOpen ? ' rotate-90' : '' ?>" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
    </button>
    <div class="dropdown-menu flex flex-col bg-black/20">
      <a href="?url=producto" class="<?= navSubLinkClass('producto', $currentUrl) ?>">Productos</a>
      <a href="?url=categoria" class="<?= navSubLinkClass('categoria', $currentUrl) ?>">Categoria</a>
    </div>
  </div>

  <a href="?url=servicio" class="<?= navLinkClass('servicio', $currentUrl) ?>">
    <svg class="w-[18px] h-[18px] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
    Servicios
  </a>

  <a href="?url=proveedor" class="<?= navLinkClass('proveedor', $currentUrl) ?>">
    <svg class="w-[18px] h-[18px] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
    Proveedores
  </a>

  <div class="dropdown-parent<?= $configOpen ? ' open' : '' ?>">
    <button type="button" class="<?= navToggleClass($configOpen, $configOpen) ?> dropdown-toggle">
      <svg class="w-[18px] h-[18px] shrink-0<?= $configOpen ? ' text-orange' : '' ?>" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
      Configuracion
      <svg class="nav-arrow<?= $configOpen ? ' rotate-90' : '' ?>" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
    </button>
    <div class="dropdown-menu flex flex-col bg-black/20">
      <a href="?url=usuario" class="<?= navSubLinkClass('usuario', $currentUrl) ?>">Usuarios</a>
      <a href="?url=rol" class="<?= navSubLinkClass('rol', $currentUrl) ?>">Rol</a>
      <a href="?url=metodopago" class="<?= navSubLinkClass('metodopago', $currentUrl) ?>">Metodo de Pago</a>
      <a href="?url=moneda" class="<?= navSubLinkClass('moneda', $currentUrl) ?>">Moneda</a>
      <a href="?url=iva" class="<?= navSubLinkClass('iva', $currentUrl) ?>">IVA</a>
      <a href="?url=medida" class="<?= navSubLinkClass('medida', $currentUrl) ?>">Unidad de Medida</a>
    </div>
  </div>

  <a href="?url=reporte" class="<?= navLinkClass('reporte', $currentUrl) ?>">
    <svg class="w-[18px] h-[18px] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd"/></svg>
    Reporte
  </a>

  <div class="mt-auto p-4 border-t border-white/10">
    <a href="?url=login" class="flex items-center gap-[10px] text-white/45 font-bold text-[0.85rem] p-2 rounded-lg transition-all hover:text-red-300 hover:bg-red-500/10">
      <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
      Cerrar Sesión
    </a>
  </div>
</aside>

<script>
document.querySelectorAll('.dropdown-toggle').forEach(function(btn) {
  btn.addEventListener('click', function() {
    btn.parentElement.classList.toggle('open');
  });
});
</script>
