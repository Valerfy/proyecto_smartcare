<?php
$modo    = $modo    ?? 'login';
$old     = $old     ?? [];
$errores = $errores ?? [];
$error   = $error   ?? '';
$esReg   = ($modo === 'register');
$registroOk = (($_GET['registro'] ?? '') === 'ok');

$e = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

/** Dibuja un campo con icono, mensaje de error y (opcional) ojo / medidor de contraseña. */
$campo = function (string $form, string $name, string $label, string $icon, array $o = []) use ($old, $errores, $e) {
    $type  = $o['type'] ?? 'text';
    $id    = "$form-$name";
    $msg   = $errores[$name] ?? '';
    $value = $type === 'password' ? '' : ($old[$name] ?? '');
    $persist = ($type !== 'password' && ($o['persist'] ?? true)) ? 'data-persist' : '';
    $req   = ($o['required'] ?? true) ? ' <span class="req">*</span>' : '';
    echo '<div class="field' . ($msg ? ' is-invalid' : '') . '">';
    echo '<label for="' . $id . '">' . $label . $req . '</label><div class="input"><i class="bi ' . $icon . ' ico"></i>';
    echo '<input id="' . $id . '" name="' . $name . '" type="' . $type . '" value="' . $e($value) . '" ' . $persist . ' ' . ($o['attrs'] ?? '') . '>';
    if ($type === 'password') {
        echo '<button type="button" class="eye" data-eye="' . $id . '" aria-label="Mostrar u ocultar contraseña"><i class="bi bi-eye"></i></button>';
    }
    echo '</div>';
    if (!empty($o['meter'])) echo '<div class="meter" data-s="0"><span></span><span></span><span></span><span></span></div><div class="meter-txt"></div>';
    if (!empty($o['hint'])) echo '<div class="hint">' . $e($o['hint']) . '</div>';
    echo '<div class="msg">' . $e($msg) . '</div></div>';
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - <?= $esReg ? 'Crear cuenta' : 'Iniciar sesión' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="../assets/img/logo_cns.png">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body class="auth-body">
<div class="auth-card <?= $esReg ? 'is-register' : '' ?>" id="card" data-clear="<?= $registroOk ? '1' : '0' ?>">

    <!-- ============ LOGIN ============ -->
    <section class="auth-panel auth-panel--login" id="panel-login" <?= $esReg ? 'inert aria-hidden="true"' : '' ?>>
      <div class="auth-inner">
        <div class="brand"><img src="../assets/img/logo_cns.png" alt="Logo CNS"><span><b>SmartCare CNS</b><small>Caja Nacional de Salud</small></span></div>
        <p class="eyebrow">Tu salud en un solo lugar</p>
        <h1>Bienvenido de nuevo</h1>
        <p class="sub">Inicia sesión para gestionar tus citas médicas.</p>

        <?php if ($registroOk): ?>
            <div class="alert ok"><i class="bi bi-check-circle-fill"></i><span>Registro exitoso. Ya puedes iniciar sesión.</span></div>
        <?php endif; ?>
        <?php if (!$esReg && $error): ?>
            <div class="alert err"><i class="bi bi-exclamation-triangle-fill"></i><span><?= $e($error) ?></span></div>
        <?php endif; ?>

        <form method="POST" action="index.php?controller=auth&action=login" data-form="login" novalidate autocomplete="on">
            <?php
            $campo('login', 'correo', 'Correo electrónico', 'bi-envelope', ['type' => 'email', 'attrs' => 'placeholder="tucorreo@ejemplo.com" autocomplete="username" maxlength="120"']);
            $campo('login', 'clave', 'Contraseña', 'bi-lock', ['type' => 'password', 'attrs' => 'placeholder="Tu contraseña" autocomplete="current-password"']);
            ?>
            <button type="submit" class="btn-main">Iniciar sesión</button>
            <p class="alt">¿No tienes cuenta? <a href="index.php?controller=auth&action=register" data-switch="register">Regístrate</a></p>
        </form>
      </div>
    </section>

    <!-- ============ REGISTRO ============ -->
    <section class="auth-panel auth-panel--register" id="panel-register" <?= $esReg ? '' : 'inert aria-hidden="true"' ?>>
      <div class="auth-inner">
        <div class="brand"><img src="../assets/img/logo_cns.png" alt="Logo CNS"><span><b>SmartCare CNS</b><small>Caja Nacional de Salud</small></span></div>
        <p class="eyebrow">Empieza en minutos</p>
        <h1>Crea tu cuenta</h1>
        <p class="sub">Completa tus datos para agendar citas sin filas.</p>

        <?php if ($esReg && $error): ?>
            <div class="alert err"><i class="bi bi-exclamation-triangle-fill"></i><span><?= $e($error) ?></span></div>
        <?php endif; ?>

        <form method="POST" action="index.php?controller=auth&action=register" enctype="multipart/form-data" data-form="register" novalidate autocomplete="on">
            <div class="grid2">
                <?php
                $campo('register', 'nombre', 'Nombre', 'bi-person', ['attrs' => 'placeholder="Ada" maxlength="40" autocomplete="given-name"']);
                $campo('register', 'apellido', 'Apellido', 'bi-person', ['attrs' => 'placeholder="Lovelace" maxlength="40" autocomplete="family-name"']);
                $campo('register', 'ci', 'CI', 'bi-card-text', ['attrs' => 'placeholder="1234567" maxlength="13" inputmode="text"', 'hint' => 'Ej: 1234567 o 1234567-1A']);
                $campo('register', 'telefono', 'Celular', 'bi-telephone', ['type' => 'tel', 'attrs' => 'placeholder="71234567" maxlength="8" inputmode="numeric" autocomplete="tel-national"', 'hint' => '8 dígitos, empieza con 6 o 7']);
                $campo('register', 'fecha_nac', 'Fecha de nacimiento', 'bi-calendar-event', ['type' => 'date', 'attrs' => 'min="' . date('Y-m-d', strtotime('-110 years')) . '" max="' . date('Y-m-d', strtotime('-18 years')) . '" autocomplete="bday"', 'hint' => 'Debes ser mayor de 18 años']);
                $campo('register', 'ocupacion', 'Ocupación', 'bi-briefcase', ['attrs' => 'placeholder="Docente, Ingeniero…" maxlength="50"']);
                ?>
            </div>
            <?php
            $campo('register', 'direccion', 'Dirección', 'bi-geo-alt', ['attrs' => 'placeholder="Calle Murillo 123, La Paz" maxlength="150" autocomplete="street-address"']);
            $campo('register', 'correo', 'Correo electrónico', 'bi-envelope', ['type' => 'email', 'attrs' => 'placeholder="tucorreo@ejemplo.com" maxlength="120" autocomplete="email"']);
            $campo('register', 'matricula', 'Número de matrícula', 'bi-card-list', ['attrs' => 'placeholder="Número de matrícula" maxlength="20"', 'hint' => '3 a 20 letras, números o guiones']);
            $campo('register', 'clave', 'Contraseña', 'bi-lock', ['type' => 'password', 'meter' => true, 'attrs' => 'placeholder="Mínimo 8 caracteres" maxlength="72" autocomplete="new-password"']);
            $campo('register', 'clave2', 'Confirmar contraseña', 'bi-shield-lock', ['type' => 'password', 'attrs' => 'placeholder="Repite tu contraseña" maxlength="72" autocomplete="new-password"']);
            ?>
            <div class="field<?= isset($errores['foto']) ? ' is-invalid' : '' ?>">
                <label for="register-foto">Foto de perfil (opcional)</label>
                <div class="input"><i class="bi bi-image ico"></i>
                    <input id="register-foto" name="foto" type="file" accept="image/jpeg,image/png">
                </div>
                <div class="hint">JPG o PNG, máximo 2 MB. Por seguridad, la foto no se conserva al refrescar la página.</div>
                <div class="msg"><?= $e($errores['foto'] ?? '') ?></div>
                <div class="preview" id="preview"><img id="img-preview" alt="Vista previa"></div>
            </div>
            <div class="field<?= isset($errores['terminos']) ? ' is-invalid' : '' ?>">
                <label class="check"><input type="checkbox" name="terminos" value="1" data-persist <?= !empty($old['terminos']) ? 'checked' : '' ?>>
                    <span>Acepto los Términos y Condiciones y la Política de Privacidad</span></label>
                <div class="msg"><?= $e($errores['terminos'] ?? '') ?></div>
            </div>
            <button type="submit" class="btn-main">Crear cuenta</button>
            <p class="alt">¿Ya tienes una cuenta? <a href="index.php?controller=auth&action=login" data-switch="login">Inicia sesión</a></p>
        </form>
      </div>
    </section>

    <!-- ============ PANEL AZUL DESLIZANTE ============ -->
    <aside class="auth-overlay" aria-hidden="false">
        <div class="ov ov--login">
            <div class="glass"><img src="../assets/img/logo_cns.png" alt="CNS"><span class="chip c1"><i class="bi bi-calendar2-check"></i></span><span class="chip c2"><i class="bi bi-shield-check"></i></span></div>
            <p class="eyebrow">Hazlo tuyo</p>
            <h2>¿Primera vez aquí?</h2>
            <p>Crea tu cuenta de paciente, agenda citas y consulta tu historial desde cualquier lugar.</p>
            <a class="btn-ghost" href="index.php?controller=auth&action=register" data-switch="register">Crear cuenta <i class="bi bi-arrow-right"></i></a>
            <small>Gratis · Tus datos están protegidos</small>
        </div>
        <div class="ov ov--register">
            <div class="glass"><img src="../assets/img/logo_cns.png" alt="CNS"><span class="chip c1"><i class="bi bi-arrow-left-right"></i></span><span class="chip c2"><i class="bi bi-clock-history"></i></span></div>
            <p class="eyebrow">Qué bueno verte</p>
            <h2>¿Ya tienes cuenta?</h2>
            <p>Tus citas y tu historial están donde los dejaste. Inicia sesión y continúa.</p>
            <a class="btn-ghost" href="index.php?controller=auth&action=login" data-switch="login"><i class="bi bi-arrow-left"></i> Iniciar sesión</a>
            <small>Acceso seguro · Siempre sincronizado</small>
        </div>
    </aside>
</div>

<script>
(function () {
  'use strict';
  const card = document.getElementById('card');
  const KEY = 'sc_auth_draft_v1';
  const L = 'A-Za-zÀ-ÿÑñ';
  const nameRe = new RegExp('^[' + L + ']+(?: [' + L + ']+)*$');
  const occRe  = new RegExp('^[' + L + ' .,\\-]+$');
  const mailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  const blocked = ['@cns.com', '@cns.org.bo', '@cns.rec.bo'];

  /* ---------- Reglas por formulario (devuelven '' si es válido) ---------- */
  const email = v => !v ? 'Ingresa tu correo.' : v.length > 120 || !mailRe.test(v) ? 'Correo electrónico inválido.' : '';
  const person = (n) => v => !v ? 'Ingresa tu ' + n + '.' : v.length < 2 || v.length > 40 ? 'Entre 2 y 40 caracteres.' : !nameRe.test(v) ? 'Solo letras y espacios.' : '';
  const rules = {
    login: {
      correo: email,
      clave: v => v ? '' : 'Ingresa tu contraseña.'
    },
    register: {
      nombre: person('nombre'),
      apellido: person('apellido'),
      ci: v => !v ? 'Ingresa tu CI.' : !/^\d{5,9}(-[0-9A-Z]{1,2})?$/.test(v) ? 'CI inválido (ej: 1234567 o 1234567-1A).' : '',
      telefono: v => !v ? 'Ingresa tu celular.' : !/^[67]\d{7}$/.test(v) ? 'Debe tener 8 dígitos y empezar con 6 o 7.' : '',
      fecha_nac: (v, f) => {
        if (!v) return 'Ingresa tu fecha de nacimiento.';
        const el = f.elements.fecha_nac;
        if (isNaN(Date.parse(v))) return 'Fecha inválida.';
        if (v > el.max) return 'Debes ser mayor de 18 años.';
        if (v < el.min) return 'Fecha de nacimiento no válida.';
        return '';
      },
      ocupacion: v => !v ? 'Ingresa tu ocupación.' : v.length < 3 || v.length > 50 || !occRe.test(v) ? 'Ocupación inválida (3 a 50 letras).' : '',
      direccion: v => !v ? 'Ingresa tu dirección.' : v.length < 5 || v.length > 150 ? 'Entre 5 y 150 caracteres.' : '',
      correo: v => {
        const m = email(v); if (m) return m;
        return blocked.some(d => v.toLowerCase().endsWith(d)) ? 'No puedes usar un correo institucional.' : '';
      },
      matricula: v => !v ? 'Ingresa tu número de matrícula.' : !/^[A-Za-z0-9\-]{3,20}$/.test(v) ? '3 a 20 letras, números o guiones.' : '',
      clave: v => v.length < 8 ? 'Mínimo 8 caracteres.' : v.length > 72 ? 'Máximo 72 caracteres.' : !/[A-Za-z]/.test(v) || !/\d/.test(v) ? 'Incluye al menos una letra y un número.' : '',
      clave2: (v, f) => !v ? 'Confirma tu contraseña.' : v !== f.elements.clave.value ? 'Las contraseñas no coinciden.' : '',
      foto: (v, f) => {
        const file = f.elements.foto.files[0]; if (!file) return '';
        if (!['image/jpeg', 'image/png'].includes(file.type)) return 'La foto debe ser JPG o PNG.';
        return file.size > 2 * 1024 * 1024 ? 'La foto no debe superar 2 MB.' : '';
      },
      terminos: (v, f) => f.elements.terminos.checked ? '' : 'Debes aceptar los términos y condiciones.'
    }
  };

  /* ---------- Mostrar / limpiar error de un campo ---------- */
  function setState(input, msg) {
    const wrap = input.closest('.field'); if (!wrap) return;
    wrap.classList.toggle('is-invalid', !!msg);
    wrap.classList.toggle('is-valid', !msg && input.type !== 'checkbox' && input.type !== 'file' && input.value.trim() !== '');
    const box = wrap.querySelector('.msg'); if (box) box.textContent = msg;
  }
  function validate(input) {
    const form = input.form, fn = rules[form.dataset.form][input.name]; if (!fn) return true;
    const v = input.type === 'password' ? input.value : input.value.trim();
    const msg = fn(v, form); setState(input, msg); return !msg;
  }

  /* ---------- Borrador: los datos NO se pierden al refrescar ---------- */
  function readDraft() { try { return JSON.parse(sessionStorage.getItem(KEY) || '{}'); } catch (_) { return {}; } }
  function saveDraft(input) {
    try {
      const d = readDraft(), k = input.form.dataset.form + '.' + input.name;
      d[k] = input.type === 'checkbox' ? input.checked : input.value;
      sessionStorage.setItem(KEY, JSON.stringify(d));
    } catch (_) { /* almacenamiento no disponible: se ignora */ }
  }
  if (card.dataset.clear === '1') { try { sessionStorage.removeItem(KEY); } catch (_) {} }
  const draft = readDraft();
  document.querySelectorAll('[data-persist]').forEach(el => {
    const k = el.form.dataset.form + '.' + el.name;
    if (!(k in draft)) return;
    if (el.type === 'checkbox') { if (!el.checked && draft[k] === true) el.checked = true; }
    else if (el.value === '' && draft[k]) el.value = draft[k];
  });

  /* ---------- Eventos de campos ---------- */
  document.querySelectorAll('form[data-form]').forEach(form => {
    form.querySelectorAll('input').forEach(input => {
      input.addEventListener('input', () => {
        if (input.name === 'telefono') input.value = input.value.replace(/\D/g, '').slice(0, 8);
        if (input.name === 'ci') input.value = input.value.toUpperCase().replace(/[^0-9A-Z\-]/g, '');
        if (input.dataset.touched || input.closest('.field').classList.contains('is-invalid')) validate(input);
        if (input.name === 'clave') { meter(input); const c2 = form.elements.clave2; if (c2 && c2.dataset.touched) validate(c2); }
        if (input.hasAttribute('data-persist')) saveDraft(input);
      });
      input.addEventListener('blur', () => {
        if (['nombre', 'apellido'].includes(input.name)) input.value = input.value.trim().replace(/\s+/g, ' ');
        if (input.value !== '' || input.dataset.touched) { input.dataset.touched = '1'; validate(input); }
      });
      if (input.type === 'checkbox' || input.type === 'file' || input.type === 'date')
        input.addEventListener('change', () => { input.dataset.touched = '1'; validate(input); if (input.hasAttribute('data-persist')) saveDraft(input); });
    });

    form.addEventListener('submit', ev => {
      let first = null;
      form.querySelectorAll('input').forEach(i => { i.dataset.touched = '1'; if (!validate(i) && !first) first = i; });
      if (first) {
        ev.preventDefault(); first.focus({ preventScroll: false });
        form.classList.remove('shake'); void form.offsetWidth; form.classList.add('shake');
        return;
      }
      const btn = form.querySelector('.btn-main');
      btn.disabled = true; btn.textContent = form.dataset.form === 'login' ? 'Ingresando…' : 'Creando cuenta…';
    });
  });
  window.addEventListener('pageshow', () => document.querySelectorAll('.btn-main').forEach(b => {
    b.disabled = false; b.textContent = b.closest('form').dataset.form === 'login' ? 'Iniciar sesión' : 'Crear cuenta';
  }));

  /* ---------- Ojo de contraseña y medidor de fortaleza ---------- */
  document.querySelectorAll('[data-eye]').forEach(b => b.addEventListener('click', () => {
    const i = document.getElementById(b.dataset.eye), show = i.type === 'password';
    i.type = show ? 'text' : 'password';
    b.firstElementChild.className = 'bi ' + (show ? 'bi-eye-slash' : 'bi-eye');
  }));
  function meter(input) {
    const wrap = input.closest('.field'), bar = wrap.querySelector('.meter'); if (!bar) return;
    const v = input.value; let s = 0;
    if (v.length >= 8) s++; if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++; if (/\d/.test(v)) s++; if (/[^A-Za-z0-9]/.test(v) || v.length >= 12) s++;
    if (!v) s = 0;
    bar.dataset.s = s;
    wrap.querySelector('.meter-txt').textContent = v ? ['', 'Débil', 'Regular', 'Buena', 'Fuerte'][s] : '';
  }

  /* ---------- Vista previa de la foto ---------- */
  const foto = document.getElementById('register-foto');
  foto.addEventListener('change', () => {
    const box = document.getElementById('preview'), f = foto.files[0];
    if (f && rules.register.foto(null, foto.form) === '') {
      const r = new FileReader();
      r.onload = e => { document.getElementById('img-preview').src = e.target.result; box.style.display = 'block'; };
      r.readAsDataURL(f);
    } else { box.style.display = 'none'; }
  });

  /* ---------- Cambio de panel con animación ---------- */
  function setMode(mode, animate) {
    const reg = mode === 'register';
    card.classList.remove('anim-left', 'anim-right'); void card.offsetWidth;
    card.classList.toggle('is-register', reg);
    if (animate) card.classList.add(reg ? 'anim-left' : 'anim-right');
    const pl = document.getElementById('panel-login'), pr = document.getElementById('panel-register');
    pl.toggleAttribute('inert', reg); pl.setAttribute('aria-hidden', reg);
    pr.toggleAttribute('inert', !reg); pr.setAttribute('aria-hidden', !reg);
    document.title = 'SmartCare CNS - ' + (reg ? 'Crear cuenta' : 'Iniciar sesión');
    history.replaceState(null, '', 'index.php?controller=auth&action=' + (reg ? 'register' : 'login'));  // al refrescar se queda en el mismo panel
    const first = (reg ? pr : pl).querySelector('input:not([type=hidden])');
    setTimeout(() => first && first.focus({ preventScroll: true }), animate ? 650 : 0);
  }
  document.querySelectorAll('[data-switch]').forEach(a => a.addEventListener('click', ev => {
    ev.preventDefault(); setMode(a.dataset.switch, true);
  }));
})();
</script>
</body>
</html>
