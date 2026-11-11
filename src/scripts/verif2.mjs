export default async function run(page) {
  await page.setViewportSize({ width: 794, height: 1123 });
  await page.goto('file:///C:/Users/rodri/Documents/COPIGABY_MANUAL/shots/manual.html', { waitUntil: 'networkidle' });
  await page.waitForTimeout(1500);

  // 1) artefactos: SVGs u otros elementos dentro de .mk
  const artefactos = await page.evaluate(() => {
    const mks = [...document.querySelectorAll('.mk')];
    return {
      totalMk: mks.length,
      conSvg: mks.filter(m => m.querySelector('svg')).length,
      muestra: mks.slice(0, 2).map(m => m.outerHTML),
    };
  });

  // 2) índice vs página real: para cada fila del índice, buscar el h2 correspondiente y en qué .pg cayó
  const cheq = await page.evaluate(() => {
    const paginas = [...document.querySelectorAll('.pg')];
    const filas = [...document.querySelectorAll('table.idx tr')].map(tr => {
      const titulo = tr.querySelector('td').textContent.trim();
      const numIdx = parseInt(tr.querySelector('td.pg').textContent.trim(), 10);
      // localizar el h2 cuyo texto comienza igual (quitando " (continuación)")
      const base = titulo.replace(' (continuación)', '');
      const idx = paginas.findIndex(p => {
        const h2 = p.querySelector('h2');
        return h2 && h2.textContent.trim().startsWith(base.split('.')[0] + '.');
      });
      return { titulo, numIdx, real: idx >= 0 ? idx + 1 : null };
    });
    return filas;
  });

  // 3) capturas de páginas clave
  const fs = await import('fs');
  const dir = 'C:/Users/rodri/Documents/COPIGABY_MANUAL/shots/revision';
  fs.mkdirSync(dir, { recursive: true });
  for (const i of [4, 6, 9, 10]) { // 0-based: pág 5 (dashboard), 7 (clientes), 10 (productos), 11
    await page.locator('div.pg').nth(i).screenshot({ path: `${dir}/chk-${String(i + 1).padStart(2, '0')}.png` });
  }
  return { artefactos, cheq };
}
