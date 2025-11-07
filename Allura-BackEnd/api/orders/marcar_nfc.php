<?php
require_once("../../config/db.php");
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$nfc_code = $data["nfc_code"] ?? null;

if (!$nfc_code) {
    echo json_encode(["status" => false, "msg" => "Código NFC no recibido."]);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE nfc_codes SET usado = 1, fecha_uso = NOW() WHERE nfc = ?");
    $stmt->bind_param("s", $nfc_code);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => true, "msg" => "NFC marcado como usado."]);
    } else {
        echo json_encode(["status" => false, "msg" => "No se encontró el código NFC o ya estaba usado."]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => false, "msg" => "Error al marcar NFC: " . $e->getMessage()]);
}
?>
