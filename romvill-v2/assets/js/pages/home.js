/* ROMVILL v2 — portada
   Dos gestos: el héroe se hunde ligeramente al bajar y la maqueta del informe
   se inclina siguiendo al puntero. Ambos se apagan con prefers-reduced-motion. */

import motion from '../motion.js';

/* El vídeo del héroe se desplaza más despacio que el texto: da profundidad
   sin el efecto barato de un parallax marcado. */
function heroDepth() {
  const media = document.querySelector('.hero__media');
  const content = document.querySelector('.hero__content');
  if (!media || motion.reduced) return;

  motion.onFrame((y, vh) => {
    if (y > vh * 1.2) return;                 // fuera de pantalla: nada que hacer
    const p = Math.min(y / vh, 1);
    media.style.transform = `translate3d(0, ${(p * 12).toFixed(2)}%, 0) scale(${(1 + p * 0.06).toFixed(4)})`;
    if (content) {
      content.style.transform = `translate3d(0, ${(p * -28).toFixed(1)}px, 0)`;
      content.style.opacity = String(Math.max(1 - p * 1.25, 0));
    }
  });
}

/* Inclinación de la maqueta del informe. */
function reportTilt() {
  const wrap = document.querySelector('.work__mock');
  const card = wrap && wrap.querySelector('.report');
  if (!card || motion.reduced || window.matchMedia('(hover: none)').matches) return;
  if (window.innerWidth < 1000) return;

  const BASE_Y = -9, BASE_X = 3;
  let raf = 0;

  wrap.addEventListener('pointermove', (e) => {
    const r = wrap.getBoundingClientRect();
    const dx = (e.clientX - r.left) / r.width - 0.5;
    const dy = (e.clientY - r.top) / r.height - 0.5;
    cancelAnimationFrame(raf);
    raf = requestAnimationFrame(() => {
      card.style.transform =
        `rotateY(${(BASE_Y + dx * 12).toFixed(2)}deg) rotateX(${(BASE_X - dy * 10).toFixed(2)}deg)`;
    });
  });
  wrap.addEventListener('pointerleave', () => {
    cancelAnimationFrame(raf);
    card.style.transform = `rotateY(${BASE_Y}deg) rotateX(${BASE_X}deg)`;
  });
}

heroDepth();
reportTilt();
