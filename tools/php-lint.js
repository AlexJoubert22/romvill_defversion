// Analiza sintacticamente archivos PHP con php-parser (equivalente a `php -l`).
const fs = require('fs');
const path = require('path');
const engine = require('php-parser');
const parser = new engine({ parser: { extractDoc: false, suppressErrors: false }, ast: { withPositions: true } });

let fallos = 0;
for (const f of process.argv.slice(2)) {
  const src = fs.readFileSync(f, 'utf8');
  try {
    const ast = parser.parseCode(src, f);
    const errs = (ast.errors || []);
    if (errs.length) {
      fallos++;
      console.log(`  FALLO  ${path.basename(f)}`);
      errs.slice(0, 3).forEach(e => console.log(`         linea ${e.line}: ${e.message}`));
    } else {
      console.log(`  OK     ${path.basename(f)}`);
    }
  } catch (e) {
    fallos++;
    console.log(`  FALLO  ${path.basename(f)}`);
    console.log(`         ${e.message.split('\n')[0]}`);
  }
}
console.log(fallos ? `\n  ${fallos} archivo(s) con errores de sintaxis` : `\n  Sintaxis correcta en los ${process.argv.length - 2} archivos`);
process.exit(fallos ? 1 : 0);

/* ── Cómo se usa ────────────────────────────────────────────────────────────
   Esta máquina no tiene PHP, así que `php -l` no está disponible y un error
   de sintaxis tumbaría romvill.com en el mismo push (main autodespliega).
   php-parser analiza PHP desde Node y da el mismo veredicto.

     npm install                       (una vez, trae php-parser)
     node tools/php-lint.js $(git ls-files '*.php')

   SIEMPRE antes de pushear PHP.
   ------------------------------------------------------------------------ */
