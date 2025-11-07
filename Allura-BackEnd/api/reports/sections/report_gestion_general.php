<?php
// ===========================================
// 📊 Reporte de Gestión General
// ===========================================

require_once(__DIR__ . "/../../../config/database.php");

// Variables heredadas desde pdf.php
$fecha_desde = $from;
$fecha_hasta = $to;
$fecha_impresion = date("d/m/Y H:i");

// ===========================================
// 🧾 1️⃣ Resumen Ejecutivo
// ===========================================
$sqlResumen = "
SELECT
  SUM(CASE WHEN order_type = 'venta' THEN total ELSE 0 END) AS total_ventas,
  SUM(CASE WHEN order_type = 'compra' THEN total ELSE 0 END) AS total_compras,
  SUM(CASE WHEN order_type = 'venta' THEN abono ELSE 0 END) AS total_cobrado,
  SUM(CASE WHEN order_type = 'compra' THEN abono ELSE 0 END) AS total_pagado,
  SUM(CASE WHEN order_type = 'venta' AND estado = 'Pendiente' THEN (total - abono) ELSE 0 END) AS cuentas_por_cobrar,
  SUM(CASE WHEN order_type = 'compra' AND estado = 'Pendiente' THEN (total - abono) ELSE 0 END) AS cuentas_por_pagar,
  (
    SUM(CASE WHEN order_type = 'venta' THEN abono ELSE 0 END)
    - SUM(CASE WHEN order_type = 'compra' THEN abono ELSE 0 END)
  ) AS ganancia_neta
FROM orders
WHERE created_at BETWEEN :desde AND :hasta;
";
$stmt = $pdo->prepare($sqlResumen);
$stmt->execute([':desde' => $fecha_desde, ':hasta' => $fecha_hasta]);
$resResumen = $stmt->fetch(PDO::FETCH_ASSOC);

// ===========================================
// 🧾 2️⃣ Comparativo Mensual (Ventas vs Compras vs Ganancia)
// ===========================================
$sqlComparativo = "
SELECT 
  DATE_FORMAT(created_at, '%Y-%m') AS mes,
  SUM(CASE WHEN order_type = 'venta' THEN total ELSE 0 END) AS total_ventas,
  SUM(CASE WHEN order_type = 'compra' THEN total ELSE 0 END) AS total_compras,
  (
    SUM(CASE WHEN order_type = 'venta' THEN total ELSE 0 END) -
    SUM(CASE WHEN order_type = 'compra' THEN total ELSE 0 END)
  ) AS ganancia
FROM orders
WHERE created_at BETWEEN :desde AND :hasta
GROUP BY mes
ORDER BY mes ASC;
";
$stmt = $pdo->prepare($sqlComparativo);
$stmt->execute([':desde' => $fecha_desde, ':hasta' => $fecha_hasta]);
$resComparativo = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===========================================
// 🖨️ 3️⃣ Generar PDF
// ===========================================
$pdf->SetFont("Arial", "B", 16);
$pdf->Cell(0, 10, utf8_decode("REPORTE DE GESTIÓN GENERAL - ALLURA"), 0, 1, "C");
$pdf->SetFont("Arial", "", 11);
$pdf->Cell(0, 6, utf8_decode("Período: $fecha_desde al $fecha_hasta"), 0, 1, "C");
$pdf->Cell(0, 6, utf8_decode("Generado el: $fecha_impresion"), 0, 1, "C");
$pdf->Ln(10);

// ===========================================
// 🔹 Sección 1: Resumen Ejecutivo
// ===========================================
$pdf->SetFont("Arial", "B", 13);
$pdf->Cell(0, 8, utf8_decode("1. Resumen Ejecutivo"), 0, 1);
$pdf->SetFont("Arial", "", 11);
$pdf->MultiCell(0, 6, utf8_decode(
  "El Resumen Ejecutivo reúne en un solo documento los indicadores más importantes de Allura: ventas totales, compras, cuentas por cobrar, cuentas por pagar y ganancia neta. Es una visión rápida del estado general del negocio."
));
$pdf->Ln(5);

$pdf->SetFont("Arial", "B", 11);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(70, 8, "Indicador", 1, 0, "C", true);
$pdf->Cell(70, 8, "Monto (RD$)", 1, 1, "C", true);

$pdf->SetFont("Arial", "", 11);
$pdf->Cell(70, 8, "Ventas Totales", 1);
$pdf->Cell(70, 8, number_format($resResumen["total_ventas"], 2), 1, 1, "R");
$pdf->Cell(70, 8, "Compras Totales", 1);
$pdf->Cell(70, 8, number_format($resResumen["total_compras"], 2), 1, 1, "R");
$pdf->Cell(70, 8, "Cuentas por Cobrar", 1);
$pdf->Cell(70, 8, number_format($resResumen["cuentas_por_cobrar"], 2), 1, 1, "R");
$pdf->Cell(70, 8, "Cuentas por Pagar", 1);
$pdf->Cell(70, 8, number_format($resResumen["cuentas_por_pagar"], 2), 1, 1, "R");
$pdf->SetFont("Arial", "B", 11);
$pdf->Cell(70, 8, "Ganancia Neta", 1);
$pdf->Cell(70, 8, number_format($resResumen["ganancia_neta"], 2), 1, 1, "R");
$pdf->Ln(10);

// ===========================================
// 🔹 Sección 2: Comparativo Mensual
// ===========================================
$pdf->SetFont("Arial", "B", 13);
$pdf->Cell(0, 8, utf8_decode("2. Comparativo Mensual (Ventas vs Compras vs Ganancia)"), 0, 1);
$pdf->SetFont("Arial", "", 11);
$pdf->MultiCell(0, 6, utf8_decode(
  "Este reporte compara mes a mes el desempeño general de Allura, mostrando cómo se comportaron las ventas frente a las compras y cuál fue el margen de ganancia alcanzado. Ideal para analizar tendencias y crecimiento."
));
$pdf->Ln(5);

// Encabezado tabla
$pdf->SetFont("Arial", "B", 11);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(40, 8, "Mes", 1, 0, "C", true);
$pdf->Cell(45, 8, "Ventas", 1, 0, "C", true);
$pdf->Cell(45, 8, "Compras", 1, 0, "C", true);
$pdf->Cell(45, 8, "Ganancia", 1, 1, "C", true);

// Datos
$pdf->SetFont("Arial", "", 10);
foreach ($resComparativo as $fila) {
  $pdf->Cell(40, 8, utf8_decode($fila["mes"]), 1);
  $pdf->Cell(45, 8, "RD$ " . number_format($fila["total_ventas"], 2), 1, 0, "R");
  $pdf->Cell(45, 8, "RD$ " . number_format($fila["total_compras"], 2), 1, 0, "R");
  $pdf->Cell(45, 8, "RD$ " . number_format($fila["ganancia"], 2), 1, 1, "R");
}
$pdf->Ln(15);

// ===========================================
// 🧾 Footer
// ===========================================
$pdf->SetFont("Arial", "I", 10);
$pdf->Cell(0, 8, utf8_decode("Reporte generado automáticamente por el sistema Allura."), 0, 1, "C");

?>
