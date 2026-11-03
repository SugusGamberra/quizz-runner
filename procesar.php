<?php
//aqui recojo las respuestas marcadas, se corrigen en el lado del servidor, calcula
//la nota y guarda los fallos para pintarlos despues
//inicio sesion
session_start();
//comprueba q se ha enviado el form con POST, si no, lo saca
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: quiz.php");
    exit;
}
//cargo la sesion d preguntas y respuestas correctas que se guardan
$datos_preguntas = $_SESSION['datos_preguntas'] ?? [];
//recorre cada respuesta del usuario y las compara con las soluciones
//correctas, si coincide suma un punto y si no lo deja en blanco
function evaluarCuestionario($respuestas_usuario, $datos_preguntas) {
    $puntos = 0;
    $falladas = [];
    foreach ($datos_preguntas as $pregunta_key => $datos) {
        $respuesta_dada = $respuestas_usuario[$pregunta_key] ?? null;
        if ($respuesta_dada === $datos['correcta']) {
            $puntos++;
        } else {
            $falladas[] = [
                'pregunta' => $datos['texto_pregunta'],
                'explicacion' => $datos['explicacion']
            ];
        }
    }
    //devuelvo los puntos y los fallos
    return [
        'puntos' => $puntos,
        'falladas' => $falladas
    ];
}
$resultado = evaluarCuestionario($_POST, $datos_preguntas);
//guarda la puntuacion final, fallos, total d preguntas en las
//variables de sesion y redirige automaticamente a la pagina
//de resultados
$_SESSION['score'] = $resultado['puntos'];
$_SESSION['falladas'] = $resultado['falladas'];
$_SESSION['total_preguntas'] = count($datos_preguntas);
header("Location: resultado.php");
exit;
?>