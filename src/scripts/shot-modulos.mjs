const fs = await import('fs');
const dir = 'C:/Users/rodri/Documents/COPIGABY_MANUAL/shots';
fs.mkdirSync(dir, { recursive: true });

const modulos = [
  ['03-pedido', 'pedido'],
  ['04-cliente', 'cliente'],
  ['05-cashea', 'cashea'],
  ['06-compra', 'compra'],
  ['07-producto', 'producto'],
  ['08-categoria', 'categoria'],
  ['09-servicio', 'servicio'],
  ['10-proveedor', 'proveedor'],
  ['11-user', 'user'],
  ['12-rol', 'rol'],
  ['13-metodopago', 'metodopago'],
  ['14-moneda', 'moneda'],
  ['15-iva', 'iva'],
  ['16-medida', 'medida'],
  ['17-empresa', 'empresa'],
  ['18-reporte', 'reporte'],
];

const results = [];
for (const [name, url] of modulos) {
  try {
    await page.goto(`http://localhost:8012/COPIGABY-2023-C.A/?url=${url}`, { waitUntil: 'networkidle', timeout: 20000 });
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${dir}/${name}.png`, fullPage: true });
    results.push([name, 'ok']);
  } catch (e) {
    results.push([name, 'FAIL ' + String(e).slice(0, 80)]);
  }
}
return results;
