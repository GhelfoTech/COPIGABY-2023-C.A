export default async function run(page) {
  await page.setViewportSize({ width: 794, height: 1123 }); // A4 a 96dpi
  await page.goto('file:///C:/Users/rodri/Documents/COPIGABY_MANUAL/manual.html', { waitUntil: 'networkidle' });
  await page.waitForTimeout(1500);
  const n = await page.locator('.page').count();
  const alturas = [];
  for (let i = 0; i < n; i++) {
    const box = await page.locator('.page').nth(i).boundingBox();
    alturas.push(Math.round(box.height));
  }
  return { n, alturas }; // hoja A4 = 1123px; si altura>1123 la página desborda
}
