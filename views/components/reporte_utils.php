<?php
/**
 * Funciones compartidas por los reportes (pantalla y PDF) del médico y de recepción.
 */

if (!function_exists('rep_pct')) {
    /** Porcentaje de $parte sobre $total, ej. "70 %" o "33,3 %". */
    function rep_pct($parte, $total): string {
        if ((float)$total <= 0) return '0 %';
        $v = round(((float)$parte / (float)$total) * 100, 1);
        $txt = rtrim(rtrim(number_format($v, 1, ',', ''), '0'), ',');
        return $txt . ' %';
    }
}

if (!function_exists('rep_delta')) {
    /**
     * Etiqueta de comparación contra el periodo anterior.
     * $anterior = null  -> no hay periodo para comparar (devuelve '').
     * $subeEsBueno: true = subir es bueno (verde), false = subir es malo (rojo), null = neutro (gris).
     */
    function rep_delta($actual, $anterior, $subeEsBueno = null): string {
        if ($anterior === null) return '';
        $actual   = (int)$actual;
        $anterior = (int)$anterior;

        if ($actual === $anterior) {
            $txt   = '= igual que antes (' . $anterior . ')';
            $color = '#64748b';
        } else {
            $sube    = $actual > $anterior;
            $flecha  = $sube ? '▲' : '▼';
            if ($anterior === 0) {
                $txt = $flecha . ' nuevo (antes 0)';
            } else {
                $v   = round(abs($actual - $anterior) / $anterior * 100, 1);
                $num = rtrim(rtrim(number_format($v, 1, ',', ''), '0'), ',');
                $txt = $flecha . ' ' . ($sube ? '+' : '-') . $num . ' % (antes ' . $anterior . ')';
            }
            if ($subeEsBueno === null) {
                $color = '#64748b';
            } else {
                $color = ($sube === (bool)$subeEsBueno) ? '#16a34a' : '#dc2626';
            }
        }
        return '<span style="color:' . $color . ';font-weight:600;font-size:11px;white-space:nowrap;">'
             . htmlspecialchars($txt) . '</span>';
    }
}

if (!function_exists('rep_periodo_anterior')) {
    /**
     * Periodo inmediatamente anterior, de igual duración.
     * Requiere Desde y Hasta; si falta alguno devuelve null.
     * @return array{0:string,1:string}|null  [desde, hasta] en Y-m-d
     */
    function rep_periodo_anterior(string $desde, string $hasta): ?array {
        if ($desde === '' || $hasta === '') return null;
        try {
            $d = new DateTime($desde);
            $h = new DateTime($hasta);
        } catch (Exception $e) {
            return null;
        }
        if ($h < $d) return null;
        $dias  = (int)$d->diff($h)->days + 1;
        $prevH = (clone $d)->modify('-1 day');
        $prevD = (clone $prevH)->modify('-' . ($dias - 1) . ' days');
        return [$prevD->format('Y-m-d'), $prevH->format('Y-m-d')];
    }
}

if (!function_exists('rep_fecha')) {
    function rep_fecha(?string $f): string {
        return $f ? date('d/m/Y', strtotime($f)) : '';
    }
}

if (!function_exists('rep_estado')) {
    /** Total de citas de un estado dentro de $estadisticas['citas_por_estado']. */
    function rep_estado(?array $estadisticas, string $estado): int {
        foreach (($estadisticas['citas_por_estado'] ?? []) as $e) {
            if ($e['estado'] === $estado) return (int)$e['total'];
        }
        return 0;
    }
}

if (!function_exists('rep_total_citas')) {
    function rep_total_citas(?array $estadisticas): int {
        $t = 0;
        foreach (($estadisticas['citas_por_estado'] ?? []) as $e) $t += (int)$e['total'];
        return $t;
    }
}

if (!function_exists('rep_edad_rangos')) {
    /** Pacientes únicos (por CI) agrupados por rango de edad. Solo devuelve rangos con datos. */
    function rep_edad_rangos(array $historiales): array {
        $rangos = [
            'Menores de 18' => 0,
            '18 a 29 años'  => 0,
            '30 a 44 años'  => 0,
            '45 a 59 años'  => 0,
            '60 o más'      => 0,
            'Sin dato'      => 0,
        ];
        $vistos = [];
        $hoy    = new DateTime('today');
        foreach ($historiales as $i => $h) {
            $clave = $h['ci'] ?? ('fila' . $i);
            if (isset($vistos[$clave])) continue;
            $vistos[$clave] = true;

            $fn = $h['fecha_nac'] ?? '';
            if (empty($fn) || $fn === '0000-00-00') { $rangos['Sin dato']++; continue; }
            try {
                $edad = (new DateTime($fn))->diff($hoy)->y;
            } catch (Exception $e) { $rangos['Sin dato']++; continue; }

            if     ($edad < 18) $rangos['Menores de 18']++;
            elseif ($edad < 30) $rangos['18 a 29 años']++;
            elseif ($edad < 45) $rangos['30 a 44 años']++;
            elseif ($edad < 60) $rangos['45 a 59 años']++;
            else                $rangos['60 o más']++;
        }
        return array_filter($rangos, fn($n) => $n > 0);
    }
}

if (!function_exists('rep_sangre_pacientes')) {
    /** Pacientes únicos (por CI) agrupados por tipo de sangre, de mayor a menor. */
    function rep_sangre_pacientes(array $historiales): array {
        $porPaciente = [];
        foreach ($historiales as $i => $h) {
            $clave = $h['ci'] ?? ('fila' . $i);
            $porPaciente[$clave] = !empty($h['tipo_sangre']) ? $h['tipo_sangre'] : 'Sin dato';
        }
        $conteo = array_count_values($porPaciente);
        arsort($conteo);
        return $conteo;
    }
}
