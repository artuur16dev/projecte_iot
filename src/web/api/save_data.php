<?php
/**
 * SmartSchool IoT – API de recepció de dades
 * Endpoint: POST /api/save_data.php
 *
 * Paràmetres comuns:
 *   tipus      => 'ambient' | 'assistencia'
 *   aula       => ID de l'aula (p. ex. 'A01')
 *
 * Paràmetres per tipus=ambient:
 *   temperatura, humitat, llum, co2, presencia, alerta
 *
 * Paràmetres per tipus=assistencia:
 *   uid        => UID de la targeta RFID
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
    echo json_encode(['ok' => false, 'error' => 'Error de connexió BD']);
    exit;
}

$tipus = $_POST['tipus'] ?? '';
$aula  = trim($_POST['aula'] ?? '');

if (empty($tipus) || empty($aula)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Falten paràmetres']);
    exit;
}

switch ($tipus) {

    case 'ambient':
        $temperatura = (float)($_POST['temperatura'] ?? 0);
        $humitat     = (float)($_POST['humitat']     ?? 0);
        $llum        = (int)  ($_POST['llum']        ?? 0);
        $co2         = (int)  ($_POST['co2']         ?? 0);
        $presencia   = (int)  ($_POST['presencia']   ?? 0);
        $alerta      = trim(  $_POST['alerta']       ?? 'cap');

        $sql = 'INSERT INTO dades_ambientals
                    (aula_id, temperatura, humitat, llum, co2, presencia, alerta)
                VALUES
                    (:aula, :temperatura, :humitat, :llum, :co2, :presencia, :alerta)';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':aula'        => $aula,
            ':temperatura' => $temperatura,
            ':humitat'     => $humitat,
            ':llum'        => $llum,
            ':co2'         => $co2,
            ':presencia'   => $presencia,
            ':alerta'      => $alerta,
        ]);

        echo json_encode(['ok' => true, 'missatge' => 'Dades ambientals desades']);
        break;

    case 'assistencia':
        $uid = strtoupper(trim($_POST['uid'] ?? ''));

        if (empty($uid)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'UID buit']);
            exit;
        }

        $stmtAl = $pdo->prepare('SELECT id FROM alumnes WHERE uid_rfid = :uid');
        $stmtAl->execute([':uid' => $uid]);
        $alumne = $stmtAl->fetch(PDO::FETCH_ASSOC);
        $alumneId = $alumne ? (int)$alumne['id'] : null;

        $sql = 'INSERT INTO assistencia (aula_id, uid_rfid, alumne_id)
                VALUES (:aula, :uid, :alumne_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':aula'      => $aula,
            ':uid'       => $uid,
            ':alumne_id' => $alumneId,
        ]);

        $nom = $alumne ? 'Alumne registrat' : 'UID desconegut (registrat igualment)';
        echo json_encode(['ok' => true, 'missatge' => $nom, 'uid' => $uid]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Tipus desconegut']);
        break;
}
