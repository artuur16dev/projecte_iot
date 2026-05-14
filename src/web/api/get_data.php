<?php
/**
 * SmartSchool IoT – API de lectura de dades pel dashboard
 * Endpoint: GET /api/get_data.php?accio=...
 *
 * accio=ambient     => Última lectura per aula
 * accio=assistencia => Assistència del dia
 * accio=alertes     => Últimes alertes (no 'cap')
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smartschool');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false]);
    exit;
}

$accio = $_GET['accio'] ?? '';

switch ($accio) {

    case 'ambient':
        $stmt = $pdo->query('SELECT * FROM ultima_lectura_aula ORDER BY aula_id');
        echo json_encode(['ok' => true, 'dades' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        break;

    case 'assistencia':
        $stmt = $pdo->query('SELECT * FROM assistencia_avui LIMIT 100');
        echo json_encode(['ok' => true, 'dades' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        break;

    case 'alertes':
        $stmt = $pdo->query(
            "SELECT aula_id, alerta, timestamp FROM dades_ambientals
             WHERE alerta != 'cap'
             ORDER BY timestamp DESC LIMIT 20"
        );
        echo json_encode(['ok' => true, 'dades' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Acció desconeguda']);
        break;
}
