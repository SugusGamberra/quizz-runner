<?php
//para evitar errores x si el usuario no ha pasado por index.php y no tiene cookie
//ni sesion le doy valores x defecto y evito q se rompa nada
session_start();
require_once 'config.php';
$nombre = $_COOKIE['usuario'] ?? 'Invitado';
//por si acaso para cumplir con los requisitos de la tarea he metido 3
//preguntas hardcodeadas y el resto d supabase como t comente
$preguntas_hardcoded = [
    [
        'id' => 'h1', 'pregunta' => '¿Qué etiqueta define la apertura y cierre de código PHP?',
        'opciones' => [
            ['id' => 'a', 'texto' => '<script>'],
            ['id' => 'b', 'texto' => '<?php ?>'],
            ['id' => 'c', 'texto' => '<php>']
        ],
        'correcta' => 'b', 'explicacion' => 'Las etiquetas <?php y ?> son las estándar en PHP.'
    ],
    [
        'id' => 'h2', 'pregunta' => '¿Qué superglobal se utiliza para recibir datos ocultos?',
        'opciones' => [
            ['id' => 'a', 'texto' => '$_GET'],
            ['id' => 'b', 'texto' => '$_POST'],
            ['id' => 'c', 'texto' => '$_COOKIE']
        ],
        'correcta' => 'b', 'explicacion' => 'La superglobal $_POST recoge los datos enviados en el cuerpo HTTP.'
    ],
    [
        'id' => 'h3', 'pregunta' => '¿Por qué la puntuación del quiz debe calcularse en el servidor PHP?',
        'opciones' => [
            ['id' => 'a', 'texto' => 'Por rendimiento del navegador'],
            ['id' => 'b', 'texto' => 'Por seguridad (evitar manipulación)'],
            ['id' => 'c', 'texto' => 'Porque el HTML no soporta formularios']
        ],
        'correcta' => 'b', 'explicacion' => 'Evita que el usuario manipule las variables mediante JS.'
    ]
];
//consulto la bbdd para traer las preguntas y mezclarlas con las hardcodeadas
$todas_las_db = consultarSupabase('/rest/v1/preguntas?select=*&asignatura_id=eq.marcas');
//aqui elijo preguntas fijas y el resto seria aleatorias
$fijas_ids = [1, 2, 3, 4, 5];
$preguntas_fijas = [];
$resto_preguntas = [];
//separo las preguntas fijas del resto q serian aleatorias
foreach ($todas_las_db as $item) {
    if (in_array($item['id'], $fijas_ids)) {
        $preguntas_fijas[] = $item;
    } else {
        $resto_preguntas[] = $item;
    }
}
//mezvlo las fijas con 2 aleatorias del resto y luego junto todo con las hardcodaedas
shuffle($resto_preguntas);
$preguntas_random = array_slice($resto_preguntas, 0, 2);
$preguntas_supabase = array_merge($preguntas_fijas, $preguntas_random);
//y las guardo en un array para luego guardarlas en $_SESSION como pide el reto
$lista_final = array_merge($preguntas_hardcoded, $preguntas_supabase);

$_SESSION['datos_preguntas'] = [];
foreach ($lista_final as $p) {
    $pregunta_key = 'q_' . $p['id'];
    $_SESSION['datos_preguntas'][$pregunta_key] = [
        'texto_pregunta' => $p['pregunta'],
        'correcta' => $p['correcta'],
        'explicacion' => $p['explicacion'] ?? 'Sin explicación.'
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>quizz.php</title>
    <link rel="stylesheet" href="estilos.css">
    <script type="module" src="https://www.ascii-magic.com/ascii/ascii.js"></script>
</head>
<body>
    <ascii-art piece="harbour-neon-skyline" look="dots"></ascii-art>
    <div class="ventanita">
        <div class="cabecera-ventana">
            <span class="puntos"><span class="punto-rojo">●</span><span class="punto-amarillo">●</span><span class="punto-verde">●</span></span>
            <span class="titulo-ventana">quiz.php</span>
        </div>
        <div class="cuerpo-editor">
            <div class="numeros-linea" id="gutter-lines">
                <?php for($i=1; $i<=15; $i++) echo "$i<br>"; ?>
            </div>
            <div class="contenido-codigo">
                <!--doy la bienvenida al usuario con su nombre-->
                <div class="bloque-bienvenida-codigo">
                    <span class="palabra-clave">export function</span> runTest() {<br>
                    &nbsp;&nbsp;<span class="funcion-saludo">console</span>.log("<span class="string">Welcome back, <?php echo htmlspecialchars($nombre); ?>!</span>");<br>
                    }
                </div>
                <!--aqui empieza el form del quiz con las preguntas y opciones q bebe de lo importado
                y hecho en el php
                para cada campo del form le doy un nombre unico (Q_id) y comprueba si las opciones d
                respuesta vienen como texto plano o como jason para convertirlas correctamente en
                un array q php pueda leer
                -->
                <form action="procesar.php" method="POST" id="quiz-form">
                    <?php foreach ($lista_final as $index => $p): ?>
                        <?php 
                            $field_name = 'q_' . $p['id'];
                            $opciones = is_string($p['opciones']) ? json_decode($p['opciones'], true) : $p['opciones'];
                        ?>
                        <div class="tarjeta-pregunta" data-index="<?php echo $index; ?>">
                            <div class="comentario-pregunta">
                                // Q[<?php echo $index + 1; ?>/10]: <?php echo htmlspecialchars($p['pregunta']); ?>
                            </div>
                            <!--este comprueba si la pregunta tiene una imagen xk en mi bbdd
                            x las preguntas del año pasado tiene imagenes, si hay pues la pinta y si no
                            se salta esto-->
                            <?php if (!empty($p['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($p['imagen']); ?>" alt="img" class="imagen-soporte"><br>
                            <?php endif; ?>
                            <!--recoorro las respuestas d la pregunta y creo los botones d opcion con sus textos
                            para q el usuario pueda marcar la q quiera-->
                            <div class="contenedor-opciones">
                                <?php foreach ($opciones as $opcion): ?>
                                    <label class="linea-radio">
                                        <input type="radio" name="<?php echo $field_name; ?>" value="<?php echo htmlspecialchars($opcion['id']); ?>">
                                        <span><?php echo htmlspecialchars($opcion['texto']); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <button type="button" id="btn-siguiente" class="boton-ejecutar">return next() &nbsp;→</button>
                    <button type="submit" id="btn-enviar" class="boton-ejecutar boton-enviar-quiz">return submit()</button>
                    <!--aqui quise integrar el aviso dentro d la ventanita-->
                    <div id="error-msg" class="mensaje-error-quiz">
                        // Syntax Error: Selecciona una pregunta para avanzar
                    </div>
                </form>
            </div>
        </div>
        <div class="pie-ventana">
            <span class="modo" id="status-text">-- NORMAL --</span>
            <span id="footer-info">Ln 5, Col 12 &nbsp; UTF-8 &nbsp; PHP</span>
        </div>
    </div>
    <script>
        //le meto un script para q le de vida, q no aparezcan todas las preguntas
        //de sopeton, detecta los botones, barra d estado y mensaje d aviso de error
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.tarjeta-pregunta');
            const btnSiguiente = document.getElementById('btn-siguiente');
            const btnEnviar = document.getElementById('btn-enviar');
            const statusText = document.getElementById('status-text');
            const footerInfo = document.getElementById('footer-info');
            const errorMsg = document.getElementById('error-msg');
            let current = 0;
            //controlo el avance mostrando solo la pregunta en la q estes en el momento
            //y ocultaa el resto
            //actualizo los textos d la barra d estado para q parezca q estas
            //yendo x las lineas del editor y cambia el boton de siguiente por enviar
            //cuando llegas a la ultima pregunta
            function showCard(idx) {
                cards.forEach((c, i) => c.style.display = (i === idx) ? 'block' : 'none');
                statusText.innerText = `-- PREGUNTA ${idx + 1} --`;
                footerInfo.innerText = `Ln ${8 + (idx*2)}, Col 4   UTF-8   PHP`;
                errorMsg.style.display = 'none';
                if (idx === cards.length - 1) {
                    btnSiguiente.style.display = 'none';
                    btnEnviar.style.display = 'block';
                } else {
                    btnSiguiente.style.display = 'block';
                    btnEnviar.style.display = 'none';
                }
            }
            //cuando cambias d opcion en la pregunta desaparece el mensaje de error
            cards.forEach(card => {
                const inputs = card.querySelectorAll('input[type="radio"]');
                inputs.forEach(input => {
                    input.addEventListener('change', () => {
                        errorMsg.style.display = 'none';
                    });
                });
            });
            //valido que al darle a siguiente se haya seleccionado alguna opcion
            //si esta en blanco bloquea el paso y muestra el error
            //si marcas pues pasa sa la siguiente  pagina
            btnSiguiente.addEventListener('click', () => {
                const inputs = cards[current].querySelectorAll('input[type="radio"]');
                let checked = false;
                inputs.forEach(i => { if(i.checked) checked = true; });
                
                if(!checked) {
                    errorMsg.style.display = 'block';
                    return;
                }

                errorMsg.style.display = 'none';
                if (current < cards.length - 1) {
                    current++;
                    showCard(current);
                }
            });
            if(cards.length > 0) showCard(0);
        });
    </script>
</body>
</html>