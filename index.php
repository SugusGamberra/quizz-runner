<?php
//proceso el form d inicio d sesion, comprueba si se ha escrito el nombre
//si es asi se guarda una cookie para recordarte x 24h
//si no se escribe nada tira un error para avisar en pantalla
$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre =$_POST['nombre'] ?? '';
    if (!empty($nombre)) {
        setcookie("usuario", $nombre, time() + 86400, "/");
        header("Location: quiz.php");
        exit;
    } else {
        $error = "Syntax Error: Faltan datos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Quizz Runner</title>
    <!--he definido la estetica general d las ventanitas inspirandome en
    https://www.ascii-magic.com/ascii/syntax-highlight-pass y para no ocupar
    tantisimo espacio lo he puesto en una hoja d estilos asi como el fondo q
    lo he sacado de https://www.ascii-magic.com/ascii/harbour-neon-skyline-->
    <link rel="stylesheet" href="estilos.css">
    <script type="module" src="https://www.ascii-magic.com/ascii/ascii.js"></script>
</head>
<body>
    <!-- para la estetica he usado d inspiracion el proyecto
    https://www.ascii-magic.com/ascii/login-form
    lo he adaptado para las necesidades del proyecto-->
    <ascii-art piece="harbour-neon-skyline" look="dots"></ascii-art>
    <div class="ventanita">
        <div class="cabecera-ventana">
            <span class="puntos"><span class="punto-rojo">●</span><span class="punto-amarillo">●</span><span class="punto-verde">●</span></span>
            <span class="titulo-ventana">quizz.php</span>
        </div>
        <div class="contenedor-login">
            <div class="tarjeta-login">
                <h2>Quizz Runner<span class="etiqueta-workspace">workspace</span></h2>
                <h3>Welcome back</h3>
                <p class="subtitulo">Ingresa tu nombre para continuar</p>
                <form action="index.php" method="POST">
                    <div class="grupo-formulario">
                        <label>Nombre de usuario</label>
                        <input type="text" name="nombre" placeholder="ej. SugusGamberra" required autocomplete="off">
                    </div>
                    <!--aqui muestra el error en pantalla-->
                    <?php if($error) echo "<p class='mensaje-error-login'>$error</p>"; ?>
                    <button type="submit">Sign in &nbsp;→</button>
                </form>
            </div>
        </div>
        <div class="pie-ventana">
            <span class="modo">-- INSERT --</span>
            <span>Ln 1, Col 1 &nbsp; UTF-8 &nbsp; Plain Text</span>
        </div>
    </div>
</body>
</html>