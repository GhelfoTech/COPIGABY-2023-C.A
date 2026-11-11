export default async function run(page) {
  await page.setViewportSize({ width: 794, height: 1123 });
  await page.goto('file:///C:/Users/rodri/Documents/COPIGABY_MANUAL/manual.html', { waitUntil: 'networkidle' });
  await page.waitForTimeout(1500);
  const dir = 'C:/Users/rodri/Documents/COPIGABY_MANUAL/shots/revision';
  const fs = await import('fs');
  fs.mkdirSync(dir, { recursive: true });
  const n = await page.locator('.page').count();
  for (let i = 0; i < n; i++) {
    await page.locator('.page').nth(i).screenshot({ path: `${dir}/pg-${String(i + 1).padStart(2, '0')}.png` });
  }
  return 'ok';
}
