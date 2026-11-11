export default async function run(page, ui) {
  const dir = 'C:/Users/rodri/Documents/COPIGABY_MANUAL/shots';
  const fs = await import('fs');
  fs.mkdirSync(dir, { recursive: true });

  await page.setViewportSize({ width: 1440, height: 900 });
  await page.goto('http://localhost:8012/COPIGABY-2023-C.A/?url=login', { waitUntil: 'networkidle' });
  await page.screenshot({ path: `${dir}/01-login.png`, fullPage: false });

  // Iniciar sesión como admin
  await page.fill('input[name="username"]', 'ADMIN');
  await page.fill('input[name="password"]', '1234');
  await page.click('button:has-text("Ingresar al Sistema")');
  await page.waitForURL('**url=dashboard*', { timeout: 15000 });
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: `${dir}/02-dashboard.png`, fullPage: true });

  return { ok: true, url: page.url() };
}
