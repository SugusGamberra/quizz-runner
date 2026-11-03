<?php
//inicia sesion para acceder a los datos guardados y recuperar toda la info
//(usuario, nota, preguntas, fallos)
//si alguna variable no existirea por lo que sea uso ?? para darle un valor
//x defecto y evitarn erroes
session_start();
$nombre = $_COOKIE['usuario'] ?? 'Invitado';
$puntuacion = $_SESSION['score'] ?? 0;
$total = $_SESSION['total_preguntas'] ?? 10;
$falladas = $_SESSION['falladas'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>results.log</title>
    <link rel="stylesheet" href="estilos.css">
    <script type="module" src="https://www.ascii-magic.com/ascii/ascii.js"></script>
</head>
<body>
    <ascii-art piece="harbour-neon-skyline" look="dots"></ascii-art>
    <div class="ventanita">
        <div class="cabecera-ventana">
            <span class="puntos"><span class="punto-rojo">●</span><span class="punto-amarillo">●</span><span class="punto-verde">●</span></span>
            <span class="titulo-ventana">results.log</span>
        </div>
        <div class="cuerpo-editor">
            <div class="numeros-linea" id="gutter-lines">
                <?php for($i=1; $i<=15; $i++) echo "$i<br>"; ?>
            </div>
            <div class="contenido-codigo">
                <div class="bloque-funcion-resultados">
                    <span class="palabra-clave">function</span> <span class="nombre-funcion">showResults</span>() {<br>
                    &nbsp;&nbsp;<span class="comentario-usuario">/* User: <?php echo htmlspecialchars($nombre); ?> */</span><br>
                    }
                </div>
                <div class="comentario-proceso">
                    // Process finished with exit code 0
                </div>
                <!--imprimo la puntuacion-->
                <h1 class="puntuacion-final">
                    Score: <?php echo $puntuacion; ?> / <?php echo $total; ?>
                </h1>
                <!--si se aciertan todas se felicita, si no muestro los fallos
                y le meto la explicacion del xk-->
                <?php if ($puntuacion === $total): ?>
                    <p class="mensaje-exito">[ OK ] ¡Perfecto! Ejecución sin errores.</p>
                <?php elseif (!empty($falladas)): ?>
                    <div class="caja-errores">
                        <span class="titulo-errores">> ERRORES DETECTADOS:</span><br><br>
                        <?php foreach ($falladas as $fallo): ?>
                            <div class="item-error">
                                <span class="pregunta-error">Q: <?php echo htmlspecialchars($fallo['pregunta']); ?></span><br>
                                <span class="solucion-error">FIX: <?php echo htmlspecialchars($fallo['explicacion']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="contenedor-botones-resultado">
                    <a href="quiz.php" class="boton-ejecutar boton-reintentar">return retry() →</a>
                    <a href="index.php" class="boton-ejecutar boton-salir">logout() ✕</a>
                </div>
            </div>
        </div>
        <div class="pie-ventana">
            <span class="modo">-- DONE --</span>
            <span>Ln 12, Col 1 &nbsp; UTF-8 &nbsp; Log</span>
        </div>
    </div>
</body>
</html>