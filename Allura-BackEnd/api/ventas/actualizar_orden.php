<?php
header('Content-Type: application/json; charset=utf-8');
include __DIR__ . "/../../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);
$order_number = $data['order_number'] ?? null;
$cliente = $data['proveedor'] ?? null;
$rnc = $data['rnc'] ?? null;
$telefono = $data['telefono'] ?? null;
$items = $data['items'] ?? [];

if (!$order_number) {
    echo json_encode(["status" => false, "msg" => "Falta número de orden"]);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        UPDATE orders 
        SET proveedor = ?, rnc = ?, telefono = ?, updated_at = NOW()
        WHERE order_number = ? AND order_type = 'venta'
    ");
    $stmt->execute([$cliente, $rnc, $telefono, $order_number]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Orden de venta no encontrada o tipo incorrecto");
    }

    $stmtId = $pdo->prepare("SELECT id FROM orders WHERE order_number = ?");
    $stmtId->execute([$order_number]);
    $order = $stmtId->fetch(PDO::FETCH_ASSOC);

    if (!$order) throw new Exception("Orden no encontrada");
    $order_id = $order['id'];

    $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order_id]);

    $stmtItem = $pdo->prepare("
        INSERT INTO order_items (order_id, product_name, unit, price, quantity)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($items as $item) {
        $stmtItem->execute([
            $order_id,
            $item['producto'] ?? '',
            $item['unidad'] ?? 'N/A',
            floatval($item['costo'] ?? 0),
            floatval($item['cantidad'] ?? 0)
        ]);
    }

    $stmtTotal = $pdo->prepare("
        UPDATE orders
        SET total = (
            SELECT IFNULL(SUM(price * quantity), 0) FROM order_items WHERE order_id = ?
        )
        WHERE id = ?
    ");
    $stmtTotal->execute([$order_id, $order_id]);

    $pdo->commit();

    echo json_encode([
        "status" => true,
        "msg" => "Orden de venta actualizada correctamente"
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        "status" => false,
        "msg" => "Error al actualizar: " . $e->getMessage()
    ]);
}
?>
