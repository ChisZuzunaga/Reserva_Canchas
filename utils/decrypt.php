<?php
function getCredentials() {
    $encrypted = include(__DIR__ . '/../config/credentials_encrypted.php');
    
    if (!isset($encrypted['iv']) || !isset($encrypted['data'])) {
        echo "Error: Las claves 'iv' o 'data' no están presentes en el archivo.";
        return null; // O un array vacío, según prefieras
    }
    
    $encryption_key = '@4f6G!2l^8b#9Q1x$wT0mZ7yR&E3hY6p'; 
    $cipher = 'AES-256-CBC';
    $iv = base64_decode($encrypted['iv']);

    $decrypted = openssl_decrypt($encrypted['data'], $cipher, $encryption_key, 0, $iv);

    if ($decrypted === false) {
        echo "Error: Falló el descifrado.";
        return null; // O un array vacío
    }

    $credentialsArray = json_decode($decrypted, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error: Falló la decodificación JSON: " . json_last_error_msg();
        return null; // O un array vacío
    }

    return $credentialsArray;
}

?>
