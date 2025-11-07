<?php
include __DIR__ . "/../../config/database.php";
header('Content-Type: application/json; charset=utf-8');

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(["status" => false, "msg" => "Falta el parámetro order_id."]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            o.id AS order_id,
            o.order_number,
            o.nfc_code,
            o.proveedor AS cliente,   -- 🔹 mismo campo, pero representa el cliente
            o.rnc,
            o.telefono,
            o.total,
            o.estado,
            DATE_FORMAT(o.created_at, '%d/%m/%Y %H:%i') AS fecha_orden,
            oi.id AS item_id,
            oi.product_name,
            oi.unit,
            oi.quantity,
            oi.price,
            (oi.quantity * oi.price) AS subtotal,
            DATE_FORMAT(oi.created_at, '%d/%m/%Y %H:%i') AS fecha_item
        FROM orders o
        INNER JOIN order_items oi ON oi.order_id = o.id
        WHERE o.id = :order_id AND o.order_type = 'venta'
        ORDER BY oi.id ASC
    ");
    $stmt->execute([':order_id' => $order_id]);

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => true,
        "msg" => "Ítems de la orden de venta obtenidos correctamente.",
        "data" => $items
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => false,
        "msg" => "Error al listar ítems: " . $e->getMessage()
    ]);
}
?>
