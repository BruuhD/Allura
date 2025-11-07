<?php
// ===============================================
// 🛒 Endpoint: Obtener Productos (por tipo de orden) y Clientes
// ===============================================
include('../config/database.php');

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

try {
    // ===============================================
    // 🧾 Productos desde order_items + relación con orders
    // ===============================================
    $sqlProductos = "
        SELECT DISTINCT 
            i.product_name AS nombre,
            o.order_type
        FROM order_items i
        INNER JOIN orders o ON o.id = i.order_id
        WHERE i.product_name IS NOT NULL
          AND i.product_name <> ''
          AND o.order_type IN ('venta', 'compra')
        ORDER BY i.product_name ASC
    ";
    $stmt = $pdo->query($sqlProductos);

    $productos = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $productos[] = [
            'nombre' => $row['nombre'],
            'tipo' => $row['order_type'] === 'venta' ? 'Producto vendido' : 'Producto comprado'
        ];
    }

    // ===============================================
    // 👥 Clientes activos
    // ===============================================
    $sqlClientes = "
        SELECT 
            c.id,
            c.name,
            c.ruc
        FROM clients c
        INNER JOIN (
            SELECT MIN(id) AS id_min
            FROM clients
            WHERE active = 1
            AND ruc IS NOT NULL
            AND ruc <> ''
            GROUP BY ruc
        ) AS t ON t.id_min = c.id
        ORDER BY c.name ASC;
    ";
    $stmt = $pdo->query($sqlClientes);
    $clientes = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $clientes[] = [
            'id' => $row['id'],
            'nombre' => $row['name'],
            'ruc' => $row['ruc']
        ];
    }

    // ===============================================
    // 📦 Respuesta final
    // ===============================================
    echo json_encode([
        'status' => true,
        'message' => 'Productos y clientes obtenidos correctamente.',
        'data' => [
            'items' => $productos,
            'clientes' => $clientes
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Error al obtener datos: ' . $e->getMessage()
    ]);
}
?>
