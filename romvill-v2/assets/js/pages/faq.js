/* ROMVILL v2 — preguntas frecuentes
   Filtro en vivo y enlaces permanentes. El filtro compara sin acentos y sin
   distinguir mayúsculas: buscar "analisis" debe encontrar "análisis". */

const input = document.getElementById('faq-q');
const clear = document.getElementById('faq-clear');
const root = document.getElementById('faq');
const noresult = document.getElementById('faq-noresult');

const fold = (s) => s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');

if (input && root) {
  const items = [...root.querySelectorAll('.faq__item')];
  const cats = [...root.querySelectorAll('.faq__cat')];

  // Se indexa pregunta + respuesta; el atributo data-faq-text del build solo
  // trae la pregunta, así que se completa aquí con el texto del panel.
  const index = items.map((el) => ({
    el,
    text: fold(`${el.getAttribute('data-faq-text') || ''} ${el.querySelector('.acc__a')?.textContent || ''}`),
  }));

  const apply = () => {
    const q = fold(input.value.trim());
    clear.hidden = !input.value;

    if (!q) {
      index.forEach(({ el }) => { el.hidden = false; });
      cats.forEach((c) => { c.hidden = false; });
      noresult.hidden = true;
      return;
    }

    let hits = 0;
    index.forEach(({ el, text }) => {
      const on = text.includes(q);
      el.hidden = !on;
      if (on) hits++;
    });
    cats.forEach((c) => {
      c.hidden = ![...c.querySelectorAll('.faq__item')].some((i) => !i.hidden);
    });
    noresult.hidden = hits > 0;
  };

  let timer;
  input.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(apply, 110); });
  input.addEventListener('search', apply);
  clear.addEventListener('click', () => { input.value = ''; apply(); input.focus(); });
}

/* Enlace permanente: al copiarlo y volver, la pregunta se abre y se marca. */
function openFromHash() {
  const id = location.hash.slice(1);
  if (!id) return;
  const item = document.getElementById(id);
  if (!item || !item.classList.contains('faq__item')) return;
  item.classList.add('is-open', 'is-targeted');
  item.querySelector('.acc__btn')?.setAttribute('aria-expanded', 'true');
  item.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
window.addEventListener('hashchange', openFromHash);
openFromHash();

document.querySelectorAll('.faq__perma').forEach((a) => {
  a.addEventListener('click', (e) => {
    e.preventDefault();
    const url = new URL(location.href);
    url.hash = a.getAttribute('href').slice(1);
    history.replaceState(null, '', url);
    navigator.clipboard?.writeText(url.toString()).catch(() => {});
  });
});
