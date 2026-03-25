<?php
/**
 * Script de verificación de compliance legal
 * Verifica que los fichajes se conservan tras dar de baja usuarios
 */

require 'vendor/autoload.php';
require 'bootstrap/app.php';

echo "=== VERIFICACIÓN DE COMPLIANCE LEGAL ===\n\n";

// 1. Verificar usuarios activos vs dados de baja
$usuarios_activos = App\User::where('baja', 0)->count();
$usuarios_baja = App\User::where('baja', 1)->count();

echo "📊 USUARIOS:\n";
echo "  - Activos: {$usuarios_activos}\n";
echo "  - Dados de baja: {$usuarios_baja}\n";
echo "  - Total: " . ($usuarios_activos + $usuarios_baja) . "\n\n";

// 2. Verificar fichajes totales
$fichajes_totales = App\Fichaje::count();
echo "📋 FICHAJES TOTALES: {$fichajes_totales}\n\n";

// 3. Verificar fichajes de usuarios dados de baja
$usuarios_dados_baja = App\User::where('baja', 1)->get();
$fichajes_usuarios_baja = 0;

foreach ($usuarios_dados_baja as $usuario) {
    $fichajes_count = App\Fichaje::where('user_id', $usuario->id)->count();
    if ($fichajes_count > 0) {
        echo "👤 Usuario: {$usuario->name} (ID: {$usuario->id})\n";
        echo "   - Estado: DADO DE BAJA\n";
        echo "   - Fichajes conservados: {$fichajes_count}\n";
        $fichajes_usuarios_baja += $fichajes_count;
    }
}

if ($fichajes_usuarios_baja > 0) {
    echo "\n✅ COMPLIANCE OK: {$fichajes_usuarios_baja} fichajes conservados de usuarios dados de baja\n";
} else {
    echo "\n⚠️  No hay usuarios dados de baja con fichajes (normal si no se ha probado aún)\n";
}

// 4. Verificar integridad de datos
echo "\n🔍 VERIFICACIÓN DE INTEGRIDAD:\n";
$fichajes_huerfanos = App\Fichaje::whereNotIn('user_id', App\User::pluck('id'))->count();

if ($fichajes_huerfanos == 0) {
    echo "  ✅ Todos los fichajes tienen usuario asociado\n";
} else {
    echo "  ❌ PROBLEMA: {$fichajes_huerfanos} fichajes sin usuario (posible eliminación física anterior)\n";
}

echo "\n=== RESULTADO ===\n";
if ($fichajes_huerfanos == 0) {
    echo "✅ SISTEMA CONFORME A LEY - Todos los registros se conservan correctamente\n";
} else {
    echo "⚠️  REVISAR - Hay fichajes huérfanos de eliminaciones físicas anteriores\n";
}

echo "\n🎯 Para probar: Da de baja un usuario y ejecuta este script de nuevo\n";
?>