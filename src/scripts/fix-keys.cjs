const fs = require('fs');
let s = fs.readFileSync('src/scripts/build-pdf.mjs', 'utf8');
let cambios = 0;
s = s.replace(/mk\('MODIFICAR', 1, 0, 4\)/g, () => { cambios++; return "mk('MODIFICAR', 1)"; });
s = s.replace(/mk\('ELIMINAR', 2, 0, 4\)/g, () => { cambios++; return "mk('ELIMINAR', 2)"; });
fs.writeFileSync('src/scripts/build-pdf.mjs', s);
console.log('sustituciones:', cambios);
