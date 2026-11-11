export default async function run(page) {
  await page.goto('http://localhost:8012/COPIGABY-2023-C.A/?url=login', { waitUntil: 'networkidle' });
  await page.fill('input[name="username"]', 'ADMIN');
  await page.fill('input[name="password"]', '1234');
  await page.click('button:has-text("Ingresar al Sistema")');
  await page.waitForURL('**url=dashboard*', { timeout: 15000 });
  await page.waitForLoadState('networkidle');
  await page.click('#btnAyudaManual');
  await page.waitForSelector('.swal2-popup', { timeout: 10000 });
  await page.waitForTimeout(600);
  const texto = await page.locator('.swal2-html-container').innerText();
  await page.screenshot({ path: 'C:/Users/rodri/Documents/COPIGABY_MANUAL/shots/modal-ayuda-v2.png' });
  return { texto };
}
