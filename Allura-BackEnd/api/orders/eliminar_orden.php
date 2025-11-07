<?php
header("Content-Type: application/json; charset=utf-8");
require_once(__DIR__ . "/../../config/database.php");

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data["order_number"])) {
        echo json_encode(["status" => false, "msg" => "Falta el número de orden."]);
        exit;
    }

    $order_number = (int) $data["order_number"];

    $stmt = $pdo->prepare("
        SELECT id, nfc_code, rnc 
        FROM orders 
        WHERE order_number = :order_number AND order_type = 'compra'
    ");
    $stmt->execute([":order_number" => $order_number]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(["status" => false, "msg" => "La orden de compra no existe."]);
        exit;
    }

    $order_id = $order["id"];
    $nfc_code = $order["nfc_code"];

    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
    $stmt->execute([":id" => $order_id]);

    if (!empty($nfc_code)) {
        $stmtNfc = $pdo->prepare("
            UPDATE nfc_codes
            SET used = 0,
                assigned_order_id = NULL,
                used_at = NULL
            WHERE code = :code
        ");
        $stmtNfc->execute([":code" => $nfc_code]);
    }

    echo json_encode([
        "status" => true,
        "msg" => "Orden de compra eliminada correctamente y código NFC liberado."
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => false,
        "msg" => "Error al eliminar: " . $e->getMessage()
    ]);
}
?>
