const h = require('fs').readFileSync('C:/Users/rodri/Documents/COPIGABY_MANUAL/shots/manual.html', 'utf8');
console.log('svg:', (h.match(/<svg/g) || []).length);
console.log('polyline:', (h.match(/polyline/g) || []).length);
console.log('mk-a:', h.includes('mk-a'));
const i = h.indexOf('mk-n"');
console.log(h.slice(i - 180, i + 120));
