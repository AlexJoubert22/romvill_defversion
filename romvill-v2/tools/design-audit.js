/* ============================================================================
   ROMVILL v2 — AUDITORÍA DE DISEÑO (se ejecuta EN el navegador)
   Recorre todas las páginas en un iframe, a varios anchos y en ambos temas,
   y mide lo que el ojo no juzga bien a escala:

     · contraste real de cada texto contra su fondo pintado (WCAG)
     · desbordes horizontales y elementos que se salen
     · tamaños de letra por debajo del mínimo legible
     · longitud de línea (medida) fuera del rango cómodo
     · objetivos táctiles menores de 44 px
     · imágenes deformadas (ratio mostrado ≠ ratio natural)
     · saltos en la jerarquía de encabezados
     · elementos pegajosos que no pueden pegarse
     · solapamientos de elementos interactivos

   Se pega en la consola del navegador (o se ejecuta vía javascript_tool):
     await romvillAudit()
   ========================================================================= */

window.romvillAudit = async function romvillAudit(opts = {}) {
  const PAGES = opts.pages || [
    'index', 'metodologia', 'analisis', 'sectores', 'precios', 'quienes-somos',
    'preguntas-frecuentes', 'contacto', 'muestra-de-informe', 'agendar-llamada',
    'presupuesto-bloque-1', 'perfil-seguridad', 'perfil-demografico',
    'perfil-sanidad', 'perfil-movilidad', 'perfil-proyeccion',
    'analisis-marbella', 'analisis-malaga', 'analisis-alicante',
    'privacidad', 'terminos', 'aviso-legal', '404',
  ];
  const WIDTHS = opts.widths || [375, 768, 1280, 1600];
  const THEMES = opts.themes || ['light', 'dark'];

  /* ── Utilidades de color ─────────────────────────────────────────────── */
  const parse = (c) => {
    const m = String(c).match(/rgba?\(([^)]+)\)/);
    if (!m) return null;
    const p = m[1].split(/[,\s/]+/).filter(Boolean).map(Number);
    return { r: p[0], g: p[1], b: p[2], a: p[3] == null ? 1 : p[3] };
  };
  const lin = (v) => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); };
  const lum = (c) => 0.2126 * lin(c.r) + 0.7152 * lin(c.g) + 0.0722 * lin(c.b);
  const over = (fg, bg) => ({
    r: fg.r * fg.a + bg.r * (1 - fg.a),
    g: fg.g * fg.a + bg.g * (1 - fg.a),
    b: fg.b * fg.a + bg.b * (1 - fg.a),
    a: 1,
  });
  const ratio = (a, b) => {
    const l1 = lum(a), l2 = lum(b);
    return (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
  };

  /** Fondo efectivo: sube por los ancestros hasta encontrar un color opaco. */
  const bgOf = (el, win) => {
    let node = el, acc = null;
    while (node && node.nodeType === 1) {
      const c = parse(win.getComputedStyle(node).backgroundColor);
      if (c && c.a > 0) acc = acc ? over(acc, c) : c;
      if (acc && acc.a >= 0.99) return acc;
      node = node.parentElement;
    }
    return acc && acc.a > 0.99 ? acc : { r: 255, g: 255, b: 255, a: 1 };
  };

  /* ── Carga de una página en iframe ───────────────────────────────────── */
  const fr = document.createElement('iframe');
  fr.style.cssText = 'position:fixed;left:-99999px;top:0;border:0;height:900px';
  document.body.appendChild(fr);

  const load = (page, width, theme) => new Promise((done) => {
    fr.style.width = width + 'px';
    fr.onload = () => {
      const d = fr.contentDocument;

      /* Se anulan transiciones y animaciones ANTES de tocar el tema.
         El iframe vive fuera de pantalla y, cuando la pestana no esta en
         primer plano, Chrome congela las transiciones: `body` tiene
         `transition: background-color`, asi que al poner el tema oscuro el
         fondo se quedaba en el valor claro de partida y el auditor media
         texto blanco sobre #f8f9fc -> 1567 fallos de contraste falsos.
         Sin transiciones la medida es la del estado final y es
         reproducible este la pestana delante o detras. */
      const quieto = d.createElement('style');
      quieto.textContent =
        '*,*::before,*::after{transition:none !important;animation:none !important}';
      d.head.appendChild(quieto);

      if (theme === 'dark') d.documentElement.setAttribute('data-theme', 'dark');
      else d.documentElement.removeAttribute('data-theme');
      // Se fuerza la revelación para medir el estado final, no el de entrada.
      d.querySelectorAll('[data-reveal],[data-draw]').forEach((n) => {
        n.classList.add('is-in');
        n.removeAttribute('data-reveal');
        n.removeAttribute('data-draw');
      });
      setTimeout(() => done(d), 220);
    };
    /* El testigo tiene que cambiar EN CADA EJECUCION, no solo por ancho y
       tema. Con `?_=1280dark` fijo, la segunda pasada recibia el HTML
       cacheado de la primera —y ese HTML apunta al CSS con el sello VIEJO—,
       asi que el auditor seguia midiendo estilos ya corregidos y repetia los
       mismos fallos con las mismas cifras. Es el mismo error que ya obligo a
       sellar los assets, una capa mas arriba. */
    fr.src = page + '.html?audit=' + RUN + '&_=' + width + theme;
  });

  /* Identificador de esta ejecucion: invalida la cache de los iframes. */
  const RUN = Math.random().toString(36).slice(2, 9);

  const issues = [];
  const push = (page, width, theme, tipo, detalle, sel) =>
    issues.push({ page, width, theme, tipo, detalle, sel });

  const nameOf = (el) => {
    const id = el.id ? '#' + el.id : '';
    const cls = typeof el.className === 'string' && el.className
      ? '.' + el.className.trim().split(/\s+/).slice(0, 2).join('.') : '';
    return (el.tagName.toLowerCase() + id + cls).slice(0, 60);
  };

  /* ── Recorrido ───────────────────────────────────────────────────────── */
  for (const page of PAGES) {
    for (const width of WIDTHS) {
      for (const theme of THEMES) {
        let d;
        try { d = await load(page, width, theme); } catch { push(page, width, theme, 'carga', 'no carga', ''); continue; }
        const win = fr.contentWindow;
        const root = d.documentElement;

        /* 1 · Desborde horizontal */
        const ov = root.scrollWidth - width;
        if (ov > 1) push(page, width, theme, 'desborde', `scrollWidth supera el viewport en ${ov}px`, 'html');

        const all = [...d.querySelectorAll('body *')];

        /* Elementos que recortan con `clip-path: inset(...)`. Se recogen UNA
           vez por pagina: la primera version subia el arbol llamando a
           getComputedStyle por cada ancestro de cada texto, y con 500 nodos
           de texto por 184 combinaciones el auditor tardaba minutos de mas.
           En casi todas las paginas esta lista esta vacia y el bloque 2c ni
           se ejecuta. */
        const recortadores = all.filter((e) => {
          const cp = win.getComputedStyle(e).clipPath;
          return cp && cp !== 'none' && /^inset\(/.test(cp);
        });

        for (const el of all) {
          const cs = win.getComputedStyle(el);
          if (cs.display === 'none' || cs.visibility === 'hidden') continue;
          const r = el.getBoundingClientRect();
          if (!r.width || !r.height) continue;

          /* 2 · Elementos que se salen por la derecha (los contenidos por un
                 ancestro con overflow no cuentan) */
          if (r.right > width + 2) {
            let clipped = false;
            for (let p = el.parentElement; p; p = p.parentElement) {
              const pc = win.getComputedStyle(p);
              if (/hidden|clip|auto|scroll/.test(pc.overflowX)) { clipped = true; break; }
            }
            if (!clipped) push(page, width, theme, 'fuera-de-pantalla', `${nameOf(el)} llega a ${Math.round(r.right)}px`, nameOf(el));
          }

          /* 2b · Texto RECORTADO por un ancestro con overflow. La
                 comprobación anterior lo excluye a propósito (un carrusel
                 recorta por diseño), pero si lo que se recorta es texto se
                 pierden letras y es un fallo grave. Se mide contra la caja
                 del ancestro que recorta, no contra el viewport, y se
                 ignoran los ancestros que el usuario puede desplazar. */
          if ([...el.childNodes].some((n) => n.nodeType === 3 && n.textContent.trim().length > 1)) {
            for (let p = el.parentElement; p; p = p.parentElement) {
              const pc = win.getComputedStyle(p);
              if (!/hidden|clip|auto|scroll/.test(pc.overflowX)) continue;
              if (p.scrollWidth > p.clientWidth + 2) break;   // se puede desplazar: no se pierde nada
              const pr = p.getBoundingClientRect();
              const fuera = Math.max(r.right - pr.right, pr.left - r.left);
              if (fuera > 1) {
                push(page, width, theme, 'texto-recortado',
                  `${nameOf(el)} se sale ${Math.round(fuera)}px de ${nameOf(p)}, que recorta`, nameOf(el));
              }
              break;
            }
          }

          /* 2c · Texto recortado por un `clip-path: inset(...)`. La
                 comprobacion anterior solo mira `overflow`, y en este
                 proyecto el fallo ha aparecido DOS veces por la otra via:
                 el deslizante «lo que se ve / lo que sabemos» recorta su
                 mitad derecha con `clip-path`, y tanto las chapas de dato
                 como la etiqueta de la mitad quedaban partidas a media
                 palabra. Solo se evalua `inset()`, que es la unica forma
                 cuya region se puede calcular sin ambiguedad. */
          if (recortadores.length
              && [...el.childNodes].some((n) => n.nodeType === 3 && n.textContent.trim().length > 1)) {
            for (const p of recortadores) {
              if (!p.contains(el)) continue;
              const m = /^inset\(([^)]+)\)/.exec(win.getComputedStyle(p).clipPath);
              if (!m) break;
              const pr = p.getBoundingClientRect();
              const val = (t, base) => (t.endsWith('%') ? (parseFloat(t) / 100) * base : parseFloat(t) || 0);
              const t = m[1].trim().split(/\s+/);
              const [ti, ri, bi, li] = t.length === 1 ? [t[0], t[0], t[0], t[0]]
                : t.length === 2 ? [t[0], t[1], t[0], t[1]]
                : t.length === 3 ? [t[0], t[1], t[2], t[1]]
                : t;
              const caja = {
                top: pr.top + val(ti, pr.height), right: pr.right - val(ri, pr.width),
                bottom: pr.bottom - val(bi, pr.height), left: pr.left + val(li, pr.width),
              };
              const fuera = Math.max(caja.left - r.left, r.right - caja.right,
                                     caja.top - r.top, r.bottom - caja.bottom);
              // Solo cuenta si el texto se ve a MEDIAS: si esta fuera del todo
              // esta oculto a proposito, que es como funciona el deslizante.
              const dentro = r.right > caja.left && r.left < caja.right
                          && r.bottom > caja.top && r.top < caja.bottom;
              if (dentro && fuera > 1) {
                push(page, width, theme, 'texto-recortado',
                  `${nameOf(el)} se sale ${Math.round(fuera)}px del clip-path de ${nameOf(p)}`, nameOf(el));
              }
              break;
            }
          }

          /* 3 · Texto: contraste, tamaño y medida */
          const hasText = [...el.childNodes].some((n) => n.nodeType === 3 && n.textContent.trim().length > 1);
          if (hasText) {
            const fs = parseFloat(cs.fontSize);
            const fw = parseInt(cs.fontWeight, 10) || 400;
            const fg0 = parse(cs.color);
            if (fg0) {
              const bg = bgOf(el, win);
              const fg = fg0.a < 1 ? over(fg0, bg) : fg0;
              const cr = ratio(fg, bg);
              const grande = fs >= 24 || (fs >= 18.66 && fw >= 700);
              const min = grande ? 3 : 4.5;
              // Se ignora el texto sobre imagen/vídeo: el fondo calculado no es real.
              /* Exclusiones: (a) texto sobre imagen o vídeo — el fondo
                 calculado no es el que se ve; (b) la barra, que es
                 transparente sobre el héroe oscuro y sólida al hacer scroll,
                 con lo que ningún cálculo estático la representa; (c) todo lo
                 marcado aria-hidden, que es decoración y WCAG no evalúa. */
              const sobreMedia = !!el.closest(
                '.hero,.qshero,.zhero,.citycard,.split,.sl,.hotspots__scene,.chero,' +
                '.nav,.mobilemenu,.footer__watermark,[aria-hidden="true"]'
              );
              if (cr < min && !sobreMedia) {
                push(page, width, theme, 'contraste', `${cr.toFixed(2)}:1 (mín ${min}) · ${fs.toFixed(0)}px/${fw} · ${nameOf(el)} · "${el.textContent.trim().slice(0, 40)}"`, nameOf(el));
              }
            }
            if (fs < 11.5) push(page, width, theme, 'letra-pequena', `${fs.toFixed(1)}px en ${nameOf(el)}`, nameOf(el));

            // Medida de línea aproximada: ancho / ancho medio de carácter
            const chars = el.textContent.trim().length;
            if (chars > 90 && cs.display.includes('block')) {
              const ch = r.width / (fs * 0.5);
              if (ch > 88) push(page, width, theme, 'linea-larga', `~${Math.round(ch)} caracteres en ${nameOf(el)}`, nameOf(el));
            }
          }

          /* 4 · Objetivos táctiles */
          if (width <= 768 && /^(a|button)$/i.test(el.tagName) && el.offsetParent !== null) {
            const inline = cs.display === 'inline' || el.closest('p,li,.acc__a,.legal__list');
            if (!inline && (r.height < 44 || r.width < 24)) {
              push(page, width, theme, 'tactil', `${Math.round(r.width)}×${Math.round(r.height)} en ${nameOf(el)}`, nameOf(el));
            }
          }

          /* 5 · Imágenes deformadas */
          if (el.tagName === 'IMG' && el.naturalWidth && cs.objectFit !== 'cover' && cs.objectFit !== 'contain') {
            const natural = el.naturalWidth / el.naturalHeight;
            const shown = r.width / r.height;
            if (Math.abs(natural - shown) / natural > 0.04) {
              push(page, width, theme, 'imagen-deformada', `${nameOf(el)} natural ${natural.toFixed(2)} vs mostrado ${shown.toFixed(2)}`, nameOf(el));
            }
          }
        }

        /* 6 · Jerarquía de encabezados */
        if (width === 1280 && theme === 'light') {
          const hs = [...d.querySelectorAll('h1,h2,h3,h4,h5,h6')]
            .filter((h) => win.getComputedStyle(h).display !== 'none')
            .map((h) => ({ n: +h.tagName[1], t: h.textContent.trim().slice(0, 30) }));
          let prev = 0;
          hs.forEach((h) => {
            if (prev && h.n > prev + 1) push(page, width, theme, 'jerarquia', `salto h${prev} → h${h.n} ("${h.t}")`, 'h' + h.n);
            prev = h.n;
          });
          const h1 = hs.filter((h) => h.n === 1).length;
          if (h1 !== 1) push(page, width, theme, 'jerarquia', `${h1} elementos h1`, 'h1');
        }
      }
    }
  }

  fr.remove();

  /* ── Resumen ─────────────────────────────────────────────────────────── */
  const porTipo = {};
  issues.forEach((i) => { (porTipo[i.tipo] ||= []).push(i); });

  // Se colapsan los duplicados (mismo tipo + mismo detalle en varias páginas)
  const resumen = Object.entries(porTipo).map(([tipo, list]) => {
    const agrupado = {};
    list.forEach((i) => {
      const k = i.tipo + '|' + (i.sel || i.detalle) + '|' + i.detalle.replace(/"[^"]*"/, '').replace(/[0-9.]+/g, '#');
      (agrupado[k] ||= { detalle: i.detalle, paginas: new Set(), anchos: new Set(), temas: new Set() });
      agrupado[k].paginas.add(i.page);
      agrupado[k].anchos.add(i.width);
      agrupado[k].temas.add(i.theme);
    });
    return {
      tipo,
      total: list.length,
      casos: Object.values(agrupado).map((g) => ({
        detalle: g.detalle,
        paginas: g.paginas.size > 4 ? `${g.paginas.size} páginas` : [...g.paginas].join(', '),
        anchos: [...g.anchos].join('/'),
        temas: [...g.temas].join('/'),
      })).slice(0, 30),
    };
  }).sort((a, b) => b.total - a.total);

  return { totalIncidencias: issues.length, paginas: PAGES.length, combinaciones: PAGES.length * WIDTHS.length * THEMES.length, resumen };
};
'romvillAudit listo';
