<?php
//cargo el .env para sacar la direccion y la key d supabase sin exponerlas
//(confio en ti simona <3 hahah)
$env = parse_ini_file(__DIR__ . '/.env');
//guardo la url y la key en constantes globales para usarlas en cualquier lado
define('SUPABASE_URL', $env['SUPABASE_URL'] ?? '');
define('SUPABASE_KEY', $env['SUPABASE_KEY'] ?? '');
//preparo y ejecuto la llamada x http a la api rest d supabase
function consultarSupabase($endpoint) {
    $url = SUPABASE_URL . $endpoint;
    //inicializo apuntando a la url
    $ch = curl_init($url);
    //meto en las cabeceras la api y el token en json para q se identifique de forma segura
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY,
        'Content-Type: application/json'
    ]);
    //ejecuta la peti, cierra la conexion y convierte la respuesta recibida en
    //un array d php
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true) ?? [];
}
?>