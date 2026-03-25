<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "=== DEBUG ARTISAN ===\n";

try {
    echo "1. Definiendo LARAVEL_START...\n";
    define('LARAVEL_START', microtime(true));

    echo "2. Cargando autoload...\n";
    require __DIR__.'/vendor/autoload.php';
    echo "   Autoload OK\n";

    echo "3. Cargando bootstrap/app.php...\n";
    $app = require_once __DIR__.'/bootstrap/app.php';
    echo "   Bootstrap OK\n";

    echo "4. Creando kernel...\n";
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    echo "   Kernel OK\n";

    echo "5. Ejecutando handle...\n";
    $status = $kernel->handle(
        $input = new Symfony\Component\Console\Input\ArgvInput,
        new Symfony\Component\Console\Output\ConsoleOutput
    );
    echo "   Handle OK - Status: $status\n";

    echo "6. Terminando...\n";
    $kernel->terminate($input, $status);

    exit($status);
    
} catch (\Throwable $e) {
    echo "\n=== ERROR CAPTURADO ===\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    exit(255);
}
