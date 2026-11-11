<?php
$__vTema   = $volverTema ?? 'claro';
$__vSolo   = !empty($volverSoloRegistrar);
unset($volverTema, $volverSoloRegistrar);

$__vCtrl   = $_GET['controller'] ?? 'auth';
$__vAccion = $_GET['action'] ?? 'index';
$__vInicio = 'index.php?controller=' . urlencode($__vCtrl) . '&action=index';

$__vParams = $_GET;
unset($__vParams['volver'], $__vParams['msg'], $__vParams['error']);
$__vActual = 'index.php?' . http_build_query($__vParams);

$__vPila = $_SESSION['pila_nav'] ?? [];

if (isset($_GET['volver'])) {
    while ($__vPila && end($__vPila) !== $__vActual) {
        array_pop($__vPila);
    }
    if (!$__vPila) {
        $__vPila[] = $__vActual;
    }
} elseif (end($__vPila) !== $__vActual) {
    $__vPila[] = $__vActual;
}

$__vPila = array_slice($__vPila, -15);
$_SESSION['pila_nav'] = $__vPila;

$__vUrl = null;
if (count($__vPila) >= 2) {
    $__vUrl = $__vPila[count($__vPila) - 2] . '&volver=1';
} elseif ($__vAccion !== 'index') {
    $__vUrl = $__vInicio;
}

if (!$__vSolo && $__vUrl):
    if (!defined('VS_VOLVER_CSS')):
        define('VS_VOLVER_CSS', true); ?>
<style>
.vs-volver{display:inline-block;margin:0 0 16px;padding:6px 14px;border:1px solid #6c757d;border-radius:8px;
  color:#495057;background:transparent;font:inherit;font-size:14px;line-height:1.4;text-decoration:none;cursor:pointer}
.vs-volver:hover{background:#6c757d;color:#fff}
.vs-volver--oscuro{border-color:#cbd5e1;color:#f8fafc}
.vs-volver--oscuro:hover{background:#8b5cf6;border-color:#8b5cf6;color:#fff}
</style>
<?php endif; ?>
<a href="<?= htmlspecialchars($__vUrl) ?>" class="vs-volver<?= $__vTema === 'oscuro' ? ' vs-volver--oscuro' : '' ?>">&larr; Volver</a>
<?php endif; ?>