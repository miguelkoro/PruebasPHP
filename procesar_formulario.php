<!-- filepath: /e:/Git Repository/PruebasPHP/procesar_formulario.php -->
<?php
function validarTexto($texto) {
    // Permitir solo letras, números, espacios y algunos caracteres básicos
    return preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $texto);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar campos de texto
    $nombre = htmlspecialchars($_POST['nombre']);
    $apellidos = htmlspecialchars($_POST['apellidos']);
    $edad = intval($_POST['edad']);
    $genero = htmlspecialchars($_POST['genero']);
    $deportes = isset($_POST['deportes']) ? $_POST['deportes'] : [];

    if (!validarTexto($nombre) || !validarTexto($apellidos)) {
        die("<h1>Error: Los campos de texto contienen caracteres no permitidos.</h1>");
    }

    // Validar la foto
    $fotoValida = false;
    $fotoRuta = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $fotoNombre = $_FILES['foto']['name'];
        $fotoTmp = $_FILES['foto']['tmp_name'];
        $fotoTamaño = $_FILES['foto']['size'];
        $fotoTipo = mime_content_type($fotoTmp);

        // Validar tamaño (máximo 5MB) y formato (jpg o png)
        if ($fotoTamaño <= 5 * 1024 * 1024 && ($fotoTipo == "image/jpeg" || $fotoTipo == "image/png")) {
            $fotoDestino = "uploads/" . $fotoNombre;

            // Crear la carpeta "uploads" si no existe
            if (!is_dir("uploads")) {
                mkdir("uploads", 0777, true);
            }

            // Mover la foto al destino
            if (move_uploaded_file($fotoTmp, $fotoDestino)) {
                $fotoValida = true;
                $fotoRuta = $fotoDestino;
            } else {
                die("<h1>Error al subir la foto.</h1>");
            }
        } else {
            die("<h1>Error: La foto debe ser JPG o PNG y no superar los 5MB.</h1>");
        }
    } else {
        die("<h1>Error: No se subió ninguna foto o hubo un error.</h1>");
    }

    // Mostrar los datos procesados
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Datos del Formulario</title>
        <link rel='stylesheet' href='styles.css'>
    </head>
    <body>
        <h1>Datos del Formulario</h1>
        <div class='form'>
            <div class='form-row'>
                <div class='form-group'>
                    <label>Nombre:</label>
                    <p>$nombre</p>
                </div>
                <div class='form-group'>
                    <label>Apellidos:</label>
                    <p>$apellidos</p>
                </div>
            </div>
            <div class='form-row'>
                <div class='form-group'>
                    <label>Edad:</label>
                    <p>$edad</p>
                </div>
                <div class='form-group'>
                    <label>Género:</label>
                    <p>$genero</p>
                </div>
            </div>
            <div class='form-row'>
                <div class='form-group'>
                    <label>Foto:</label>
                    <img src='$fotoRuta' alt='Foto subida' style='max-width: 150px; border-radius: 8px;'>
                </div>
            </div>
            <div class='form-section'>
                <label>Deportes seleccionados:</label>
                <p>" . implode(", ", $deportes) . "</p>
            </div>
        </div>
    </body>
    </html>";
} else {
    echo "<h1>Método no permitido.</h1>";
}
?>