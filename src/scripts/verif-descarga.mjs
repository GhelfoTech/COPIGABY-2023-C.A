export default async function run(page, ui) {
  await page.goto('http://localhost:8012/COPIGABY-2023-C.A/?url=login', { waitUntil: 'networkidle' });
  await page.fill('input[name="username"]', 'ADMIN');
  await page.fill('input[name="password"]', '1234');
  await page.click('button:has-text("Ingresar al Sistema")');
  await page.waitForURL('**url=dashboard*', { timeout: 15000 });

  const resp = await page.request.get('http://localhost:8012/COPIGABY-2023-C.A/?url=ayuda&descargar=pdf');
  const body = await resp.body();
  const head = body.slice(0, 5).toString();
  await resp.dispose();
  return { status: resp.status(), bytes: body.length, firma: head, esPdf: head.startsWith('%PDF') };
}
