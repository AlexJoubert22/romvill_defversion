<?php
/**
 * Template: Agenda de llamadas — reserva de llamada por expediente
 *
 * Con ?ref= válida: chip del expediente + calendario de 3 pasos
 * (día → hora → teléfono) en el idioma del expediente (_rv_lang).
 * Sin ref (o inválida): variante amable «Cuéntenos qué necesita»
 * hacia info@. Nunca un rechazo.
 *
 * @package Romvill
 */

// ── Resolver el expediente ANTES de get_header() para fijar el idioma ──
$rv_ag_ref = isset( $_GET['ref'] ) ? sanitize_text_field( wp_unslash( $_GET['ref'] ) ) : '';
$rv_ag_sol = ( $rv_ag_ref !== '' && function_exists( 'romvill_agenda_solicitud_por_ref' ) )
	? romvill_agenda_solicitud_por_ref( $rv_ag_ref )
	: 0;
if ( $rv_ag_sol && ! isset( $_GET['lang'] ) ) {
	$rv_ag_l = (string) get_post_meta( $rv_ag_sol, '_rv_lang', true );
	if ( in_array( $rv_ag_l, ROMVILL_LANGS, true ) ) {
		// Toda la página (textos romvill_t) hereda el idioma del expediente.
		$_GET['lang'] = $rv_ag_l;
	}
}

// Body class para ocultar la navegación y el pie del sitio.
add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'rv-ag-page';
	return $classes;
} );

add_action( 'wp_head', function () { ?>
<style>
/* ── Ocultar el chrome del sitio en la agenda ── */
.rv-ag-page nav[role=navigation],
.rv-ag-page .mobile-menu,
.rv-ag-page footer { display: none !important; }
.rv-ag-page .group\/design-root { padding-top: 0; }
.rv-ag-page { background:#FCFBF9 !important; }

/* ── Maqueta aprobada (paleta tinta/oro/crema) ── */
.rv-ag { font-family:-apple-system,'Segoe UI',Calibri,Arial,sans-serif; background:#FCFBF9; color:#333b47; min-height:100vh; padding-bottom:48px; }
.rv-ag * { box-sizing:border-box; }
.rv-ag-head { background:#000000; text-align:center; padding:26px 18px 20px; }
.rv-ag-head .nm { color:#fff; font-weight:800; font-size:20px; letter-spacing:7px; text-indent:7px; }
.rv-ag-head .sb { color:#C9A653; font-size:11px; letter-spacing:3px; text-transform:uppercase; margin-top:6px; }
.rv-ag-gold { height:3px; background:#C9A653; }
.rv-ag-wrap { max-width:520px; margin:0 auto; padding:24px 16px; }
.rv-ag h1 { font-family:Georgia,serif; font-size:23px; color:#000000; margin:0 0 6px; font-weight:700; line-height:1.3; }
.rv-ag-sub { font-size:14.5px; color:#5c6b80; margin:0 0 18px; line-height:1.6; }
.rv-ag-exp { display:inline-block; background:#000000; color:#fff; font-size:12px; font-weight:800; letter-spacing:1px; padding:5px 12px; border-radius:999px; margin-bottom:20px; text-transform:uppercase; }
.rv-ag-lbl { font-size:12px; font-weight:800; letter-spacing:1.6px; text-transform:uppercase; color:#8a6b18; margin:18px 0 8px; }
.rv-ag-dias { display:flex; gap:8px; overflow-x:auto; padding-bottom:4px; }
.rv-ag-dia { flex:0 0 auto; text-align:center; border:1.5px solid #e4e1d8; background:#fff; border-radius:12px; padding:10px 0; width:72px; cursor:pointer; }
.rv-ag-dia .dsem { font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#8a919c; }
.rv-ag-dia .dnum { font-size:20px; font-weight:800; color:#000000; margin:2px 0; }
.rv-ag-dia .dmes { font-size:11px; color:#8a919c; }
.rv-ag-dia.sel { border-color:#000000; background:#000000; }
.rv-ag-dia.sel .dsem, .rv-ag-dia.sel .dmes { color:#C9A653; }
.rv-ag-dia.sel .dnum { color:#fff; }
.rv-ag-horas { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
.rv-ag-hora { border:1.5px solid #e4e1d8; background:#fff; border-radius:10px; padding:11px 0; text-align:center; font-size:15px; font-weight:700; color:#000000; cursor:pointer; }
.rv-ag-hora.sel { background:#000000; color:#C9A653; border-color:#000000; }
.rv-ag-hora.off { opacity:.35; text-decoration:line-through; cursor:default; }
.rv-ag input[type=text], .rv-ag input[type=tel], .rv-ag textarea { width:100%; border:1.5px solid #e4e1d8; border-radius:10px; padding:13px 14px; font-size:16px; font-family:inherit; background:#fff; color:#000000; outline:none; }
.rv-ag input:focus, .rv-ag textarea:focus { border-color:#000000; }
.rv-ag textarea { min-height:96px; resize:vertical; }
.rv-ag-btn { display:block; width:100%; background:#000000; color:#fff; border:0; border-radius:10px; padding:16px; font-size:16px; font-weight:800; letter-spacing:.5px; margin-top:20px; font-family:inherit; cursor:pointer; }
.rv-ag-btn[disabled] { opacity:.45; cursor:not-allowed; }
.rv-ag-btn small { display:block; font-weight:600; font-size:12px; color:#C9A653; margin-top:3px; letter-spacing:1px; text-transform:uppercase; }
.rv-ag-nota { font-size:12.5px; color:#8a919c; text-align:center; margin-top:14px; line-height:1.6; }
.rv-ag-err { font-size:13px; color:#a13333; text-align:center; margin-top:12px; min-height:18px; line-height:1.5; }
.rv-ag-card { background:#fff; border:1px solid #e4e1d8; border-radius:12px; padding:18px 16px; margin-bottom:6px; }
.rv-ag-card .clbl { font-size:11px; font-weight:800; letter-spacing:2px; text-transform:uppercase; color:#8a6b18; margin-bottom:6px; }
.rv-ag-card .cbig { font-size:19px; font-weight:800; color:#000000; line-height:1.4; }
.rv-ag-card .csub { font-size:13.5px; color:#5c6b80; margin-top:4px; line-height:1.6; }
.rv-ag-link { display:inline-block; margin-top:12px; background:none; border:1.5px solid #000000; border-radius:999px; color:#000000; font-family:inherit; font-size:13px; font-weight:700; padding:9px 18px; cursor:pointer; }
.rv-ag-okic { width:52px; height:52px; border-radius:50%; background:#166B42; color:#fff; font-size:26px; line-height:52px; text-align:center; margin:0 auto 14px; }
.rv-ag-okwrap { text-align:center; padding:26px 0 6px; }
.rv-ag-rgpd { display:flex; align-items:flex-start; gap:10px; margin-top:14px; font-size:13px; color:#5c6b80; line-height:1.55; }
.rv-ag-rgpd input { width:18px; height:18px; margin-top:1px; flex:0 0 auto; }
.rv-ag-hide { display:none !important; }
</style>
<?php } );

get_header();
$_lang = romvill_current_lang();
romvill_seo( array() );

// ── Datos del expediente (si lo hay) ──
$rv_ag_nombre = $rv_ag_sol ? (string) get_post_meta( $rv_ag_sol, '_rv_nombre', true ) : '';
$rv_ag_tel    = $rv_ag_sol ? (string) get_post_meta( $rv_ag_sol, '_rv_tel', true ) : '';
if ( $rv_ag_tel === '—' ) $rv_ag_tel = '';
$rv_ag_nombre_bonito = ( $rv_ag_nombre !== '' && $rv_ag_nombre !== '—' && function_exists( 'romvill_mail_cliente_nombre' ) )
	? romvill_mail_cliente_nombre( $rv_ag_nombre ) : '';
$rv_ag_trat = trim( romvill_t( 'agenda.trat' ) );
$rv_ag_chip = trim( romvill_t( 'agenda.chip.exp' ) . ' ' . $rv_ag_ref
	. ( $rv_ag_nombre_bonito !== '' ? ' · ' . trim( $rv_ag_trat . ' ' . $rv_ag_nombre_bonito ) : '' ) );

// ── Reserva actual (una reserva viva por expediente) ──
$rv_ag_prev_f = $rv_ag_sol ? (string) get_post_meta( $rv_ag_sol, '_rv_llamada_fecha', true ) : '';
$rv_ag_prev_h = $rv_ag_sol ? (string) get_post_meta( $rv_ag_sol, '_rv_llamada_hora', true ) : '';
$rv_ag_prev_t = $rv_ag_sol ? (string) get_post_meta( $rv_ag_sol, '_rv_llamada_tel', true ) : '';
$rv_ag_tiene_reserva = ( $rv_ag_prev_f !== '' && $rv_ag_prev_h !== '' );
$rv_ag_prev_leg = $rv_ag_tiene_reserva ? romvill_agenda_fecha_legible( $rv_ag_prev_f, $_lang ) : '';

// ── Días disponibles + franjas ocupadas ──
$rv_ag_dias = array();
if ( $rv_ag_sol ) {
	$abbr = romvill_agenda_abreviaturas( $_lang );
	$ocup = romvill_agenda_ocupadas( $rv_ag_sol );
	foreach ( romvill_agenda_dias_disponibles() as $fecha => $horas ) {
		$ts = strtotime( $fecha . ' 12:00:00' );
		$rv_ag_dias[] = array(
			'f'    => $fecha,
			'sem'  => $abbr['sem'][ (int) date( 'N', $ts ) ],
			'num'  => (int) date( 'j', $ts ),
			'mes'  => $abbr['mes'][ (int) date( 'n', $ts ) ],
			'leg'  => romvill_agenda_fecha_legible( $fecha, $_lang ),
			'h'    => array_values( $horas ),
			'o'    => isset( $ocup[ $fecha ] ) ? array_values( array_unique( $ocup[ $fecha ] ) ) : array(),
		);
	}
}
?>

<div class="rv-ag">
	<div class="rv-ag-head">
		<div class="nm">ROMVILL</div>
		<div class="sb"><?php echo esc_html( romvill_t( 'agenda.head' ) ); ?></div>
	</div>
	<div class="rv-ag-gold"></div>

<?php if ( $rv_ag_sol ) : ?>

	<div class="rv-ag-wrap">
		<span class="rv-ag-exp"><?php echo esc_html( $rv_ag_chip ); ?></span>

		<!-- Reserva actual (si la hay) -->
		<div id="rv-ag-actual" class="<?php echo $rv_ag_tiene_reserva ? '' : 'rv-ag-hide'; ?>">
			<h1><?php echo esc_html( romvill_t( 'agenda.actual.lbl' ) ); ?></h1>
			<div class="rv-ag-card" style="margin-top:14px">
				<div class="clbl"><?php echo esc_html( romvill_t( 'agenda.actual.cita' ) ); ?></div>
				<div class="cbig" id="rv-ag-actual-txt"><?php echo esc_html( $rv_ag_prev_leg . ' · ' . $rv_ag_prev_h ); ?></div>
				<div class="csub"><?php echo esc_html( romvill_t( 'agenda.actual.body' ) . ' ' . $rv_ag_prev_t ); ?></div>
				<button type="button" class="rv-ag-link" id="rv-ag-btn-cambiar"><?php echo esc_html( romvill_t( 'agenda.cambiar' ) ); ?></button>
			</div>
		</div>

		<!-- Selector de 3 pasos -->
		<div id="rv-ag-picker" class="<?php echo $rv_ag_tiene_reserva ? 'rv-ag-hide' : ''; ?>">
			<h1><?php echo esc_html( romvill_t( 'agenda.title' ) ); ?></h1>
			<p class="rv-ag-sub"><?php echo esc_html( romvill_t( 'agenda.sub' ) ); ?></p>

			<div class="rv-ag-lbl">1 · <?php echo esc_html( romvill_t( 'agenda.step1' ) ); ?></div>
			<div class="rv-ag-dias" id="rv-ag-dias"></div>

			<div class="rv-ag-lbl">2 · <?php echo esc_html( romvill_t( 'agenda.step2' ) ); ?></div>
			<div class="rv-ag-horas" id="rv-ag-horas"></div>

			<div class="rv-ag-lbl">3 · <?php echo esc_html( romvill_t( 'agenda.step3' ) ); ?></div>
			<input type="tel" id="rv-ag-tel" placeholder="<?php echo esc_attr( romvill_t( 'agenda.tel.ph' ) ); ?>" value="<?php echo esc_attr( $rv_ag_tel ); ?>" autocomplete="tel">

			<button type="button" class="rv-ag-btn" id="rv-ag-btn" disabled>
				<span id="rv-ag-btn-txt"><?php echo esc_html( romvill_t( 'agenda.btn' ) ); ?></span>
				<small id="rv-ag-btn-sum"><?php echo esc_html( romvill_t( 'agenda.btn.pick' ) ); ?></small>
			</button>
			<div class="rv-ag-err" id="rv-ag-err"></div>
			<p class="rv-ag-nota"><?php echo esc_html( romvill_t( 'agenda.nota' ) ); ?></p>
		</div>

		<!-- Confirmación -->
		<div id="rv-ag-ok" class="rv-ag-hide">
			<div class="rv-ag-okwrap">
				<div class="rv-ag-okic">&#10003;</div>
				<h1><?php echo esc_html( romvill_t( 'agenda.ok.title' ) ); ?></h1>
				<div class="rv-ag-card" style="margin-top:14px;text-align:left">
					<div class="clbl"><?php echo esc_html( romvill_t( 'agenda.actual.cita' ) ); ?></div>
					<div class="cbig" id="rv-ag-ok-cita"></div>
					<div class="csub" id="rv-ag-ok-tel"></div>
				</div>
				<p class="rv-ag-sub" style="margin-top:12px"><?php echo esc_html( romvill_t( 'agenda.ok.body' ) ); ?></p>
			</div>
		</div>
	</div>

	<script>
	(function(){
		var DIAS = <?php echo wp_json_encode( $rv_ag_dias ); ?>;
		var AJAX = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
		var NONCE = '<?php echo esc_js( wp_create_nonce( 'romvill_agenda_nonce' ) ); ?>';
		var REF = '<?php echo esc_js( $rv_ag_ref ); ?>';
		var T = {
			btn: '<?php echo esc_js( romvill_t( 'agenda.btn' ) ); ?>',
			pick: '<?php echo esc_js( romvill_t( 'agenda.btn.pick' ) ); ?>',
			sending: '<?php echo esc_js( romvill_t( 'agenda.enviando' ) ); ?>',
			errTel: '<?php echo esc_js( romvill_t( 'agenda.err.tel' ) ); ?>',
			errEnvio: '<?php echo esc_js( romvill_t( 'agenda.err.envio' ) ); ?>',
			llamamos: '<?php echo esc_js( romvill_t( 'agenda.actual.body' ) ); ?>'
		};
		var selDia = null, selHora = null;
		var $dias = document.getElementById('rv-ag-dias');
		var $horas = document.getElementById('rv-ag-horas');
		var $tel = document.getElementById('rv-ag-tel');
		var $btn = document.getElementById('rv-ag-btn');
		var $sum = document.getElementById('rv-ag-btn-sum');
		var $err = document.getElementById('rv-ag-err');

		function renderDias(){
			$dias.innerHTML = '';
			DIAS.forEach(function(d, i){
				var el = document.createElement('div');
				el.className = 'rv-ag-dia' + (i === selDia ? ' sel' : '');
				el.innerHTML = '<div class="dsem"></div><div class="dnum"></div><div class="dmes"></div>';
				el.children[0].textContent = d.sem;
				el.children[1].textContent = d.num;
				el.children[2].textContent = d.mes;
				el.addEventListener('click', function(){ selDia = i; selHora = null; renderDias(); renderHoras(); update(); });
				$dias.appendChild(el);
			});
		}
		function renderHoras(){
			$horas.innerHTML = '';
			if (selDia === null) return;
			var d = DIAS[selDia];
			d.h.forEach(function(h){
				var off = d.o.indexOf(h) !== -1;
				var el = document.createElement('div');
				el.className = 'rv-ag-hora' + (off ? ' off' : '') + (h === selHora ? ' sel' : '');
				el.textContent = h;
				if (!off) el.addEventListener('click', function(){ selHora = h; renderHoras(); update(); });
				$horas.appendChild(el);
			});
		}
		function telOk(){ return ($tel.value.replace(/\D/g,'').length >= 7); }
		function update(){
			var listo = (selDia !== null && selHora !== null && telOk());
			$btn.disabled = !listo;
			$sum.textContent = (selDia !== null && selHora !== null)
				? (DIAS[selDia].leg + ' · ' + selHora).toUpperCase()
				: T.pick;
		}
		$tel.addEventListener('input', update);

		$btn.addEventListener('click', function(){
			if ($btn.disabled) return;
			$err.textContent = '';
			if (!telOk()) { $err.textContent = T.errTel; return; }
			$btn.disabled = true;
			document.getElementById('rv-ag-btn-txt').textContent = T.sending;
			var data = new FormData();
			data.append('action', 'romvill_agenda_reservar');
			data.append('nonce', NONCE);
			data.append('ref', REF);
			data.append('fecha', DIAS[selDia].f);
			data.append('hora', selHora);
			data.append('tel', $tel.value.trim());
			fetch(AJAX, { method:'POST', body:data })
				.then(function(r){ return r.json(); })
				.then(function(res){
					if (res && res.success) {
						document.getElementById('rv-ag-ok-cita').textContent = DIAS[selDia].leg + ' · ' + selHora;
						document.getElementById('rv-ag-ok-tel').textContent = T.llamamos + ' ' + $tel.value.trim();
						document.getElementById('rv-ag-picker').classList.add('rv-ag-hide');
						document.getElementById('rv-ag-actual').classList.add('rv-ag-hide');
						document.getElementById('rv-ag-ok').classList.remove('rv-ag-hide');
						window.scrollTo(0, 0);
					} else {
						$err.textContent = (res && res.data && res.data.message) ? res.data.message : T.errEnvio;
						if (res && res.data && res.data.code === 'ocupada' && selDia !== null) {
							// Marcar la franja como ocupada y deseleccionarla.
							DIAS[selDia].o.push(selHora);
							selHora = null;
							renderHoras();
						}
						document.getElementById('rv-ag-btn-txt').textContent = T.btn;
						update();
					}
				})
				.catch(function(){
					$err.textContent = T.errEnvio;
					document.getElementById('rv-ag-btn-txt').textContent = T.btn;
					update();
				});
		});

		var $cambiar = document.getElementById('rv-ag-btn-cambiar');
		if ($cambiar) $cambiar.addEventListener('click', function(){
			document.getElementById('rv-ag-actual').classList.add('rv-ag-hide');
			document.getElementById('rv-ag-picker').classList.remove('rv-ag-hide');
		});

		renderDias(); renderHoras(); update();
	})();
	</script>

<?php else : /* ── Variante amable sin referencia: nunca un rechazo ── */ ?>

	<div class="rv-ag-wrap">
		<div id="rv-ag-form">
			<h1><?php echo esc_html( romvill_t( 'agenda.sinref.title' ) ); ?></h1>
			<p class="rv-ag-sub"><?php echo esc_html( romvill_t( 'agenda.sinref.sub' ) ); ?></p>

			<div class="rv-ag-lbl"><?php echo esc_html( romvill_t( 'agenda.form.nombre' ) ); ?></div>
			<input type="text" id="rv-agf-nombre" autocomplete="name">

			<div class="rv-ag-lbl"><?php echo esc_html( romvill_t( 'agenda.form.tel' ) ); ?></div>
			<input type="tel" id="rv-agf-tel" placeholder="<?php echo esc_attr( romvill_t( 'agenda.tel.ph' ) ); ?>" autocomplete="tel">

			<div class="rv-ag-lbl"><?php echo esc_html( romvill_t( 'agenda.form.msg' ) ); ?></div>
			<textarea id="rv-agf-msg"></textarea>

			<label class="rv-ag-rgpd">
				<input type="checkbox" id="rv-agf-rgpd">
				<span><?php echo esc_html( romvill_t( 'agenda.form.rgpd' ) ); ?></span>
			</label>

			<button type="button" class="rv-ag-btn" id="rv-agf-btn"><?php echo esc_html( romvill_t( 'agenda.form.btn' ) ); ?></button>
			<div class="rv-ag-err" id="rv-agf-err"></div>
		</div>

		<div id="rv-agf-ok" class="rv-ag-hide">
			<div class="rv-ag-okwrap">
				<div class="rv-ag-okic">&#10003;</div>
				<h1><?php echo esc_html( romvill_t( 'agenda.form.ok' ) ); ?></h1>
				<p class="rv-ag-sub" style="margin-top:10px"><?php echo esc_html( romvill_t( 'agenda.form.ok.body' ) ); ?></p>
			</div>
		</div>
	</div>

	<script>
	(function(){
		var AJAX = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
		var NONCE = '<?php echo esc_js( wp_create_nonce( 'romvill_agenda_nonce' ) ); ?>';
		var $btn = document.getElementById('rv-agf-btn');
		var $err = document.getElementById('rv-agf-err');
		var TXT_BTN = $btn.textContent;
		var SENDING = '<?php echo esc_js( romvill_t( 'agenda.enviando' ) ); ?>';
		var ERR = '<?php echo esc_js( romvill_t( 'agenda.form.err' ) ); ?>';
		$btn.addEventListener('click', function(){
			$err.textContent = '';
			var nombre = document.getElementById('rv-agf-nombre').value.trim();
			var tel = document.getElementById('rv-agf-tel').value.trim();
			var rgpd = document.getElementById('rv-agf-rgpd').checked;
			if (!nombre || tel.replace(/\D/g,'').length < 7 || !rgpd) { $err.textContent = ERR; return; }
			$btn.disabled = true; $btn.textContent = SENDING;
			var data = new FormData();
			data.append('action', 'romvill_agenda_contacto');
			data.append('nonce', NONCE);
			data.append('nombre', nombre);
			data.append('tel', tel);
			data.append('mensaje', document.getElementById('rv-agf-msg').value.trim());
			data.append('rgpd', '1');
			fetch(AJAX, { method:'POST', body:data })
				.then(function(r){ return r.json(); })
				.then(function(res){
					if (res && res.success) {
						document.getElementById('rv-ag-form').classList.add('rv-ag-hide');
						document.getElementById('rv-agf-ok').classList.remove('rv-ag-hide');
						window.scrollTo(0, 0);
					} else {
						$err.textContent = (res && res.data && res.data.message) ? res.data.message : ERR;
						$btn.disabled = false; $btn.textContent = TXT_BTN;
					}
				})
				.catch(function(){ $err.textContent = ERR; $btn.disabled = false; $btn.textContent = TXT_BTN; });
		});
	})();
	</script>

<?php endif; ?>
</div>

<?php get_footer(); ?>
