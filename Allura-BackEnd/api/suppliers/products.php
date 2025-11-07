<?php
header('Content-Type: application/json; charset=utf-8');
include('../../config/database.php');
include('../../utils/response.php');

$ruc = $_GET['ruc'] ?? null;

if (!$ruc) {
    sendResponse(false, "Debe enviar el parámetro 'ruc'.");
    exit;
}

try {
    // ============================================================
    // 🟢 1️⃣ Productos provenientes de órdenes (con order_items)
    // ============================================================
    $stmt = $pdo->prepare("
        SELECT 
            o.id AS order_id,
            o.order_number,
            DATE_FORMAT(o.created_at, '%Y-%m-%d %H:%i:%s') AS fecha_orden,
            oi.product_name AS producto,
            oi.unit AS unidad,
            oi.price AS precio,
            oi.quantity AS cantidad,
            (oi.price * oi.quantity) AS total
        FROM orders o
        INNER JOIN order_items oi ON oi.order_id = o.id
        WHERE o.rnc = :ruc
        ORDER BY o.id DESC, oi.product_name ASC
    ");
    $stmt->execute([':ruc' => $ruc]);
    $rowsOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ============================================================
    // 🟡 2️⃣ Productos registrados directamente en clients
    // ============================================================
    $stmt2 = $pdo->prepare("
        SELECT 
            c.name AS cliente,
            c.product AS producto,
            c.phone,
            c.created_at
        FROM clients c
        WHERE c.ruc = :ruc
    ");
    $stmt2->execute([':ruc' => $ruc]);
    $rowsClients = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // ============================================================
    // 🔹 Agrupar resultados por orden (sin duplicados)
    // ============================================================
    $productosAgrupados = [];

    foreach ($rowsOrders as $r) {
        $order = $r['order_number'] ?? 'SIN ORDEN';
        if (!isset($productosAgrupados[$order])) {
            $productosAgrupados[$order] = [
                'order_number' => $order,
                'fecha' => $r['fecha_orden'],
                'productos' => []
            ];
        }

        // Evitar duplicados
        $clave = strtolower(trim($r['producto'] . $r['precio'] . $r['cantidad']));
        $yaExiste = false;
        foreach ($productosAgrupados[$order]['productos'] as $p) {
            if (strtolower(trim($p['producto'] . $p['precio'] . $p['cantidad'])) === $clave) {
                $yaExiste = true;
                break;
            }
        }

        if (!$yaExiste) {
            $productosAgrupados[$order]['productos'][] = [
                'producto' => $r['producto'],
                'unidad'   => $r['unidad'],
                'cantidad' => $r['cantidad'],
                'precio'   => $r['precio'],
                'total'    => $r['total']
            ];
        }
    }

    // ============================================================
    // 🔹 Si no tiene órdenes, mostrar productos de clients
    // ============================================================
    if (empty($rowsOrders) && !empty($rowsClients)) {
        $productos = [];
        foreach ($rowsClients as $r) {
            // Si tiene varios productos separados por comas
            $items = array_filter(array_map('trim', explode(',', $r['producto'] ?? '')));
            foreach ($items as $it) {
                $productos[] = [
                    'producto' => $it,
                    'unidad'   => '—',
                    'cantidad' => 1,
                    'precio'   => 0,
                    'total'    => 0
                ];
            }
        }

        $productosAgrupados[] = [
            'order_number' => 'SIN ORDEN',
            'fecha' => $rowsClients[0]['created_at'] ?? null,
            'productos' => $productos
        ];
    }

    // ============================================================
    // 🔹 Enviar respuesta
    // ============================================================
    if (empty($productosAgrupados)) {
        sendResponse(true, "No hay productos asociados a este cliente.", []);
    } else {
        sendResponse(true, "Productos obtenidos correctamente.", array_values($productosAgrupados));
    }

} catch (Exception $e) {
    sendResponse(false, "Error al obtener productos: " . $e->getMessage());
}
?>
