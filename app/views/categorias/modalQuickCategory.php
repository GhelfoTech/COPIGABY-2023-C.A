<div id="modalQuickCategory" class="fixed inset-0 z-[200] hidden overflow-y-auto" aria-hidden="true">
  <div class="flex min-h-screen items-center justify-center p-4">
    <button type="button" class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" data-close-quick-category aria-label="Cerrar modal de categoría"></button>
    <section class="relative w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl" role="dialog" aria-modal="true" aria-labelledby="modalQuickCategoryTitle">
      <header class="flex items-center justify-between border-b bg-gray-50/50 px-6 py-4">
        <h3 id="modalQuickCategoryTitle" class="text-xl font-black text-navy-dark">Añadir categoría</h3>
        <button type="button" data-close-quick-category aria-label="Cerrar" class="text-gray-400 hover:text-navy-dark">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </header>
      <form id="formQuickCategory" action="?url=categoria&type=quick_register" method="POST" class="space-y-4 p-6">
        <label for="quickCategoryName" class="block text-xs font-black uppercase text-gray-500">Nombre de la categoría</label>
        <input type="text" id="quickCategoryName" name="nombre_categoria" maxlength="100" required class="w-full rounded-lg border bg-gray-50 px-4 py-2 font-bold outline-none focus:border-orange">
        <div class="flex justify-end gap-3">
          <button type="button" data-close-quick-category class="rounded-lg px-5 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100">Cancelar</button>
          <button type="submit" id="btnSaveQuickCategory" class="rounded-lg bg-navy-dark px-6 py-2 text-sm font-black text-white hover:bg-navy">Guardar categoría</button>
        </div>
      </form>
    </section>
  </div>
</div>
