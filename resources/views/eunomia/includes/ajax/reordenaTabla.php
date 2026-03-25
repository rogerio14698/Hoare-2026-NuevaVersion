<?php

$id = $_POST['id'];
$oldPosition = $_POST['oldPosition'];
$newPosition = $_POST['newPosition'];
$tabla = $_POST['tabla'];
//print_r($_POST);
//echo "id: ".$id." oldPosition: ".$oldPosition." newPosition: ".$newPosition;

$mysqli = new mysqli('146.255.96.44', 'gscadmin', 'Ebkc08^7', 'gijonsecome17bd');


if ($mysqli->connect_errno) {
    // La conexión falló. ¿Que vamos a hacer?
    // Se podría contactar con uno mismo (¿email?), registrar el error, mostrar una bonita página, etc.
    // No se debe revelar información delicada

    // Probemos esto:
    echo "Lo sentimos, este sitio web está experimentando problemas.";

    // Algo que no se debería de hacer en un sitio público, aunque este ejemplo lo mostrará
    // de todas formas, es imprimir información relacionada con errores de MySQL -- se podría registrar
    echo "Error: Fallo al conectarse a MySQL debido a: \n";
    echo "Errno: " . $mysqli->connect_errno . "\n";
    echo "Error: " . $mysqli->connect_error . "\n";

    // Podría ser conveniente mostrar algo interesante, aunque nosotros simplemente saldremos
    exit;
}

if ($newPosition > $oldPosition) {
    $sql = "UPDATE " . $tabla . " SET orden=orden-1 WHERE orden<=" . $newPosition . " and orden>" . $oldPosition;
} else {
    $sql = "UPDATE " . $tabla . " SET orden=orden+1 WHERE orden>=" . $newPosition . " and orden<" . $oldPosition;
}

if (!$resultado = $mysqli->query($sql)) {
    // ¡Oh, no! La consulta falló.
    echo "Lo sentimos, este sitio web está experimentando problemas.";

    // De nuevo, no hacer esto en un sitio público, aunque nosotros mostraremos
    // cómo obtener información del error
    echo "Error: La ejecución de la consulta falló debido a: \n";
    echo "Query: " . $sql . "\n";
    echo "Errno: " . $mysqli->errno . "\n";
    echo "Error: " . $mysqli->error . "\n";
    exit;
}

$sql = "UPDATE " . $tabla . " SET orden=".$newPosition." WHERE id=".$id;

if (!$resultado = $mysqli->query($sql)) {
    // ¡Oh, no! La consulta falló.
    echo "Lo sentimos, este sitio web está experimentando problemas.";

    // De nuevo, no hacer esto en un sitio público, aunque nosotros mostraremos
    // cómo obtener información del error
    echo "Error: La ejecución de la consulta falló debido a: \n";
    echo "Query: " . $sql . "\n";
    echo "Errno: " . $mysqli->errno . "\n";
    echo "Error: " . $mysqli->error . "\n";
    exit;
}

?>