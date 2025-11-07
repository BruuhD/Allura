<?php
header('Content-Type: application/json; charset=utf-8');
include __DIR__ . "/../../config/database.php";

try {
    $ruc = $_GET['ruc'] ?? null;

    if (!$ruc) {
        echo json_encode(["status" => false, "msg" => "Falta el parámetro RUC del cliente."]);
        exit;
    }

    // 🔹 Consultar órdenes PENDIENTES del cliente según su RNC/RUC
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            o.order_number,
            o.nfc_code,  
            o.proveedor,
            o.rnc,
            o.telefono,
            o.abono,
            o.total,
            (o.total - IFNULL(o.abono, 0)) AS pendiente,
            o.estado,
            o.orden_pdf_url,
            o.comprobante_url,
            DATE_FORMAT(o.created_at, '%d/%m/%Y %H:%i') AS fecha,
            GROUP_CONCAT(oi.product_name SEPARATOR ', ') AS productos,
            SUM(oi.quantity) AS cantidad_total,
            SUM(oi.price * oi.quantity) AS total_items,

            (
                SELECT CONCAT(
                    '[', 
                    GROUP_CONCAT(
                        CONCAT(
                            '{\"url\":\"', f.file_url, '\",',
                            '\"monto\":', IFNULL(f.amount,0), ',',
                            '\"fecha\":\"', DATE_FORMAT(f.created_at, '%d/%m/%Y %H:%i'), '\"}'
                        )
                        SEPARATOR ','
                    ), 
                    ']'
                )
                FROM order_files f
                WHERE f.order_id = o.id
            ) AS comprobantes

        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.order_type = 'venta'
          AND o.estado = 'Pendiente'
          AND o.rnc = :ruc
        GROUP BY o.id
        ORDER BY o.id DESC
    ");

    $stmt->execute([':ruc' => $ruc]);
    $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => true,
        "msg" => "Órdenes pendientes obtenidas correctamente",
        "data" => $ordenes
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => false,
        "msg" => "Error al listar órdenes: " . $e->getMessage()
    ]);
}
?>
