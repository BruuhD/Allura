<?php
header('Content-Type: application/json');
include('../config/database.php');
include('../utils/response.php');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(false, "Método no permitido");
    exit;
}

try {
    // 🔹 Ventas pagadas
    $ventasPagadasStmt = $pdo->query("
        SELECT COALESCE(SUM(total),0) 
        FROM orders 
        WHERE order_type = 'venta' AND estado = 'Pagado'
    ");
    $totalVentasPagadas = (float) $ventasPagadasStmt->fetchColumn();

    // 🔹 Ventas pendientes (por cobrar)
    $ventasPendientesStmt = $pdo->query("
        SELECT COALESCE(SUM(total),0) 
        FROM orders 
        WHERE order_type = 'venta' AND estado = 'Pendiente'
    ");
    $totalVentasPendientes = (float) $ventasPendientesStmt->fetchColumn();

    // 🔹 Compras pagadas
    $comprasPagadasStmt = $pdo->query("
        SELECT COALESCE(SUM(total),0) 
        FROM orders 
        WHERE order_type = 'compra' AND estado = 'Pagado'
    ");
    $totalComprasPagadas = (float) $comprasPagadasStmt->fetchColumn();

    // 🔹 Compras pendientes (por pagar)
    $comprasPendientesStmt = $pdo->query("
        SELECT COALESCE(SUM(total),0) 
        FROM orders 
        WHERE order_type = 'compra' AND estado = 'Pendiente'
    ");
    $totalComprasPendientes = (float) $comprasPendientesStmt->fetchColumn();

    // 🔹 Ganancia neta = ventas pagadas - compras pagadas
    $gananciaNeta = $totalVentasPagadas - $totalComprasPagadas;

    // 🔹 Venta neta = total de ventas pagadas
    $ventaNeta = $totalVentasPagadas;

    // 🔹 Total por cobrar a clientes = ventas pendientes
    $totalPorCobrar = $totalVentasPendientes;

    // 🔹 Total por pagar a suplidores = compras pendientes
    $totalPorPagar = $totalComprasPendientes;

    // 🔹 Gráfico mensual (ventas)
    $graficoStmt = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%b') AS mes,
            SUM(total) AS total
        FROM orders
        WHERE order_type = 'venta' AND estado = 'Pagado'
        GROUP BY MONTH(created_at)
        ORDER BY MONTH(created_at)
    ");
    $grafico = $graficoStmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [
        "ganancia_neta" => number_format($gananciaNeta, 2),
        "venta_neta"    => number_format($ventaNeta, 2),
        "total_cobrar"  => number_format($totalPorCobrar, 2),
        "total_pagar"   => number_format($totalPorPagar, 2),
        "grafico"       => $grafico
    ];

    sendResponse(true, "Dashboard general obtenido correctamente", $data);

} catch (Exception $e) {
    sendResponse(false, "Error al generar dashboard: " . $e->getMessage());
}
?>
