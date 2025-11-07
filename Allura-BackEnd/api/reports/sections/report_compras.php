<?php
// ==========================================
// 📊 Reporte de Compras y Proveedores - Allura
// ==========================================

require_once(__DIR__ . "/../../../config/database.php");

// Variables recibidas desde pdf.php
$fecha_desde = $from;
$fecha_hasta = $to;
$fecha_impresion = date("d/m/Y H:i");

// ==========================================
// 🧾 1️⃣ Cuentas por Pagar
// ==========================================
$sqlPendientes = "
SELECT 
  proveedor AS proveedor,
  rnc,
  total,
  abono,
  (total - abono) AS pendiente,
  estado,
  created_at
FROM orders
WHERE order_type = 'compra'
  AND estado != 'Pagado'
  AND created_at BETWEEN :desde AND :hasta
ORDER BY created_at DESC;
";
$stmt = $pdo->prepare($sqlPendientes);
$stmt->execute([':desde' => $fecha_desde, ':hasta' => $fecha_hasta]);
$resPendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// 🧾 2️⃣ Historial de Compras (Agrupado por número de orden)
// ==========================================
$sqlHistorialAgrupado = "
SELECT 
  o.order_number,
  o.proveedor AS proveedor,
  o.total,
  o.abono,
  o.estado,
  COUNT(i.id) AS cantidad_productos,
  SUM(i.quantity) AS total_unidades
FROM orders o
LEFT JOIN order_items i ON o.id = i.order_id
WHERE o.order_type = 'compra'
  AND o.created_at BETWEEN :desde AND :hasta
GROUP BY o.id
ORDER BY o.created_at DESC;
";
$stmt = $pdo->prepare($sqlHistorialAgrupado);
$stmt->execute([':desde' => $fecha_desde, ':hasta' => $fecha_hasta]);
$resHistorialAgrupado = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// 🧾 3️⃣ Historial de Compras por Proveedor
// ==========================================
$sqlPorProveedor = "
SELECT 
  proveedor AS proveedor,
  rnc,
  COUNT(*) AS total_compras,
  SUM(total) AS monto_total,
  SUM(abono) AS total_abonado,
  SUM(total - abono) AS saldo_pendiente
FROM orders
WHERE order_type = 'compra'
  AND created_at BETWEEN :desde AND :hasta
GROUP BY proveedor, rnc
ORDER BY monto_total DESC;
";
$stmt = $pdo->prepare($sqlPorProveedor);
$stmt->execute([':desde' => $fecha_desde, ':hasta' => $fecha_hasta]);
$resPorProveedor = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// 🧾 4️⃣ Pagos a Proveedores
// ==========================================
$sqlAbonos = "
SELECT 
  proveedor AS proveedor,
  rnc,
  abono,
  total,
  (total - abono) AS restante,
  estado,
  created_at
FROM orders
WHERE order_type = 'compra'
  AND abono > 0
  AND created_at BETWEEN :desde AND :hasta
ORDER BY created_at DESC;
";
$stmt = $pdo->prepare($sqlAbonos);
$stmt->execute([':desde' => $fecha_desde, ':hasta' => $fecha_hasta]);
$resAbonos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// 🧾 5️⃣ Productos Más Comprados
// ==========================================
$sqlTopProductos = "
SELECT 
  i.product_name,
  SUM(i.quantity) AS total_comprado,
  SUM(i.quantity * i.price) AS costo_total
FROM orders o
INNER JOIN order_items i ON o.id = i.order_id
WHERE o.order_type = 'compra'
  AND o.created_at BETWEEN :desde AND :hasta
GROUP BY i.product_name
ORDER BY total_comprado DESC
LIMIT 5;
";
$stmt = $pdo->prepare($sqlTopProductos);
$stmt->execute([':desde' => $fecha_desde, ':hasta' => $fecha_hasta]);
$topProductos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// 🖨️ Generación del PDF
// ==========================================
$pdf->SetFont("Arial", "B", 16);
$pdf->Cell(0, 10, utf8_decode("REPORTE DE COMPRAS Y PROVEEDORES - ALLURA"), 0, 1, "C");
$pdf->SetFont("Arial", "", 11);
$pdf->Cell(0, 6, utf8_decode("Período: $fecha_desde al $fecha_hasta"), 0, 1, "C");
$pdf->Cell(0, 6, utf8_decode("Generado el: $fecha_impresion"), 0, 1, "C");
$pdf->Ln(10);

// ==========================================
// 🔹 Sección 1: Cuentas por Pagar
// ==========================================
$pdf->SetFont("Arial", "B", 13);
$pdf->Cell(0, 8, utf8_decode("1. Cuentas por Pagar"), 0, 1);
$pdf->SetFont("Arial", "", 11);
$pdf->MultiCell(0, 6, utf8_decode("Muestra las compras pendientes de pago a los proveedores. Incluye el total, abono actual y saldo restante."));
$pdf->Ln(5);

$pdf->SetFont("Arial", "B", 11);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(45, 8, "Proveedor", 1, 0, "C", true);
$pdf->Cell(35, 8, "RNC", 1, 0, "C", true);
$pdf->Cell(30, 8, "Total", 1, 0, "C", true);
$pdf->Cell(30, 8, "Abono", 1, 0, "C", true);
$pdf->Cell(30, 8, "Pendiente", 1, 0, "C", true);
$pdf->Cell(25, 8, "Estado", 1, 1, "C", true);

$pdf->SetFont("Arial", "", 10);
foreach ($resPendientes as $row) {
    $pdf->Cell(45, 8, utf8_decode($row["proveedor"]), 1);
    $pdf->Cell(35, 8, utf8_decode($row["rnc"]), 1);
    $pdf->Cell(30, 8, "RD$ " . number_format($row["total"], 2), 1, 0, "R");
    $pdf->Cell(30, 8, "RD$ " . number_format($row["abono"], 2), 1, 0, "R");
    $pdf->Cell(30, 8, "RD$ " . number_format($row["pendiente"], 2), 1, 0, "R");
    $pdf->Cell(25, 8, utf8_decode($row["estado"]), 1, 1, "C");
}
$pdf->Ln(10);

// ==========================================
// 🔹 Sección 2: Historial de Compras agrupadas
// ==========================================
$pdf->SetFont("Arial", "B", 13);
$pdf->Cell(0, 8, utf8_decode("2. Historial de Compras por Número de Orden"), 0, 1);
$pdf->SetFont("Arial", "", 11);
$pdf->MultiCell(0, 6, utf8_decode("Resumen de compras agrupadas por orden, mostrando totales y unidades sin detallar cada producto."));
$pdf->Ln(5);

$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(25, 8, "Orden #", 1, 0, "C", true);
$pdf->Cell(45, 8, "Proveedor", 1, 0, "C", true);
$pdf->Cell(25, 8, "Productos", 1, 0, "C", true);
$pdf->Cell(25, 8, "Unidades", 1, 0, "C", true);
$pdf->Cell(25, 8, "Total", 1, 0, "C", true);
$pdf->Cell(25, 8, "Abono", 1, 0, "C", true);
$pdf->Cell(25, 8, "Estado", 1, 1, "C", true);

$pdf->SetFont("Arial", "", 9);
foreach ($resHistorialAgrupado as $row) {
    $pdf->Cell(25, 8, str_pad($row["order_number"], 6, "0", STR_PAD_LEFT), 1, 0, "C");
    $pdf->Cell(45, 8, utf8_decode($row["proveedor"]), 1);
    $pdf->Cell(25, 8, $row["cantidad_productos"], 1, 0, "C");
    $pdf->Cell(25, 8, $row["total_unidades"], 1, 0, "C");
    $pdf->Cell(25, 8, "RD$ " . number_format($row["total"], 2), 1, 0, "R");
    $pdf->Cell(25, 8, "RD$ " . number_format($row["abono"], 2), 1, 0, "R");
    $pdf->Cell(25, 8, utf8_decode($row["estado"]), 1, 1, "C");
}
$pdf->Ln(10);

// ==========================================
// 🔹 Sección 3: Historial por Proveedor
// ==========================================
$pdf->SetFont("Arial", "B", 13);
$pdf->Cell(0, 8, utf8_decode("3. Historial de Compras por Proveedor"), 0, 1);
$pdf->SetFont("Arial", "", 11);
$pdf->MultiCell(0, 6, utf8_decode("Resumen agrupado por proveedor, mostrando montos totales, abonos y saldos pendientes."));
$pdf->Ln(5);

$pdf->SetFont("Arial", "B", 11);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(50, 8, "Proveedor", 1, 0, "C", true);
$pdf->Cell(30, 8, "RNC", 1, 0, "C", true);
$pdf->Cell(30, 8, "Compras", 1, 0, "C", true);
$pdf->Cell(30, 8, "Total", 1, 0, "C", true);
$pdf->Cell(30, 8, "Abonado", 1, 0, "C", true);
$pdf->Cell(30, 8, "Pendiente", 1, 1, "C", true);

$pdf->SetFont("Arial", "", 10);
foreach ($resPorProveedor as $row) {
    $pdf->Cell(50, 8, utf8_decode($row["proveedor"]), 1);
    $pdf->Cell(30, 8, utf8_decode($row["rnc"]), 1);
    $pdf->Cell(30, 8, $row["total_compras"], 1, 0, "C");
    $pdf->Cell(30, 8, "RD$ " . number_format($row["monto_total"], 2), 1, 0, "R");
    $pdf->Cell(30, 8, "RD$ " . number_format($row["total_abonado"], 2), 1, 0, "R");
    $pdf->Cell(30, 8, "RD$ " . number_format($row["saldo_pendiente"], 2), 1, 1, "R");
}
$pdf->Ln(10);

// ==========================================
// 🔹 Sección 4: Pagos a Proveedores
// ==========================================
$pdf->SetFont("Arial", "B", 13);
$pdf->Cell(0, 8, utf8_decode("4. Pagos a Proveedores"), 0, 1);
$pdf->SetFont("Arial", "", 11);
$pdf->MultiCell(0, 6, utf8_decode("Relación de compras con abonos realizados. Incluye el total del pedido, monto abonado, saldo restante y estado actual."));
$pdf->Ln(5);

$pdf->SetFont("Arial", "B", 11);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(50, 8, "Proveedor", 1, 0, "C", true);
$pdf->Cell(30, 8, "RNC", 1, 0, "C", true);
$pdf->Cell(30, 8, "Total", 1, 0, "C", true);
$pdf->Cell(30, 8, "Abono", 1, 0, "C", true);
$pdf->Cell(30, 8, "Restante", 1, 0, "C", true);
$pdf->Cell(30, 8, "Estado", 1, 1, "C", true);

$pdf->SetFont("Arial", "", 10);
foreach ($resAbonos as $row) {
    $pdf->Cell(50, 8, utf8_decode($row["proveedor"]), 1);
    $pdf->Cell(30, 8, utf8_decode($row["rnc"]), 1);
    $pdf->Cell(30, 8, "RD$ " . number_format($row["total"], 2), 1, 0, "R");
    $pdf->Cell(30, 8, "RD$ " . number_format($row["abono"], 2), 1, 0, "R");
    $pdf->Cell(30, 8, "RD$ " . number_format($row["restante"], 2), 1, 0, "R");
    $pdf->Cell(30, 8, utf8_decode($row["estado"]), 1, 1, "C");
}
$pdf->Ln(10);

// ==========================================
// 🔹 Sección 5: Top Productos Más Comprados
// ==========================================
if ($topProductos) {
    $pdf->SetFont("Arial", "B", 13);
    $pdf->Cell(0, 8, utf8_decode("5. Top Productos Más Comprados"), 0, 1);
    $pdf->SetFont("Arial", "", 11);
    $pdf->MultiCell(0, 6, utf8_decode("Listado de los productos con mayor volumen de compras durante el período seleccionado, ordenados de mayor a menor cantidad."));
    $pdf->Ln(5);

    $pdf->SetFont("Arial", "B", 11);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(15, 8, "#", 1, 0, "C", true);
    $pdf->Cell(80, 8, "Producto", 1, 0, "C", true);
    $pdf->Cell(40, 8, "Unidades Compradas", 1, 0, "C", true);
    $pdf->Cell(45, 8, "Costo Total", 1, 1, "C", true);

    $pdf->SetFont("Arial", "", 10);
    $rank = 1;
    foreach ($topProductos as $row) {
        if ($rank === 1) {
            $pdf->SetFillColor(255, 245, 200);
            $fill = true;
        } else {
            $pdf->SetFillColor(255, 255, 255);
            $fill = false;
        }

        $pdf->Cell(15, 8, $rank, 1, 0, "C", $fill);
        $pdf->Cell(80, 8, utf8_decode($row["product_name"]), 1, 0, "L", $fill);
        $pdf->Cell(40, 8, $row["total_comprado"], 1, 0, "C", $fill);
        $pdf->Cell(45, 8, "RD$ " . number_format($row["costo_total"], 2), 1, 1, "R", $fill);

        $rank++;
    }

    $pdf->Ln(10);
}

// ==========================================
// 🧾 Footer
// ==========================================
$pdf->SetFont("Arial", "I", 10);
$pdf->Cell(0, 8, utf8_decode("Reporte generado automáticamente por el sistema Allura."), 0, 1, "C");

?>
