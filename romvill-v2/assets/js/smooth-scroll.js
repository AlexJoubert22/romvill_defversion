/* ============================================================================
   ROMVILL v2 — SCROLL SUAVE

   Interpolación del desplazamiento para dar la sensación de inercia que se
   espera hoy en una web de este nivel.

   Decisiones, porque este componente es fácil de hacer mal:

   · Se mueve el scroll REAL (window.scrollTo), no un contenedor con
     transform. Así siguen funcionando position:sticky, position:fixed, los
     anclas del navegador y la barra de desplazamiento. La alternativa
     (translate sobre un wrapper) es más suave pero rompe las tres cosas.
   · Solo en puntero fino. En táctil el sistema operativo ya tiene inercia
     propia y sobreescribirla se nota mal.
   · prefers-reduced-motion lo desactiva por completo.
   · Se apaga solo cuando hay un modal o el menú abierto (body sin scroll).
   · Cualquier scroll que no venga de la rueda (teclado, barra, anclas,
     scrollIntoView) resincroniza el objetivo en vez de pelearse con él.

   El factor de interpolación es deliberadamente alto (0.14): el objetivo es
   suavizar, no arrastrar. Un valor bajo produce esa sensación de goma que
   hace que la página parezca lenta.
   ========================================================================= */

const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)');
const FINE = window.matchMedia('(pointer: fine)');
const LERP = 0.14;

let target = 0;
let current = 0;
let raf = 0;
let activo = false;
/* Última posición que hemos fijado nosotros. Se compara con scrollY para
   saber si un evento de scroll lo hemos provocado nosotros o alguien más.
   Un booleano NO vale: window.scrollTo emite el evento de forma asíncrona y
   con varios fotogramas seguidos la bandera se desincroniza; el resultado
   era que onScroll reseteaba el objetivo a mitad de la interpolación y la
   página dejaba de moverse. */
let ultimoFijado = -1;

const max = () => Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
const clamp = (v) => Math.min(Math.max(v, 0), max());

function fijar(y) {
  ultimoFijado = Math.round(y);
  window.scrollTo(0, y);
}

function tick() {
  const delta = target - current;
  if (Math.abs(delta) < 0.4) {
    current = target;
    fijar(current);
    raf = 0;
    return;
  }
  current += delta * LERP;
  fijar(current);
  raf = requestAnimationFrame(tick);
}

function arrancar() {
  if (!raf) raf = requestAnimationFrame(tick);
}

function onWheel(e) {
  // Con Ctrl es zoom del navegador; no se toca.
  if (e.ctrlKey || !activo) return;
  // Si el puntero está sobre algo con scroll propio (tabla ancha, modal),
  // se deja pasar el evento.
  //
  // `nodeType === 1` no es paranoia: el objetivo de un evento de rueda no
  // siempre es un elemento —puede ser el propio `window` o el documento— y
  // `getComputedStyle` lanza con cualquier otra cosa. Al lanzar aquí no se
  // llegaba a `preventDefault()` y el desplazamiento suave se quedaba muerto
  // para ese gesto sin dejar más rastro que una excepción en consola.
  for (let el = e.target; el && el.nodeType === 1 && el !== document.body; el = el.parentElement) {
    const cs = getComputedStyle(el);
    if (/auto|scroll/.test(cs.overflowY) && el.scrollHeight > el.clientHeight + 2) return;
    if (/auto|scroll/.test(cs.overflowX) && el.scrollWidth > el.clientWidth + 2) return;
  }
  e.preventDefault();
  target = clamp(target + e.deltaY * (e.deltaMode === 1 ? 18 : 1));
  arrancar();
}

/* Si el scroll lo provoca otra cosa (teclado, barra, ancla, scrollIntoView),
   el objetivo se resincroniza: si no, la interpolación tiraría de vuelta. */
function onScroll() {
  // Si la posición es la que acabamos de fijar (±2 px), el scroll es nuestro.
  if (Math.abs(window.scrollY - ultimoFijado) <= 2) return;
  target = window.scrollY;
  current = window.scrollY;
}

function activar() {
  if (activo) return;
  target = current = ultimoFijado = window.scrollY;
  activo = true;
  window.addEventListener('wheel', onWheel, { passive: false });
  window.addEventListener('scroll', onScroll, { passive: true });
}

function desactivar() {
  if (!activo) return;
  activo = false;
  cancelAnimationFrame(raf); raf = 0;
  window.removeEventListener('wheel', onWheel);
  window.removeEventListener('scroll', onScroll);
}

export function init() {
  const permitido = () => FINE.matches && !REDUCED.matches;
  if (permitido()) activar();

  REDUCED.addEventListener('change', () => (permitido() ? activar() : desactivar()));
  FINE.addEventListener('change', () => (permitido() ? activar() : desactivar()));

  /* Con modal o menú abierto el body se bloquea: la interpolación no debe
     seguir empujando por detrás. */
  const obs = new MutationObserver(() => {
    const bloqueado = document.body.style.overflow === 'hidden';
    if (bloqueado) desactivar();
    else if (permitido()) activar();
  });
  obs.observe(document.body, { attributes: true, attributeFilter: ['style'] });

  window.addEventListener('resize', () => { target = clamp(target); }, { passive: true });
}

export default { init };
