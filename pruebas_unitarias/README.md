# Pruebas unitarias

## Ejecución

1. Colocar el proyecto en la carpeta `htdocs` de XAMPP.
2. Iniciar Apache.
3. Abrir una terminal en la carpeta del proyecto.
4. Ejecutar:

```bash
php pruebas_unitarias/ejecutar.php
```

También se puede abrir la dirección `http://localhost/proyecto_smartcare-main/pruebas_unitarias/ejecutar.php` si el servidor está configurado para ejecutar PHP.

## Funciones evaluadas

- `HistorialModel::separarLista`
- `MedicoModel::generarHoras`
- `ReporteRecepcionModel::filtrosDesdeGet`
- `ChatbotController::detectarIntent`
- `ChatbotController::limpiarRespuesta`
