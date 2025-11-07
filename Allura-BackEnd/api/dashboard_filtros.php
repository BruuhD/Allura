<?php
// ===============================================
// 📊 Endpoint: Dashboard - Filtros de resumen
// ===============================================

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include('../config/database.php');

try {
    // ==========================
    // 📥 Leer el JSON recibido
    // ==========================
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode([
            "status" => false,
            "message" => "No se recibieron datos válidos."
        ]);
        exit;
    }

    // ==========================
    // 🧩 Variables de entrada
    // ==========================
    $tipoSemanal     = $input["tipo_semanal"] ?? '';
    $valorSemanal    = $input["valor_semanal"] ?? '';
    $tipoMensual     = $input["tipo_mensual"] ?? '';
    $valorMensual    = $input["valor_mensual"] ?? '';
    $producto        = $input["producto"] ?? '';
    $clienteProducto = $input["cliente_producto"] ?? '';

    if (
        empty($tipoSemanal) || empty($valorSemanal) ||
        empty($tipoMensual) || empty($valorMensual) ||
        empty($producto) || empty($clienteProducto)
    ) {
        echo json_encode([
            "status" => false,
            "message" => "Faltan campos obligatorios en la solicitud."
        ]);
        exit;
    }

    // ==========================
    // 📅 RANGOS DE FECHA
    // ==========================
    $fechaSemanaInicio = date('Y-m-d', strtotime('-7 days'));
    $fechaSemanaFin    = date('Y-m-d 23:59:59');
    $fechaMesInicio    = date('Y-m-01');
    $fechaMesFin       = date('Y-m-d 23:59:59', strtotime('+1 month', strtotime($fechaMesInicio)));

    // ==========================
    // 🔍 Obtener nombres de entidades
    // ==========================
    $nombreSemanal = '';
    $nombreMensual = '';
    $nombreClienteProd = '';
    $nombreProducto = $producto;

    // 🔸 Cliente o Suplidor semanal
    if ($tipoSemanal === 'cliente') {
        $stmt = $pdo->prepare("SELECT name FROM clients WHERE ruc = :ruc LIMIT 1");
    } else {
        $stmt = $pdo->prepare("SELECT name FROM suppliers WHERE ruc = :ruc LIMIT 1");
    }
    $stmt->execute([':ruc' => $valorSemanal]);
    $nombreSemanal = $stmt->fetchColumn() ?: 'No encontrado';

    // 🔸 Cliente o Suplidor mensual
    if ($tipoMensual === 'cliente') {
        $stmt = $pdo->prepare("SELECT name FROM clients WHERE ruc = :ruc LIMIT 1");
    } else {
        $stmt = $pdo->prepare("SELECT name FROM suppliers WHERE ruc = :ruc LIMIT 1");
    }
    $stmt->execute([':ruc' => $valorMensual]);
    $nombreMensual = $stmt->fetchColumn() ?: 'No encontrado';

    // 🔸 Cliente del producto
    $stmt = $pdo->prepare("SELECT name FROM clients WHERE ruc = :ruc LIMIT 1");
    $stmt->execute([':ruc' => $clienteProducto]);
    $nombreClienteProd = $stmt->fetchColumn() ?: 'No encontrado';

    // 🔸 Producto
    $stmt = $pdo->prepare("SELECT name FROM products WHERE name = :nombre LIMIT 1");
    $stmt->execute([':nombre' => $producto]);
    $nombreProducto = $stmt->fetchColumn() ?: $producto;

    // ==========================
    // 🔹 1. Resumen Semanal
    // ==========================
    $sqlSemanal = "
        SELECT SUM(total) AS total
        FROM orders
        WHERE rnc = :rnc
          AND order_type = :tipo
          AND created_at BETWEEN :inicio AND :fin
    ";
    $stmt = $pdo->prepare($sqlSemanal);
    $stmt->execute([
        ':rnc' => $valorSemanal,
        ':tipo' => ($tipoSemanal === 'cliente') ? 'venta' : 'compra',
        ':inicio' => $fechaSemanaInicio,
        ':fin' => $fechaSemanaFin
    ]);
    $resumenSemanal = $stmt->fetchColumn() ?: 0;

    // ==========================
    // 🔹 2. Resumen Mensual
    // ==========================
    $sqlMensual = "
        SELECT SUM(total) AS total
        FROM orders
        WHERE rnc = :rnc
          AND order_type = :tipo
          AND created_at BETWEEN :inicio AND :fin
    ";
    $stmt = $pdo->prepare($sqlMensual);
    $stmt->execute([
        ':rnc' => $valorMensual,
        ':tipo' => ($tipoMensual === 'cliente') ? 'venta' : 'compra',
        ':inicio' => $fechaMesInicio,
        ':fin' => $fechaMesFin
    ]);
    $resumenMensual = $stmt->fetchColumn() ?: 0;

    // ==========================
    // 🔹 3. Cattleya → Producto
    // ==========================
    $sqlProducto = "
        SELECT SUM(oi.subtotal)
        FROM order_items oi
        INNER JOIN orders o ON o.id = oi.order_id
        WHERE oi.product_name = :producto
    ";
    $stmt = $pdo->prepare($sqlProducto);
    $stmt->execute([':producto' => $producto]);
    $resumenCattleya = $stmt->fetchColumn() ?: 0;

    // ==========================
    // 🔹 4. Toyota → Cliente del producto
    // ==========================
    $sqlClienteProd = "
        SELECT SUM(oi.subtotal)
        FROM order_items oi
        INNER JOIN orders o ON o.id = oi.order_id
        WHERE o.rnc = :rnc
          AND oi.product_name = :producto
    ";
    $stmt = $pdo->prepare($sqlClienteProd);
    $stmt->execute([
        ':rnc' => $clienteProducto,
        ':producto' => $producto
    ]);
    $resumenToyota = $stmt->fetchColumn() ?: 0;

    // ==========================
    // ✅ RESPUESTA FINAL
    // ==========================
    echo json_encode([
        "status" => true,
        "message" => "Datos de resumen obtenidos correctamente.",
        "data" => [
            "resumen_semanal" => [
                "total" => number_format($resumenSemanal, 2),
                "tipo" => $tipoSemanal,
                "rnc" => $valorSemanal,
                "nombre" => $nombreSemanal
            ],
            "resumen_mensual" => [
                "total" => number_format($resumenMensual, 2),
                "tipo" => $tipoMensual,
                "rnc" => $valorMensual,
                "nombre" => $nombreMensual
            ],
            "resumen_cattleya" => [
                "total" => number_format($resumenCattleya, 2),
                "producto" => $nombreProducto
            ],
            "resumen_toyota" => [
                "total" => number_format($resumenToyota, 2),
                "cliente" => $nombreClienteProd
            ]
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => false,
        "message" => "Error al obtener los datos: " . $e->getMessage()
    ]);
}
?>
