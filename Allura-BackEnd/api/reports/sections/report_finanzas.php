<?php
// =============================
// 📊 Reporte Financiero
// =============================

require_once(__DIR__ . "/../../../config/database.php");

// Se reutilizan las variables $pdf, $from y $to recibidas desde pdf.php
$fecha_desde = $from;
$fecha_hasta = $to;
$fecha_impresion = date("d/m/Y H:i");

// =============================
// 🧾 1. Consultas SQL
// =============================

// 🧾 Estado de Resultado (ventas, compras, ganancia)
$sqlResultado = "
SELECT
  SUM(CASE WHEN order_type = 'venta' AND estado = 'Pagado' THEN total ELSE 0 END) AS total_ventas,
  SUM(CASE WHEN order_type = 'compra' AND estado = 'Pagado' THEN total ELSE 0 END) AS total_compras,
  (SUM(CASE WHEN order_type = 'venta' AND estado = 'Pagado' THEN total ELSE 0 END) -
   SUM(CASE WHEN order_type = 'compra' AND estado = 'Pagado' THEN total ELSE 0 END)) AS ganancia_neta
FROM orders
WHERE created_at BETWEEN :desde AND :hasta;
";
$stmt = $pdo->prepare($sqlResultado);
$stmt->execute([':desde' => $fecha_desde, ':hasta' => $fecha_hasta]);
$resResultado = $stmt->fetch(PDO::FETCH_ASSOC);

// 💰 Flujo de Caja (por día)
$sqlFlujo = "
SELECT
  DATE(created_at) AS fecha,
  SUM(CASE WHEN order_type = 'venta' AND estado = 'Pagado' THEN total ELSE 0 END) AS ingresos,
  SUM(CASE WHEN order_type = 'compra' AND estado = 'Pagado' THEN total ELSE 0 END) AS egresos,
  (SUM(CASE WHEN order_type = 'venta' AND estado = 'Pagado' THEN total ELSE 0 END) -
   SUM(CASE WHEN order_type = 'compra' AND estado = 'Pagado' THEN total ELSE 0 END)) AS saldo_dia
FROM orders
WHERE created_at BETWEEN :desde AND :hasta
GROUP BY DATE(created_at)
ORDER BY fecha;
";
$stmt = $pdo->prepare($sqlFlujo);
$stmt->execute([':desde' => $fecha_desde, ':hasta' => $fecha_hasta]);
$resFlujo = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 📈 Resumen de Ganancia
$sqlResumen = "
SELECT
  COUNT(CASE WHEN order_type = 'venta' AND estado = 'Pagado' THEN id END) AS total_ventas_realizadas,
  SUM(CASE WHEN order_type = 'venta' AND estado = 'Pagado' THEN total ELSE 0 END) AS ingresos_totales,
  SUM(CASE WHEN order_type = 'compra' AND estado = 'Pagado' THEN total ELSE 0 END) AS costos_totales,
  (SUM(CASE WHEN order_type = 'venta' AND estado = 'Pagado' THEN total ELSE 0 END) -
   SUM(CASE WHEN order_type = 'compra' AND estado = 'Pagado' THEN total ELSE 0 END)) AS beneficio_neto,
  ROUND(
    (((
      SUM(CASE WHEN order_type = 'venta' AND estado = 'Pagado' THEN total ELSE 0 END) -
      SUM(CASE WHEN order_type = 'compra' AND estado = 'Pagado' THEN total ELSE 0 END)
    ) / NULLIF(SUM(CASE WHEN order_type = 'venta' AND estado = 'Pagado' THEN total ELSE 0 END), 0)) * 100), 2
  ) AS margen_operativo
FROM orders
WHERE created_at BETWEEN :desde AND :hasta;
";
$stmt = $pdo->prepare($sqlResumen);
$stmt->execute([':desde' => $fecha_desde, ':hasta' => $fecha_hasta]);
$resResumen = $stmt->fetch(PDO::FETCH_ASSOC);

// =============================
// 🖨️ 2. Escribir en PDF (usando $pdf del archivo principal)
// =============================

$pdf->SetFont("Arial", "B", 16);
$pdf->Cell(0, 10, utf8_decode("REPORTE FINANCIERO - ALLURA"), 0, 1, "C");
$pdf->SetFont("Arial", "", 11);
$pdf->Cell(0, 6, utf8_decode("Período: $fecha_desde al $fecha_hasta"), 0, 1, "C");
$pdf->Cell(0, 6, utf8_decode("Generado el: $fecha_impresion"), 0, 1, "C");
$pdf->Ln(10);

// ===== 1️⃣ Estado de Resultado =====
$pdf->SetFont("Arial", "B", 13);
$pdf->Cell(0, 8, utf8_decode("1. Estado de Resultado (Ganancia y Pérdida)"), 0, 1);
$pdf->SetFont("Arial", "", 11);
$pdf->MultiCell(0, 6, utf8_decode(
    "Este reporte presenta un resumen del desempeño financiero durante el período seleccionado. " .
    "Se muestran los ingresos generados, los costos asociados y la ganancia o pérdida neta resultante."
));
$pdf->Ln(5);
$pdf->SetFont("Arial", "", 12);
$pdf->Cell(95, 8, "Total Ventas:", 0, 0);
$pdf->Cell(0, 8, "RD$ " . number_format($resResultado["total_ventas"] ?? 0, 2), 0, 1, "R");
$pdf->Cell(95, 8, "Total Compras:", 0, 0);
$pdf->Cell(0, 8, "RD$ " . number_format($resResultado["total_compras"] ?? 0, 2), 0, 1, "R");
$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(95, 8, "Ganancia / Pérdida Neta:", 0, 0);
$pdf->Cell(0, 8, "RD$ " . number_format($resResultado["ganancia_neta"] ?? 0, 2), 0, 1, "R");
$pdf->Ln(10);

// ===== 2️⃣ Flujo de Caja =====
$pdf->SetFont("Arial", "B", 13);
$pdf->Cell(0, 8, utf8_decode("2. Flujo de Caja"), 0, 1);
$pdf->SetFont("Arial", "", 11);
$pdf->MultiCell(0, 6, utf8_decode(
    "A continuación se muestra cómo se ha movido el dinero dentro y fuera de la empresa durante el período analizado."
));
$pdf->Ln(5);

// Encabezado tabla flujo
$pdf->SetFont("Arial", "B", 11);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(40, 8, "Fecha", 1, 0, "C", true);
$pdf->Cell(45, 8, "Ingresos", 1, 0, "C", true);
$pdf->Cell(45, 8, "Egresos", 1, 0, "C", true);
$pdf->Cell(45, 8, "Saldo Día", 1, 1, "C", true);

$pdf->SetFont("Arial", "", 10);
foreach ($resFlujo as $fila) {
    $pdf->Cell(40, 8, utf8_decode($fila["fecha"]), 1);
    $pdf->Cell(45, 8, "RD$ " . number_format($fila["ingresos"], 2), 1, 0, "R");
    $pdf->Cell(45, 8, "RD$ " . number_format($fila["egresos"], 2), 1, 0, "R");
    $pdf->Cell(45, 8, "RD$ " . number_format($fila["saldo_dia"], 2), 1, 1, "R");
}
$pdf->Ln(10);

// ===== 3️⃣ Resumen de Ganancia =====
$pdf->SetFont("Arial", "B", 13);
$pdf->Cell(0, 8, utf8_decode("3. Resumen de Ganancia"), 0, 1);
$pdf->SetFont("Arial", "", 11);
$pdf->MultiCell(0, 6, utf8_decode(
    "Este resumen consolida las ganancias generadas en el período, mostrando el beneficio neto y el margen operativo."
));
$pdf->Ln(5);
$pdf->SetFont("Arial", "", 12);
$pdf->Cell(100, 8, "Total Ventas Realizadas:", 0, 0);
$pdf->Cell(0, 8, ($resResumen["total_ventas_realizadas"] ?? 0), 0, 1, "R");
$pdf->Cell(100, 8, "Ingresos Totales:", 0, 0);
$pdf->Cell(0, 8, "RD$ " . number_format($resResumen["ingresos_totales"] ?? 0, 2), 0, 1, "R");
$pdf->Cell(100, 8, "Costos Totales:", 0, 0);
$pdf->Cell(0, 8, "RD$ " . number_format($resResumen["costos_totales"] ?? 0, 2), 0, 1, "R");
$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(100, 8, "Beneficio Neto:", 0, 0);
$pdf->Cell(0, 8, "RD$ " . number_format($resResumen["beneficio_neto"] ?? 0, 2), 0, 1, "R");
$pdf->Cell(100, 8, "Margen Operativo:", 0, 0);
$pdf->Cell(0, 8, ($resResumen["margen_operativo"] ?? 0) . " %", 0, 1, "R");
$pdf->Ln(15);

// Footer
$pdf->SetFont("Arial", "I", 10);
$pdf->Cell(0, 8, utf8_decode("Reporte generado automáticamente por el sistema Allura."), 0, 1, "C");
