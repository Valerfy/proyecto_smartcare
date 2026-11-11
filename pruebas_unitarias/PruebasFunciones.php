<?php
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../models/MedicoModel.php';
require_once __DIR__ . '/../models/ReporteRecepcionModel.php';
require_once __DIR__ . '/../controllers/ChatbotController.php';

class PruebasFunciones
{
    private int $aprobadas = 0;
    private int $fallidas = 0;

    private function comprobar(string $nombre, bool $resultado, mixed $valorDevuelto): void
    {
        echo ($resultado ? "[APROBADA] " : "[FALLIDA] ") . $nombre . PHP_EOL;
        echo "Valor devuelto: " . json_encode($valorDevuelto, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL . PHP_EOL;

        if ($resultado) {
            $this->aprobadas++;
        } else {
            $this->fallidas++;
        }
    }

    private function ejecutarPrivada(string $clase, string $metodo, array $argumentos)
    {
        $reflexion = new ReflectionMethod($clase, $metodo);
        $reflexion->setAccessible(true);

        if ($reflexion->isStatic()) {
            return $reflexion->invokeArgs(null, $argumentos);
        }

        return $reflexion->invokeArgs(new $clase(), $argumentos);
    }

    public function probarSepararLista(): void
    {
        $resultado = $this->ejecutarPrivada(HistorialModel::class, 'separarLista', [
            "Alergia, asma; Alergia\nDiabetes", 30
        ]);

        $this->comprobar(
            'HistorialModel::separarLista',
            $resultado === ['Alergia', 'asma', 'Diabetes'],
            $resultado
        );
    }

    public function probarGenerarHoras(): void
    {
        $resultado = $this->ejecutarPrivada(MedicoModel::class, 'generarHoras', [
            '08:00', '10:00'
        ]);

        $this->comprobar(
            'MedicoModel::generarHoras',
            $resultado === ['08:00', '08:30', '09:00', '09:30'],
            $resultado
        );
    }

    public function probarFiltrosDesdeGet(): void
    {
        $resultado = ReporteRecepcionModel::filtrosDesdeGet([
            'q' => '  Juan  ',
            'medico_id' => '12',
            'fecha_desde' => '2026-11-01',
            'fecha_hasta' => 'fecha-invalida',
            'tipo' => 'Nuevo'
        ]);

        $esperado = [
            'q' => 'Juan',
            'medico_id' => 12,
            'fecha_desde' => '2026-11-01',
            'fecha_hasta' => '',
            'tipo' => 'Nuevo'
        ];

        $this->comprobar(
            'ReporteRecepcionModel::filtrosDesdeGet',
            $resultado === $esperado,
            $resultado
        );
    }

    public function probarDetectarIntent(): void
    {
        $resultado = $this->ejecutarPrivada(ChatbotController::class, 'detectarIntent', [
            'Quiero reservar una cita'
        ]);

        $this->comprobar(
            'ChatbotController::detectarIntent',
            $resultado === 'agendar_cita',
            $resultado
        );
    }

    public function probarLimpiarRespuesta(): void
    {
        $resultado = $this->ejecutarPrivada(ChatbotController::class, 'limpiarRespuesta', [
            "<think>razonamiento interno</think>\nRespuesta para el usuario."
        ]);

        $this->comprobar(
            'ChatbotController::limpiarRespuesta',
            $resultado === 'Respuesta para el usuario.',
            $resultado
        );
    }

    public function ejecutar(): void
    {
        $this->probarSepararLista();
        $this->probarGenerarHoras();
        $this->probarFiltrosDesdeGet();
        $this->probarDetectarIntent();
        $this->probarLimpiarRespuesta();

        echo PHP_EOL;
        echo "Pruebas aprobadas: {$this->aprobadas}" . PHP_EOL;
        echo "Pruebas fallidas: {$this->fallidas}" . PHP_EOL;

        if ($this->fallidas > 0) {
            exit(1);
        }
    }
}
