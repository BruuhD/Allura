<?php
header("Content-Type: application/json; charset=utf-8");
include __DIR__ . "/../../config/database.php";

try {
    // ===========================
    // 📊 Verificar total y disponibles
    // ===========================
    $stmt = $pdo->query("SELECT COUNT(*) AS total, SUM(used = 0) AS disponibles FROM nfc_codes");
    $info = $stmt->fetch(PDO::FETCH_ASSOC);

    $total = (int) ($info['total'] ?? 0);
    $disponibles = (int) ($info['disponibles'] ?? 0);

    if ($total === 0) {
        echo json_encode([
            "status" => false,
            "msg" => "No se han cargado códigos NFC. Debe subir un lote antes de crear órdenes."
        ]);
        exit;
    }

    if ($disponibles === 0) {
        echo json_encode([
            "status" => false,
            "msg" => "Se han agotado los códigos NFC disponibles. Contacte al administrador."
        ]);
        exit;
    }

    // ===========================
    // 🔹 Obtener siguiente código libre
    // ===========================
    $stmt = $pdo->query("SELECT id, code FROM nfc_codes WHERE used = 0 ORDER BY id ASC LIMIT 1");
    $nfc = $stmt->fetch(PDO::FETCH_ASSOC);

    // ===========================
    // 🔹 Calcular el próximo número de orden
    // ===========================
    $stmtOrder = $pdo->query("SELECT COALESCE(MAX(order_number), 0) + 1 AS next_order FROM orders");
    $nextOrder = $stmtOrder->fetchColumn();
    $orderNumber = str_pad($nextOrder, 6, "0", STR_PAD_LEFT);

    // ===========================
    // ✅ Respuesta final
    // ===========================
    $response = [
        "status" => true,
        "msg" => "Código NFC disponible.",
        "nfc" => $nfc['code'],
        "order_number" => $orderNumber,
        "stats" => [
            "total" => $total,
            "disponibles" => $disponibles
        ]
    ];

    // ⚠️ Si quedan menos de 10 códigos, añadimos alerta
    if ($disponibles < 10) {
        $response["alerta"] = "Solo quedan {$disponibles} códigos NFC disponibles. Sube nuevos lo antes posible.";
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        "status" => false,
        "msg" => "Error al consultar NFC: " . $e->getMessage()
    ]);
}
?>
