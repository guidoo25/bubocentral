<?php

spl_autoload_register(function ($class) {
    // Convertir el namespace a una ruta de archivo
    $prefix = '';
    $base_dir = __DIR__ . '/';
    
    // Si la clase no usa el prefijo, saltar
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Clase no encontrada
        return;
    }
    
    // Obtener el nombre relativo de la clase
    $relative_class = substr($class, $len);
    
    // Reemplazar el namespace con la ruta del directorio
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // Si el archivo existe, requerirlo
    if (file_exists($file)) {
        require $file;
    }
});