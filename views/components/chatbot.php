<style>
#sc-chat-btn {
    position: fixed;
    bottom: 28px;
    right: 28px;
    width: 62px;
    height: 62px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0d6efd, #0056d6);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    cursor: pointer;
    z-index: 99999;
    box-shadow: 0 6px 24px rgba(13,110,253,.45);
    transition: transform .25s, box-shadow .25s;
    border: none;
}
#sc-chat-btn:hover { transform: scale(1.1); }

#sc-badge {
    position: absolute;
    top: -4px; right: -4px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px; height: 20px;
    font-size: 11px;
    font-weight: 700;
    align-items: center;
    justify-content: center;
    display: none;
}

#sc-chat-window {
    position: fixed;
    right: 28px;
    bottom: 105px;
    width: 390px;
    height: 570px;
    background: #fff;
    border-radius: 20px;
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 99999;
    box-shadow: 0 16px 48px rgba(0,0,0,.22);
}
@keyframes scSlideUp {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
#sc-chat-window.sc-visible { animation: scSlideUp .2s ease; }

#sc-chat-header {
    background: linear-gradient(135deg, #0d6efd, #0056d6);
    color: white;
    padding: 14px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}
.sc-avatar {
    width: 40px; height: 40px;
    background: rgba(255,255,255,.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    margin-right: 10px;
}
#sc-header-info strong { display:block; font-size:15px; }
#sc-header-info small  { opacity:.8; font-size:12px; }
.sc-dot-online {
    display: inline-block;
    width: 8px; height: 8px;
    background: #4ade80;
    border-radius: 50%;
    margin-right: 4px;
    animation: scPulse 2s infinite;
}
@keyframes scPulse { 0%,100%{opacity:1} 50%{opacity:.35} }
#sc-header-actions { display:flex; gap:8px; align-items:center; }
#sc-header-actions button {
    background: rgba(255,255,255,.15);
    border: none; color: white;
    width: 30px; height: 30px;
    border-radius: 50%;
    cursor: pointer; font-size: 14px;
    transition: background .2s;
}
#sc-header-actions button:hover { background: rgba(255,255,255,.3); }

#sc-quick-btns {
    padding: 8px 10px 6px;
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    background: #f0f4ff;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.sc-qbtn {
    background: white;
    border: 1px solid #c7d7fd;
    border-radius: 20px;
    padding: 5px 11px;
    font-size: 12px;
    cursor: pointer;
    color: #0d6efd;
    transition: all .18s;
    white-space: nowrap;
    font-family: inherit;
}
.sc-qbtn:hover { background:#0d6efd; color:white; border-color:#0d6efd; }

#sc-chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 14px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
}
#sc-chat-body::-webkit-scrollbar { width:4px; }
#sc-chat-body::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:4px; }

.sc-user {
    background: linear-gradient(135deg,#0d6efd,#0056d6);
    color: white;
    padding: 10px 14px;
    border-radius: 18px 18px 4px 18px;
    margin-bottom: 4px;
    margin-left: auto;
    max-width: 82%;
    width: fit-content;
    font-size: 14px;
    line-height: 1.5;
    word-break: break-word;
}
.sc-bot {
    background: white;
    color: #1e293b;
    padding: 11px 14px;
    border-radius: 18px 18px 18px 4px;
    margin-bottom: 4px;
    max-width: 88%;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
    font-size: 14px;
    line-height: 1.55;
}
.sc-bot a { color:#0d6efd; }
.btn-chat-link {
    display: inline-block;
    margin-top: 7px;
    padding: 6px 14px;
    background: #0d6efd;
    color: white !important;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    transition: background .2s;
}
.btn-chat-link:hover { background:#0056d6; }
.sc-time {
    font-size: 10px;
    color: #94a3b8;
    margin-bottom: 10px;
    padding: 0 4px;
}
.sc-time.sc-time-user { text-align:right; margin-left:auto; }
.sc-time.sc-time-bot  { text-align:left; }

.sc-typing-bubble {
    background: white;
    padding: 12px 16px;
    border-radius: 18px 18px 18px 4px;
    margin-bottom: 4px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
.sc-typing-bubble span {
    display: inline-block;
    width: 8px; height: 8px;
    background: #94a3b8;
    border-radius: 50%;
    animation: scBounce 1.2s infinite;
}
.sc-typing-bubble span:nth-child(2) { animation-delay:.2s; }
.sc-typing-bubble span:nth-child(3) { animation-delay:.4s; }
@keyframes scBounce {
    0%,60%,100%{ transform:translateY(0); }
    30%         { transform:translateY(-6px); }
}

#sc-chat-footer {
    display: flex;
    align-items: center;
    border-top: 1px solid #e2e8f0;
    padding: 8px 10px;
    gap: 8px;
    background: white;
    flex-shrink: 0;
}
#sc-message {
    flex: 1;
    border: 1px solid #e2e8f0;
    border-radius: 22px;
    padding: 10px 16px;
    outline: none;
    font-size: 14px;
    background: #f8fafc;
    transition: border-color .2s;
    resize: none;
    height: 42px;
    max-height: 100px;
    overflow-y: auto;
    font-family: inherit;
}
#sc-message:focus { border-color:#0d6efd; background:white; }
#sc-message:disabled { opacity:.5; cursor:not-allowed; }
#sc-send {
    width: 42px; height: 42px;
    border: none;
    border-radius: 50%;
    background: linear-gradient(135deg,#0d6efd,#0056d6);
    color: white;
    cursor: pointer;
    font-size: 17px;
    display: flex; align-items: center; justify-content: center;
    transition: transform .2s, box-shadow .2s;
    flex-shrink: 0;
}
#sc-send:hover:not(:disabled) { transform:scale(1.08); box-shadow:0 4px 12px rgba(13,110,253,.4); }
#sc-send:disabled { background:#94a3b8; cursor:not-allowed; }

@media (max-width:480px) {
    #sc-chat-window { width:calc(100vw - 20px); right:10px; bottom:90px; height:75vh; }
}
</style>

<button id="sc-chat-btn" title="SmartCare Assistant" aria-label="Abrir chat">
    💬
    <span id="sc-badge">1</span>
</button>

<div id="sc-chat-window">

    <div id="sc-chat-header">
        <div style="display:flex;align-items:center">
            <div class="sc-avatar">🤖</div>
            <div id="sc-header-info">
                <strong>SmartCare Assistant</strong>
                <small><span class="sc-dot-online"></span>En línea · CNS La Paz</small>
            </div>
        </div>
        <div id="sc-header-actions">
            <button id="sc-clear-btn" title="Limpiar conversación">🗑️</button>
            <button id="sc-close" title="Cerrar chat">✕</button>
        </div>
    </div>

    <div id="sc-quick-btns">
        <button class="sc-qbtn" data-msg="Quiero agendar una cita">📅 Agendar cita</button>
        <button class="sc-qbtn" data-msg="Ver médicos disponibles">🩺 Ver médicos</button>
        <button class="sc-qbtn" data-msg="Mis citas próximas">📋 Mis citas</button>
        <button class="sc-qbtn" data-msg="Ver horarios de atención">🕐 Horarios</button>
        <button class="sc-qbtn" data-msg="Cómo funciona la plataforma">ℹ️ Ayuda</button>
    </div>

    <div id="sc-chat-body"></div>

    <div id="sc-chat-footer">
        <textarea
            id="sc-message"
            placeholder="Escribe tu pregunta..."
            rows="1"
            maxlength="600"
        ></textarea>
        <button id="sc-send" title="Enviar">➤</button>
    </div>

</div>

<script>
(function () {
    'use strict';

    var ventana    = document.getElementById('sc-chat-window');
    var btnAbrir   = document.getElementById('sc-chat-btn');
    var btnCerrar  = document.getElementById('sc-close');
    var btnLimpiar = document.getElementById('sc-clear-btn');
    var cuerpo     = document.getElementById('sc-chat-body');
    var inputMsg   = document.getElementById('sc-message');
    var btnEnviar  = document.getElementById('sc-send');
    var badge      = document.getElementById('sc-badge');

    var abierto           = false;
    var esperando         = false;
    var bienvenidaCargada = false;
    var typingEl          = null;

    btnAbrir.addEventListener('click', function () {
        abierto = !abierto;
        if (abierto) {
            ventana.style.display = 'flex';
            ventana.classList.add('sc-visible');
            badge.style.display = 'none';
            if (!bienvenidaCargada) cargarBienvenida();
            setTimeout(function(){ inputMsg.focus(); }, 100);
        } else {
            ventana.style.display = 'none';
            ventana.classList.remove('sc-visible');
        }
    });

    btnCerrar.addEventListener('click', function () {
        abierto = false;
        ventana.style.display = 'none';
        ventana.classList.remove('sc-visible');
    });

    btnLimpiar.addEventListener('click', function () {
        if (!confirm('¿Limpiar la conversación?')) return;
        fetch('index.php?controller=chatbot&action=limpiarHistorial', { method: 'POST' });
        cuerpo.innerHTML = '';
        bienvenidaCargada = false;
        esperando = false;
        bloquearInput(false);
        cargarBienvenida();
    });

    document.querySelectorAll('.sc-qbtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (esperando) return;
            var msg = btn.getAttribute('data-msg');
            if (msg) enviarMensaje(msg);
        });
    });

    inputMsg.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!esperando) enviarMensaje();
        }
    });

    btnEnviar.addEventListener('click', function () {
        if (!esperando) enviarMensaje();
    });

    inputMsg.addEventListener('input', function () {
        inputMsg.style.height = '42px';
        inputMsg.style.height = Math.min(inputMsg.scrollHeight, 100) + 'px';
    });

    function cargarBienvenida() {
        bienvenidaCargada = true;
        mostrarTyping();

        fetch('index.php?controller=chatbot&action=enviar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'mensaje='
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            quitarTyping();
            agregarBurbuja('bot', data.respuesta || '👋 Hola, soy SmartCare Assistant.');
        })
        .catch(function () {
            quitarTyping();
            agregarBurbuja('bot', '👋 Hola, soy SmartCare Assistant. ¿En qué puedo ayudarte?');
        });
    }

    function enviarMensaje(msgForzado) {
        var texto = msgForzado ? msgForzado.trim() : inputMsg.value.trim();
        if (!texto) return;
        if (esperando) return;

        agregarBurbuja('user', escapeHtml(texto));

        if (!msgForzado) {
            inputMsg.value = '';
            inputMsg.style.height = '42px';
        }

        bloquearInput(true);
        mostrarTyping();

        fetch('index.php?controller=chatbot&action=enviar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'mensaje=' + encodeURIComponent(texto)
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            quitarTyping();
            bloquearInput(false);
            agregarBurbuja('bot', data.respuesta || 'No obtuve respuesta.');
            if (!abierto) {
                badge.style.display = 'flex';
            }
        })
        .catch(function () {
            quitarTyping();
            bloquearInput(false);
            agregarBurbuja('bot', '⚠️ Error al conectar. Por favor intenta de nuevo.');
        });
    }

    function bloquearInput(bloquear) {
        esperando          = bloquear;
        btnEnviar.disabled = bloquear;
        inputMsg.disabled  = bloquear;
    }

    function mostrarTyping() {
        if (typingEl) return;
        typingEl = document.createElement('div');
        typingEl.className = 'sc-typing-bubble';
        typingEl.innerHTML = '<span></span><span></span><span></span>';
        cuerpo.appendChild(typingEl);
        scrollAbajo();
    }

    function quitarTyping() {
        if (typingEl && typingEl.parentNode) {
            typingEl.parentNode.removeChild(typingEl);
        }
        typingEl = null;
    }

    function agregarBurbuja(tipo, html) {
        quitarTyping();

        var div = document.createElement('div');
        div.className = (tipo === 'user') ? 'sc-user' : 'sc-bot';
        div.innerHTML = html;
        cuerpo.appendChild(div);

        var time = document.createElement('div');
        time.className = 'sc-time ' + (tipo === 'user' ? 'sc-time-user' : 'sc-time-bot');
        time.textContent = horaActual();
        cuerpo.appendChild(time);

        scrollAbajo();
    }

    function scrollAbajo() {
        setTimeout(function () { cuerpo.scrollTop = cuerpo.scrollHeight; }, 60);
    }

    function horaActual() {
        return new Date().toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(t) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(t));
        return d.innerHTML;
    }

})();
</script>
