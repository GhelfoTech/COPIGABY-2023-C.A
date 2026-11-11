export default async function run(page, ui) {
  await page.goto('http://localhost:8012/COPIGABY-2023-C.A/?url=login', { waitUntil: 'networkidle' });
  await page.fill('input[name="username"]', 'ADMIN');
  await page.fill('input[name="password"]', '1234');
  await page.click('button:has-text("Ingresar al Sistema")');
  await page.waitForURL('**url=dashboard*', { timeout: 15000 });

  const out = {};
  for (const m of ['cliente', 'producto', 'servicio', 'proveedor']) {
    await page.goto(`http://localhost:8012/COPIGABY-2023-C.A/?url=${m}`, { waitUntil: 'networkidle' });
    await page.waitForTimeout(500);
    const snap = await ui.snapshot();
    out[m] = snap.split('\n').filter(l => /button|Añadir|Registrar|Nuevo/.test(l)).slice(0, 8);
  }
  return out;
}
