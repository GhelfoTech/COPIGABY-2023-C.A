<?php
// Este mismo modal se reutiliza en el módulo Clientes y en el módulo Pedidos.
$clienteModalActionFinal = $clienteModalAction ?? '?url=cliente&type=register';
?>
<div id="modalClienteRegistro" class="fixed inset-0 hidden overflow-y-auto" style="z-index: 10000; isolation: isolate;" aria-hidden="true">
  <div class="relative flex min-h-screen items-center justify-center px-4 py-6">
    <div class="absolute inset-0 bg-navy-dark/70 backdrop-blur-sm" data-close-cliente-modal aria-hidden="true"></div>

    <section class="relative z-10 w-full max-w-md overflow-y-auto rounded-[14px] bg-white shadow-2xl animate-fade-up" style="max-height: calc(100vh - 3rem);" role="dialog" aria-modal="true" aria-labelledby="tituloModalClienteRegistro">
      <div class="flex items-center justify-between border-b bg-gray-50/50 px-6 py-4">
        <h3 id="tituloModalClienteRegistro" class="text-xl font-black text-navy-dark">Nuevo Cliente</h3>
        <button type="button" data-close-cliente-modal class="text-gray-400 transition-colors hover:text-navy-dark" aria-label="Cerrar formulario de cliente">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <form id="formClienteRegistro" action="<?= htmlspecialchars($clienteModalActionFinal, ENT_QUOTES, 'UTF-8') ?>" method="POST" class="p-6">
        <div class="mb-4">
          <label for="clienteRegistroCedula" class="mb-1 block text-xs font-black uppercase text-gray-400">Cédula <span class="text-red-500">*</span></label>
          <div class="flex gap-0">
            <span class="inline-flex items-center rounded-l-lg border border-r-0 bg-gray-200 px-3 py-2 text-sm font-black text-navy-dark">V-</span>
            <input type="tel" inputmode="numeric" pattern="[0-9]*" id="clienteRegistroCedula" name="cedula_cliente" required class="no-spinner w-full rounded-r-lg border bg-gray-50 px-4 py-2 text-sm font-bold outline-hidden focus:border-orange" placeholder="12345678">
          </div>
        </div>

        <div class="mb-4">
          <label for="clienteRegistroNombre" class="mb-1 block text-xs font-black uppercase text-gray-400">Nombre Completo <span class="text-red-500">*</span></label>
          <input type="text" id="clienteRegistroNombre" name="nombre" required class="w-full rounded-lg border bg-gray-50 px-4 py-2 text-sm font-bold outline-hidden focus:border-orange" placeholder="Juan Pérez">
        </div>

        <div class="mb-4">
          <label for="clienteRegistroTelefono" class="mb-1 block text-xs font-black uppercase text-gray-400">Teléfono <span class="text-red-500">*</span></label>
          <input type="tel" inputmode="numeric" pattern="[0-9]*" id="clienteRegistroTelefono" name="telefono" required class="w-full rounded-lg border bg-gray-50 px-4 py-2 text-sm font-bold outline-hidden focus:border-orange" placeholder="04121234567">
        </div>

        <div class="mb-4">
          <label for="clienteRegistroCorreo" class="mb-1 block text-xs font-black uppercase text-gray-400">Correo Electrónico</label>
          <input type="email" id="clienteRegistroCorreo" name="correo" class="w-full rounded-lg border bg-gray-50 px-4 py-2 text-sm font-bold outline-hidden focus:border-orange" placeholder="correo@cliente.com">
        </div>

        <div class="mb-4">
          <label for="clienteRegistroDireccion" class="mb-1 block text-xs font-black uppercase text-gray-400">Dirección <span class="text-red-500">*</span></label>
          <textarea id="clienteRegistroDireccion" name="direccion" rows="2" required class="w-full rounded-lg border bg-gray-50 px-4 py-2 font-bold outline-hidden focus:border-orange"></textarea>
        </div>

        <div class="mt-8 flex justify-end gap-3">
          <button type="button" data-close-cliente-modal class="rounded-lg px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100">Cancelar</button>
          <button type="submit" id="btnGuardarClienteRegistro" class="rounded-lg bg-navy-dark px-8 py-2 text-sm font-black text-white shadow-lg transition-all hover:bg-navy">Guardar</button>
        </div>
      </form>
    </section>
  </div>
</div>
