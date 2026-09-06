/* ROMVILL v2 — muestra de informe
   Capítulos plegables. Es el mismo patrón que el acordeón general, pero con
   su propia clase porque el bloque abierto reajusta padding y dispara la
   animación de los gráficos (las barras y las curvas solo se animan cuando
   el capítulo está realmente visible). */

import motion from '../motion.js';

const chapters = [...document.querySelectorAll('.chapter')];

chapters.forEach((ch, i) => {
  const btn = ch.querySelector('.chapter__btn');
  btn.addEventListener('click', () => {
    const open = ch.classList.contains('is-open');
    ch.classList.toggle('is-open', !open);
    btn.setAttribute('aria-expanded', String(!open));

    // Al abrirse por primera vez se trazan las curvas SVG del capítulo.
    if (!open) {
      ch.querySelectorAll('[data-draw]').forEach((el) => {
        if (el.classList.contains('is-in')) return;
        if (typeof el.getTotalLength === 'function') {
          try { el.style.setProperty('--draw-len', Math.ceil(el.getTotalLength())); } catch { /* sin geometría */ }
        }
        // El retardo por línea da la sensación de que el gráfico se dibuja.
        requestAnimationFrame(() => el.classList.add('is-in'));
      });
    }
  });

  // El primer capítulo se abre solo al llegar a él: el visitante ve de
  // inmediato que esto se puede abrir, sin tener que adivinarlo.
  if (i === 0) {
    const io = new IntersectionObserver(([e]) => {
      if (!e.isIntersecting) return;
      io.disconnect();
      setTimeout(() => { if (!ch.classList.contains('is-open')) btn.click(); }, motion.reduced ? 0 : 900);
    }, { threshold: 0.35 });
    io.observe(ch);
  }
});
