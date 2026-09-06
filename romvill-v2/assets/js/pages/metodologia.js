/* ROMVILL v2 — metodología · registro de campo

   Cinco comprobaciones, cinco fotografías. Al elegir una fila se cruza su
   fotografía y se despliega su descripción.

   Accesibilidad: las filas son botones reales con `aria-pressed`, navegables
   con tabulador y con flechas. Nada depende del ratón.

   La descripción se envuelve en un `<span>` al arrancar porque el desplegado
   va con `grid-template-rows: 0fr → 1fr`, y esa técnica necesita un hijo con
   `overflow: hidden` que colapse. Se hace aquí y no en la plantilla para que
   el `data-t` siga apuntando al elemento que rellena el build: si el `<span>`
   estuviera en el HTML, el texto del diccionario entraría dentro y la
   auditoría de copy dejaría de reconocer el nodo. */

const field = document.getElementById('field');

if (field) {
  const items = [...field.querySelectorAll('.fitem')];
  const shots = [...field.querySelectorAll('.field__shot')];
  const stamp = field.querySelector('[data-f-stamp]');

  /* El texto llega ya inyectado por el build; aquí solo se le pone la caja
     que necesita la animación de altura. */
  items.forEach((it) => {
    const i = it.querySelector('.fitem__body i');
    if (i && !i.firstElementChild) {
      const box = document.createElement('span');
      box.textContent = i.textContent;
      i.textContent = '';
      i.appendChild(box);
    }
  });

  const total = String(items.length).padStart(2, '0');

  const elegir = (key) => {
    items.forEach((it) => it.setAttribute('aria-pressed', String(it.dataset.f === key)));
    shots.forEach((sh) => sh.toggleAttribute('data-on', sh.dataset.fShot === key));
    const i = items.findIndex((it) => it.dataset.f === key);
    if (stamp && i >= 0) stamp.textContent = String(i + 1).padStart(2, '0') + ' / ' + total;
  };

  items.forEach((it) => {
    it.addEventListener('click', () => elegir(it.dataset.f));
    it.addEventListener('focus', () => elegir(it.dataset.f));
    /* Con el ratón basta pasar por encima: obligar a pulsar en una lista que
       ya se lee entera sería fricción sin premio. */
    it.addEventListener('pointerenter', (e) => {
      if (e.pointerType === 'mouse') elegir(it.dataset.f);
    });
  });

  field.addEventListener('keydown', (e) => {
    const i = items.indexOf(document.activeElement);
    if (i < 0) return;
    let next = null;
    if (e.key === 'ArrowDown' || e.key === 'ArrowRight') next = (i + 1) % items.length;
    if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') next = (i - 1 + items.length) % items.length;
    if (next === null) return;
    e.preventDefault();
    items[next].focus();
  });
}
