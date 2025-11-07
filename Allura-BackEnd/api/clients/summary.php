<?php
header('Content-Type: application/json; charset=utf-8');
include('../../config/database.php');
include('../../utils/response.php');

try {
    // 🔹 Consulta total de VENTAS agrupadas por estado (Pagado / Pendiente)
    $stmt = $pdo->query("
        SELECT 
            estado,
            SUM(CASE 
                    WHEN estado = 'Pendiente' THEN (total - COALESCE(abono, 0))
                    ELSE total
                END) AS total_monto,
            COUNT(*) AS cantidad
        FROM orders
        WHERE order_type = 'venta'
        GROUP BY estado
    ");

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Inicializar totales
    $pendiente = 0;
    $pagado = 0;
    $cantidadPendiente = 0;
    $cantidadPagado = 0;

    foreach ($rows as $row) {
        $estado = strtolower(trim($row['estado']));

        if ($estado === 'pendiente') {
            $pendiente = floatval($row['total_monto']);
            $cantidadPendiente = intval($row['cantidad']);
        } elseif ($estado === 'pagado') {
            $pagado = floatval($row['total_monto']);
            $cantidadPagado = intval($row['cantidad']);
        }
    }

    sendResponse(true, "Resumen de ventas obtenido correctamente", [
        'pendiente' => [
            'monto' => $pendiente,
            'cantidad' => $cantidadPendiente
        ],
        'pagado' => [
            'monto' => $pagado,
            'cantidad' => $cantidadPagado
        ]
    ]);

} catch (Exception $e) {
    sendResponse(false, "Error al obtener resumen: " . $e->getMessage());
}
?>
