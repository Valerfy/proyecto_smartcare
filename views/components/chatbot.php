<style>
#sc-chat-btn,#sc-chat-window,#sc-chat-window *{box-sizing:border-box}
#sc-chat-btn{position:fixed;right:24px;bottom:24px;width:62px;height:62px;border:0;border-radius:50%;cursor:pointer;z-index:99998;
  display:flex;align-items:center;justify-content:center;color:#fff;background:linear-gradient(135deg,#0b6b57,#004840);
  box-shadow:0 10px 26px -6px rgba(0,72,64,.6);transition:transform .25s,box-shadow .25s}
#sc-chat-btn:hover{transform:scale(1.08)}
#sc-chat-btn:focus-visible,#sc-chat-window button:focus-visible,#sc-message:focus-visible{outline:3px solid #7dd3b8;outline-offset:2px}
#sc-chat-btn::after{content:"";position:absolute;inset:0;border-radius:50%;border:2px solid #0b6b57;animation:scRing 2.6s ease-out 3}
@keyframes scRing{0%{transform:scale(1);opacity:.7}100%{transform:scale(1.6);opacity:0}}
.sc-ic{width:22px;height:22px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0}
#sc-chat-btn .sc-ic{width:28px;height:28px}
#sc-badge{position:absolute;top:-3px;right:-3px;min-width:21px;height:21px;padding:0 5px;border-radius:11px;background:#dc2626;color:#fff;
  font:700 11px/21px Inter,system-ui,sans-serif;text-align:center;display:none;border:2px solid #fff}

#sc-chat-window{position:fixed;right:24px;bottom:98px;width:392px;height:min(620px,calc(100vh - 126px));display:none;flex-direction:column;
  overflow:hidden;z-index:99999;background:#fff;border-radius:22px;box-shadow:0 24px 60px -12px rgba(16,40,72,.45);
  font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;color:#0f172a}
#sc-chat-window.sc-open{display:flex;animation:scUp .22s ease}
@keyframes scUp{from{opacity:0;transform:translateY(14px) scale(.98)}to{opacity:1;transform:none}}
#sc-chat-window button{font-family:inherit}

#sc-chat-header{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:14px 16px;color:#fff;flex-shrink:0;
  background:repeating-linear-gradient(0deg,rgba(255,255,255,.05) 0 5px,transparent 5px 11px),linear-gradient(135deg,#102848,#004840 55%,#0b6b57)}
.sc-id{display:flex;align-items:center;gap:11px;min-width:0}
.sc-avatar{width:44px;height:44px;border-radius:50%;background:#fff;padding:2px;flex-shrink:0;box-shadow:0 0 0 3px rgba(255,255,255,.25)}
.sc-avatar img{width:100%;height:100%;border-radius:50%;display:block}
.sc-id strong{display:block;font-size:15px;line-height:1.2}
.sc-id small{display:flex;align-items:center;gap:6px;font-size:12px;opacity:.85;margin-top:2px}
.sc-dot{width:8px;height:8px;border-radius:50%;background:#4ade80;animation:scPulse 2s infinite}
@keyframes scPulse{0%,100%{opacity:1}50%{opacity:.35}}
.sc-actions{display:flex;gap:6px}
.sc-actions button{width:34px;height:34px;border:0;border-radius:50%;background:rgba(255,255,255,.14);color:#fff;cursor:pointer;
  display:flex;align-items:center;justify-content:center;transition:background .2s}
.sc-actions button:hover{background:rgba(255,255,255,.3)}
.sc-actions .sc-ic{width:17px;height:17px}

#sc-quick-btns{display:flex;gap:6px;padding:9px 12px;overflow-x:auto;background:#eef5f3;border-bottom:1px solid #dbe7e3;flex-shrink:0;scrollbar-width:none}
#sc-quick-btns::-webkit-scrollbar{display:none}
.sc-qbtn{flex-shrink:0;border:1px solid #bcd9d0;background:#fff;color:#0b6b57;border-radius:20px;padding:6px 12px;font-size:12.5px;font-weight:500;
  cursor:pointer;white-space:nowrap;transition:all .18s}
.sc-qbtn:hover{background:#0b6b57;border-color:#0b6b57;color:#fff}

#sc-body-wrap{position:relative;flex:1;min-height:0;display:flex}
#sc-chat-body{flex:1;overflow-y:auto;padding:16px 14px 8px;background:#f6f9f8;display:flex;flex-direction:column;gap:2px;scroll-behavior:smooth;scrollbar-width:thin}
.sc-row{display:flex;flex-direction:column;margin-bottom:8px;animation:scIn .2s ease}
@keyframes scIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
.sc-row-user{align-items:flex-end}.sc-row-bot{align-items:flex-start}
.sc-user,.sc-bot{max-width:86%;padding:10px 14px;font-size:14px;line-height:1.55;word-break:break-word}
.sc-user{background:linear-gradient(135deg,#0b6b57,#004840);color:#fff;border-radius:18px 18px 4px 18px;white-space:pre-wrap}
.sc-bot{background:#fff;color:#1e293b;border-radius:18px 18px 18px 4px;box-shadow:0 2px 8px rgba(15,23,42,.08)}
.sc-bot a{color:#0b6b57;font-weight:600}
.sc-bot ul,.sc-bot ol{margin:6px 0;padding-left:20px}
.btn-chat-link{display:inline-block;margin-top:8px;padding:7px 15px;background:#0b6b57;color:#fff!important;border-radius:9px;text-decoration:none;font-size:13px;font-weight:600;transition:background .2s}
.btn-chat-link:hover{background:#004840}
.sc-time{font-size:10.5px;color:#94a3b8;margin-top:3px;padding:0 4px}
.sc-error .sc-bot{background:#fef2f2;color:#991b1b}
.sc-retry{margin-top:8px;border:1px solid #991b1b;background:#fff;color:#991b1b;border-radius:8px;padding:5px 12px;font-size:12.5px;font-weight:600;cursor:pointer}
.sc-retry:hover{background:#991b1b;color:#fff}

.sc-typing{display:inline-flex;align-items:center;gap:4px;background:#fff;padding:13px 16px;border-radius:18px 18px 18px 4px;box-shadow:0 2px 8px rgba(15,23,42,.08)}
.sc-typing span{width:8px;height:8px;border-radius:50%;background:#94a3b8;animation:scBounce 1.2s infinite}
.sc-typing span:nth-child(2){animation-delay:.2s}.sc-typing span:nth-child(3){animation-delay:.4s}
@keyframes scBounce{0%,60%,100%{transform:translateY(0)}30%{transform:translateY(-6px)}}

#sc-down{position:absolute;left:50%;bottom:10px;transform:translateX(-50%) translateY(8px);width:34px;height:34px;border:0;border-radius:50%;
  background:#fff;color:#0b6b57;box-shadow:0 4px 14px rgba(15,23,42,.25);cursor:pointer;display:flex;align-items:center;justify-content:center;
  opacity:0;pointer-events:none;transition:opacity .2s,transform .2s}
#sc-down.sc-show{opacity:1;pointer-events:auto;transform:translateX(-50%)}

#sc-confirm{position:absolute;inset:0;z-index:3;display:none;align-items:center;justify-content:center;padding:24px;background:rgba(15,23,42,.45);backdrop-filter:blur(2px)}
#sc-confirm.sc-show{display:flex}
.sc-confirm-box{background:#fff;border-radius:16px;padding:20px;text-align:center;max-width:280px;box-shadow:0 18px 40px rgba(0,0,0,.3)}
.sc-confirm-box p{margin:0 0 16px;font-size:14px;line-height:1.5}
.sc-confirm-box div{display:flex;gap:8px;justify-content:center}
.sc-confirm-box button{border:0;border-radius:10px;padding:9px 16px;font-size:13.5px;font-weight:600;cursor:pointer}
#sc-confirm-no{background:#e2e8f0;color:#0f172a}
#sc-confirm-yes{background:#dc2626;color:#fff}

#sc-chat-footer{display:flex;align-items:flex-end;gap:8px;padding:10px 12px calc(10px + env(safe-area-inset-bottom,0px));background:#fff;border-top:1px solid #e2e8f0;flex-shrink:0;position:relative}
#sc-message{flex:1;border:1.5px solid #e2e8f0;border-radius:22px;padding:10px 16px;font:inherit;font-size:14px;line-height:1.4;color:#0f172a;background:#f6f9f8;
  resize:none;height:42px;max-height:110px;overflow-y:auto;transition:border-color .2s,background .2s}
#sc-message:focus{outline:none;border-color:#0b6b57;background:#fff}
#sc-send{width:42px;height:42px;border:0;border-radius:50%;flex-shrink:0;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;
  background:linear-gradient(135deg,#0b6b57,#004840);transition:transform .2s,box-shadow .2s,background .2s}
#sc-send:hover:not(:disabled){transform:scale(1.08);box-shadow:0 6px 14px -4px rgba(0,72,64,.6)}
#sc-send:disabled{background:#cbd5e1;cursor:not-allowed}
#sc-send .sc-ic{width:19px;height:19px}
#sc-counter{position:absolute;right:66px;top:-18px;font-size:11px;color:#64748b;background:#fff;padding:1px 7px;border-radius:8px;display:none}
#sc-counter.sc-show{display:block}
.sc-foot-note{font-size:10.5px;text-align:center;color:#94a3b8;padding:0 12px 8px;background:#fff;flex-shrink:0}

@media (max-width:480px){
  #sc-chat-window{inset:0;width:100%;height:100vh;height:100dvh;border-radius:0}
  #sc-chat-btn{right:16px;bottom:16px}
}
@media (prefers-reduced-motion:reduce){
  #sc-chat-btn::after,.sc-dot,.sc-typing span{animation:none}
  #sc-chat-window.sc-open,.sc-row{animation:none}
  #sc-chat-body{scroll-behavior:auto}
}
</style>

<button id="sc-chat-btn" type="button" aria-label="Abrir asistente SmartCare" aria-expanded="false" aria-controls="sc-chat-window">
    <svg class="sc-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 8.6 8.6 0 0 1-3.8-.9L3 20l1.1-4.9A8.4 8.4 0 1 1 21 11.5z"/></svg>
    <span id="sc-badge"></span>
</button>

<section id="sc-chat-window" role="dialog" aria-label="Asistente SmartCare">

    <header id="sc-chat-header">
        <div class="sc-id">
            <div class="sc-avatar"><img src="../assets/img/logo_cns.png" alt="CNS"></div>
            <div>
                <strong>SmartCare Assistant</strong>
                <small><span class="sc-dot"></span>En línea · CNS La Paz</small>
            </div>
        </div>
        <div class="sc-actions">
            <button id="sc-clear-btn" type="button" title="Limpiar conversación" aria-label="Limpiar conversación">
                <svg class="sc-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
            </button>
            <button id="sc-close" type="button" title="Cerrar chat" aria-label="Cerrar chat">
                <svg class="sc-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
            </button>
        </div>
    </header>

    <div id="sc-quick-btns">
        <button class="sc-qbtn" type="button" data-msg="Quiero agendar una cita">📅 Agendar cita</button>
        <button class="sc-qbtn" type="button" data-msg="Mis citas próximas">📋 Mis citas</button>
        <button class="sc-qbtn" type="button" data-msg="Ver médicos disponibles">🩺 Médicos</button>
        <button class="sc-qbtn" type="button" data-msg="Ver horarios de atención">🕐 Horarios</button>
        <button class="sc-qbtn" type="button" data-msg="Quiero cancelar una cita">❌ Cancelar cita</button>
        <button class="sc-qbtn" type="button" data-msg="Cómo funciona la plataforma">ℹ️ Ayuda</button>
    </div>

    <div id="sc-body-wrap">
        <div id="sc-chat-body" role="log" aria-live="polite" aria-label="Conversación"></div>
        <button id="sc-down" type="button" aria-label="Ir al último mensaje">
            <svg class="sc-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
        </button>
        <div id="sc-confirm" role="alertdialog" aria-label="Confirmar limpieza">
            <div class="sc-confirm-box">
                <p>¿Quieres borrar toda la conversación?</p>
                <div>
                    <button id="sc-confirm-no" type="button">Cancelar</button>
                    <button id="sc-confirm-yes" type="button">Borrar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="sc-chat-footer">
        <span id="sc-counter"></span>
        <textarea id="sc-message" placeholder="Escribe tu pregunta..." rows="1" maxlength="600" aria-label="Mensaje"></textarea>
        <button id="sc-send" type="button" title="Enviar" aria-label="Enviar mensaje" disabled>
            <svg class="sc-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 2 11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
        </button>
    </div>
    <div class="sc-foot-note">No sustituye una consulta médica. En emergencias llama al 165.</div>

</section>

<script>
(function () {
    'use strict';

    var $ = function (id) { return document.getElementById(id); };
    var ventana = $('sc-chat-window');
    var fab = $('sc-chat-btn');
    var cuerpo = $('sc-chat-body');
    var input = $('sc-message');
    var enviarBtn = $('sc-send');
    var badge = $('sc-badge');
    var contador = $('sc-counter');
    var bajarBtn = $('sc-down');
    var confirmar = $('sc-confirm');

    var CLAVE = 'sc_chat_v2';
    var MAX = 600;
    var LIMITE = 60;
    var URL_ENVIAR = 'index.php?controller=chatbot&action=enviar';
    var URL_LIMPIAR = 'index.php?controller=chatbot&action=limpiarHistorial';

    var PERMITIDAS = { STRONG: 1, B: 1, EM: 1, I: 1, BR: 1, A: 1, UL: 1, OL: 1, LI: 1, P: 1, SPAN: 1 };
    var DESCARTAR = { SCRIPT: 1, STYLE: 1, IFRAME: 1, OBJECT: 1, EMBED: 1, LINK: 1, META: 1, FORM: 1 };

    var estado = cargar();
    var esperando = false;
    var escribiendo = null;

    function cargar() {
        try {
            var d = JSON.parse(sessionStorage.getItem(CLAVE) || 'null');
            if (d && Array.isArray(d.msgs)) return d;
        } catch (e) {}
        return { open: false, msgs: [], unread: 0, draft: '' };
    }

    function guardar() {
        try { sessionStorage.setItem(CLAVE, JSON.stringify(estado)); } catch (e) {}
    }

    function limpiar(html) {
        var tpl = document.createElement('template');
        tpl.innerHTML = html;
        recorrer(tpl.content);
        var div = document.createElement('div');
        div.appendChild(tpl.content);
        return div.innerHTML;
    }

    function recorrer(nodo) {
        var hijos = Array.prototype.slice.call(nodo.childNodes);
        for (var i = 0; i < hijos.length; i++) {
            var h = hijos[i];
            if (h.nodeType === 3) continue;
            if (h.nodeType !== 1) { h.parentNode.removeChild(h); continue; }
            var tag = h.tagName;
            if (DESCARTAR[tag]) { h.parentNode.removeChild(h); continue; }
            if (!PERMITIDAS[tag]) {
                var frag = document.createDocumentFragment();
                while (h.firstChild) frag.appendChild(h.firstChild);
                h.parentNode.replaceChild(frag, h);
                recorrer(nodo);
                return;
            }
            Array.prototype.slice.call(h.attributes).forEach(function (a) {
                var conservar = tag === 'A' && (a.name === 'href' || (a.name === 'class' && a.value === 'btn-chat-link'));
                if (!conservar) h.removeAttribute(a.name);
            });
            if (tag === 'A') {
                var href = h.getAttribute('href') || '';
                if (!/^(index\.php|\/|https?:\/\/|#)/i.test(href)) {
                    h.removeAttribute('href');
                } else if (/^https?:/i.test(href)) {
                    h.setAttribute('target', '_blank');
                    h.setAttribute('rel', 'noopener noreferrer');
                }
            }
            recorrer(h);
        }
    }

    function hora(ts) {
        return new Date(ts).toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });
    }

    function pintar(tipo, contenido, ts) {
        var fila = document.createElement('div');
        fila.className = 'sc-row sc-row-' + tipo;
        var burbuja = document.createElement('div');
        burbuja.className = tipo === 'user' ? 'sc-user' : 'sc-bot';
        if (tipo === 'user') burbuja.textContent = contenido;
        else burbuja.innerHTML = limpiar(contenido);
        var t = document.createElement('div');
        t.className = 'sc-time';
        t.textContent = hora(ts);
        fila.appendChild(burbuja);
        fila.appendChild(t);
        cuerpo.appendChild(fila);
        return fila;
    }

    function agregar(tipo, contenido) {
        quitarEscribiendo();
        var ts = Date.now();
        pintar(tipo, contenido, ts);
        estado.msgs.push({ t: tipo, h: contenido, ts: ts });
        if (estado.msgs.length > LIMITE) estado.msgs.shift();
        guardar();
        bajar(tipo === 'user');
    }

    function cercaDelFinal() {
        return cuerpo.scrollHeight - cuerpo.scrollTop - cuerpo.clientHeight < 160;
    }

    function bajar(forzar) {
        if (forzar || cercaDelFinal()) {
            requestAnimationFrame(function () { cuerpo.scrollTop = cuerpo.scrollHeight; });
        } else {
            bajarBtn.classList.add('sc-show');
        }
    }

    function mostrarEscribiendo() {
        if (escribiendo) return;
        escribiendo = document.createElement('div');
        escribiendo.className = 'sc-row sc-row-bot';
        escribiendo.innerHTML = '<div class="sc-typing"><span></span><span></span><span></span></div>';
        cuerpo.appendChild(escribiendo);
        bajar(true);
    }

    function quitarEscribiendo() {
        if (escribiendo && escribiendo.parentNode) escribiendo.parentNode.removeChild(escribiendo);
        escribiendo = null;
    }

    function mostrarError(mensaje, texto) {
        quitarEscribiendo();
        var fila = document.createElement('div');
        fila.className = 'sc-row sc-row-bot sc-error';
        var b = document.createElement('div');
        b.className = 'sc-bot';
        b.textContent = '⚠️ ' + mensaje;
        var r = document.createElement('button');
        r.type = 'button';
        r.className = 'sc-retry';
        r.textContent = 'Reintentar';
        r.addEventListener('click', function () {
            if (esperando) return;
            fila.parentNode.removeChild(fila);
            enviar(texto, true);
        });
        b.appendChild(document.createElement('br'));
        b.appendChild(r);
        fila.appendChild(b);
        cuerpo.appendChild(fila);
        bajar(true);
    }

    function pedir(texto) {
        var ctrl = new AbortController();
        var tiempo = setTimeout(function () { ctrl.abort(); }, 60000);
        return fetch(URL_ENVIAR, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'mensaje=' + encodeURIComponent(texto),
            credentials: 'same-origin',
            signal: ctrl.signal
        }).then(function (r) {
            if (!r.ok) throw new Error('http');
            return r.json();
        }).then(function (d) {
            clearTimeout(tiempo);
            return d;
        }, function (e) {
            clearTimeout(tiempo);
            throw e;
        });
    }

    function actualizarEnviar() {
        enviarBtn.disabled = esperando || !input.value.trim();
    }

    function bloquear(valor) {
        esperando = valor;
        actualizarEnviar();
    }

    function actualizarBadge() {
        if (estado.unread > 0 && !estaAbierto()) {
            badge.textContent = estado.unread > 9 ? '9+' : String(estado.unread);
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }

    function enviar(texto, reintento) {
        texto = (texto || '').trim();
        if (!texto || esperando) return;
        if (!estaAbierto()) abrir(true);
        if (!reintento) agregar('user', texto);
        bloquear(true);
        mostrarEscribiendo();
        pedir(texto).then(function (d) {
            agregar('bot', d.respuesta || 'No obtuve respuesta.');
            if (!estaAbierto()) {
                estado.unread++;
                guardar();
                actualizarBadge();
            }
        }).catch(function (e) {
            var msg = !navigator.onLine
                ? 'Sin conexión a internet.'
                : (e && e.name === 'AbortError' ? 'El asistente tardó demasiado en responder.' : 'No pude conectar con el asistente.');
            mostrarError(msg, texto);
        }).then(function () {
            bloquear(false);
            if (estaAbierto()) input.focus();
        });
    }

    function bienvenida() {
        mostrarEscribiendo();
        pedir('').then(function (d) {
            agregar('bot', d.respuesta || '👋 Hola, soy SmartCare Assistant.');
        }).catch(function () {
            agregar('bot', '👋 Hola, soy <strong>SmartCare Assistant</strong>. ¿En qué puedo ayudarte?');
        });
    }

    function estaAbierto() {
        return ventana.classList.contains('sc-open');
    }

    function abrir(sinFoco) {
        ventana.classList.add('sc-open');
        fab.setAttribute('aria-expanded', 'true');
        estado.open = true;
        estado.unread = 0;
        guardar();
        actualizarBadge();
        if (!estado.msgs.length && !escribiendo) bienvenida();
        ajustarAltura();
        bajar(true);
        if (!sinFoco) setTimeout(function () { input.focus(); }, 80);
    }

    function cerrar(devolverFoco) {
        ventana.classList.remove('sc-open');
        fab.setAttribute('aria-expanded', 'false');
        estado.open = false;
        guardar();
        if (devolverFoco) fab.focus();
    }

    function ajustarAltura() {
        input.style.height = '42px';
        input.style.height = Math.max(42, Math.min(input.scrollHeight, 110)) + 'px';
    }

    function actualizarContador() {
        var n = input.value.length;
        contador.textContent = n + '/' + MAX;
        contador.classList.toggle('sc-show', n > MAX - 120);
    }

    function enviarDesdeInput() {
        var t = input.value;
        if (!t.trim() || esperando) return;
        input.value = '';
        estado.draft = '';
        ajustarAltura();
        actualizarContador();
        actualizarEnviar();
        enviar(t);
    }

    function limpiarConversacion() {
        fetch(URL_LIMPIAR, { method: 'POST', credentials: 'same-origin' }).catch(function () {});
        cuerpo.innerHTML = '';
        escribiendo = null;
        estado.msgs = [];
        estado.unread = 0;
        guardar();
        bloquear(false);
        bienvenida();
    }

    fab.addEventListener('click', function () {
        if (estaAbierto()) cerrar(false); else abrir(false);
    });

    $('sc-close').addEventListener('click', function () { cerrar(true); });

    $('sc-clear-btn').addEventListener('click', function () {
        confirmar.classList.add('sc-show');
        $('sc-confirm-no').focus();
    });

    $('sc-confirm-no').addEventListener('click', function () {
        confirmar.classList.remove('sc-show');
        input.focus();
    });

    $('sc-confirm-yes').addEventListener('click', function () {
        confirmar.classList.remove('sc-show');
        limpiarConversacion();
    });

    document.querySelectorAll('.sc-qbtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            enviar(btn.getAttribute('data-msg'));
        });
    });

    enviarBtn.addEventListener('click', enviarDesdeInput);

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
            e.preventDefault();
            enviarDesdeInput();
        }
    });

    input.addEventListener('input', function () {
        estado.draft = input.value;
        guardar();
        ajustarAltura();
        actualizarContador();
        actualizarEnviar();
    });

    cuerpo.addEventListener('scroll', function () {
        if (cercaDelFinal()) bajarBtn.classList.remove('sc-show');
    });

    bajarBtn.addEventListener('click', function () {
        cuerpo.scrollTop = cuerpo.scrollHeight;
        bajarBtn.classList.remove('sc-show');
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape' || !estaAbierto()) return;
        if (confirmar.classList.contains('sc-show')) confirmar.classList.remove('sc-show');
        else cerrar(true);
    });

    estado.msgs.forEach(function (m) { pintar(m.t, m.h, m.ts); });
    input.value = estado.draft || '';
    ajustarAltura();
    actualizarContador();
    actualizarEnviar();
    actualizarBadge();
    if (estado.open) abrir(true);
})();
</script>