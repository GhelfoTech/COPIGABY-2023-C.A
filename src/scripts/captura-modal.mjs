export default async function run(page) {
  const dir = 'C:/Users/rodri/Documents/COPIGABY_MANUAL/shots';
  const BASE = 'http://localhost:8012/COPIGABY-2023-C.A';
  const fs = await import('fs');
  const path = await import('path');
  await page.setViewportSize({ width: 1440, height: 900 });

  await page.goto(`${BASE}/?url=login`, { waitUntil: 'networkidle' });
  await page.fill('input[name="username"]', 'ADMIN');
  await page.fill('input[name="password"]', '1234');
  await page.click('button:has-text("Ingresar al Sistema")');
  await page.waitForURL('**url=dashboard*', { timeout: 15000 });
  await page.waitForLoadState('networkidle');

  const coords = {};

  // Abre modal de registro, recorta el dialog y mide labels/botones RESPECTO AL DIALOG
  async function modalRegistro(url, shot, campos) {
    await page.goto(`${BASE}/?url=${url}`, { waitUntil: 'networkidle' });
    await page.waitForTimeout(400);
    await page.locator('button:has-text("AÑADIR")').first().click();
    await page.waitForTimeout(700);

    // localizar el diálogo: el ancestro "card" del título del modal
    const dlgInfo = await page.evaluate((titulos) => {
      for (const t of titulos) {
        const h = [...document.querySelectorAll('h1,h2,h3,h4,p,span')]
          .find(e => e.textContent.trim().toUpperCase().includes(t) && e.offsetParent);
        if (!h) continue;
        let el = h;
        // subir hasta el contenedor de tarjeta visible más grande razonable
        while (el.parentElement) {
          const r = el.getBoundingClientRect();
          if (r.width > 340 && r.width < 900 && r.height > 200) break;
          el = el.parentElement;
        }
        const r = el.getBoundingClientRect();
        return { x: r.x, y: r.y, width: r.width, height: r.height };
      }
      return null;
    }, ['NUEVO CLIENTE', 'NUEVO PRODUCTO', 'NUEVO SERVICIO', 'NUEVO PROVEEDOR', 'NUEVA CATEGORÍA']);

    if (!dlgInfo) { coords[shot] = { error: 'dialogo no encontrado' }; 
      await page.keyboard.press('Escape'); return; }

    await page.screenshot({ path: `${dir}/${shot}.png`, clip: dlgInfo });

    const data = await page.evaluate(({ box, campos }) => {
      const out = {};
      for (const [key, texto] of Object.entries(campos)) {
        const lab = [...document.querySelectorAll('label')]
          .find(e => e.textContent.trim().toUpperCase().includes(texto.toUpperCase()) && e.offsetParent);
        if (lab) {
          const r = lab.getBoundingClientRect();
          // punto a la IZQUIERDA del label (donde va el número)
          out[key] = {
            x: +(((r.left - 8) - box.x) / box.width * 100).toFixed(1),
            y: +(((r.top + r.height / 2) - box.y) / box.height * 100).toFixed(1),
          };
        } else out[key] = null;
      }
      for (const [key, txt] of [['_guardar', 'Guardar'], ['_cancelar', 'Cancelar']]) {
        const b = [...document.querySelectorAll('button')]
          .find(e => e.textContent.trim().toUpperCase().startsWith(txt.toUpperCase()) && e.offsetParent);
        if (b) {
          const r = b.getBoundingClientRect();
          out[key] = {
            x: +(((r.left + r.width / 2) - box.x) / box.width * 100).toFixed(1),
            y: +(((r.top + r.height / 2) - box.y) / box.height * 100).toFixed(1),
          };
        }
      }
      return out;
    }, { box: dlgInfo, campos });

    coords[shot] = data;

    const can = page.locator('button:has-text("Cancelar")').first();
    if (await can.isVisible().catch(() => false)) await can.click().catch(() => {});
    await page.keyboard.press('Escape');
    await page.waitForTimeout(300);
  }

  await modalRegistro('cliente', 'm-cliente-add', { CEDULA: 'Cédula', NOMBRE: 'Nombre Completo', TELEFONO: 'Teléfono', CORREO: 'Correo Electrónico', DIRECCION: 'Dirección' });
  await modalRegistro('producto', 'm-producto-add', { NOMBRE: 'Nombre del Producto', CATEGORIA: 'Categoría', GANANCIA: '% Ganancia', COSTO: 'Costo Unitario', PRECIO_CALC: 'Precio Calculado', STOCK_ACTUAL: 'Stock Actual', STOCK_MINIMO: 'Stock Mínimo', DESCRIPCION: 'Descripción' });
  await modalRegistro('servicio', 'm-servicio-add', { NOMBRE: 'Nombre', PRECIO: 'Precio', DESCRIPCION: 'Descripción', MATERIALES: 'Materiales que consume' });
  await modalRegistro('proveedor', 'm-proveedor-add', { RAZON: 'Razón Social', RIF: 'RIF', TELEFONO: 'Teléfono', CORREO: 'Correo', DIRECCION: 'Dirección' });
  await modalRegistro('categoria', 'm-categoria-add', { NOMBRE: 'Nombre', DESCRIPCION: 'Descripción' });

  // ---- modales de DETALLE: recortar dialog y medir botones ----
  for (const url of ['cliente', 'producto', 'servicio', 'proveedor']) {
    await page.goto(`${BASE}/?url=${url}`, { waitUntil: 'networkidle' });
    await page.waitForTimeout(600);
    try {
      await page.waitForSelector('table tbody tr td button', { timeout: 8000 });
      await page.locator('table tbody tr').first().locator('td button').first().click();
      await page.waitForTimeout(700);
      const shot = `m-${url}-det`;

      const dlgInfo = await page.evaluate(() => {
        const h = [...document.querySelectorAll('h1,h2,h3,h4,p,span')]
          .find(e => /DETALLE/i.test(e.textContent.trim()) && e.offsetParent);
        if (!h) return null;
        let el = h;
        while (el.parentElement) {
          const r = el.getBoundingClientRect();
          if (r.width > 300 && r.width < 900 && r.height > 200) break;
          el = el.parentElement;
        }
        const r = el.getBoundingClientRect();
        return { x: r.x, y: r.y, width: r.width, height: r.height };
      });
      if (!dlgInfo) { coords[shot] = { error: 'dialogo no encontrado' }; continue; }

      await page.screenshot({ path: `${dir}/${shot}.png`, clip: dlgInfo });

      const data = await page.evaluate((box) => {
        const out = {};
        for (const [key, txt] of [['MODIFICAR', 'Modificar'], ['ELIMINAR', 'Eliminar'], ['CERRAR', 'Cerrar']]) {
          const b = [...document.querySelectorAll('button')]
            .find(e => e.textContent.trim().toUpperCase() === txt && e.offsetParent);
          if (b) {
            const r = b.getBoundingClientRect();
            out[key] = {
              x: +(((r.left + r.width / 2) - box.x) / box.width * 100).toFixed(1),
              y: +(((r.top + r.height / 2) - box.y) / box.height * 100).toFixed(1),
            };
          }
        }
        return out;
      }, dlgInfo);
      coords[shot] = data;

      const cer = page.locator('button:has-text("Cerrar")').first();
      if (await cer.isVisible().catch(() => false)) await cer.click().catch(() => {});
      await page.keyboard.press('Escape');
      await page.waitForTimeout(300);
    } catch (e) { coords[`m-${url}-det`] = { error: String(e).slice(0, 80) }; }
  }

  fs.writeFileSync(path.join(dir, 'coords.json'), JSON.stringify(coords, null, 2));
  return coords;
}
