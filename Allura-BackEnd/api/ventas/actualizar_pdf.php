<?php
require_once("../../fpdf/fpdf.php");
include __DIR__ . "/../../config/database.php";

header('Content-Type: application/pdf');

$data = json_decode(file_get_contents("php://input"), true);
$order_number = $data["order_number"] ?? null;

if (!$order_number) {
    echo "Falta número de orden";
    exit;
}

$stmtOrder = $pdo->prepare("
    SELECT 
        order_number,
        CAST(nfc_code AS CHAR) AS nfc_code,
        proveedor,
        rnc,
        telefono,
        DATE(created_at) AS fecha
    FROM orders
    WHERE order_number = ? AND order_type = 'venta'
");
$stmtOrder->execute([$order_number]);
$order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Orden no encontrada";
    exit;
}

$stmtItems = $pdo->prepare("
    SELECT 
        product_name AS producto,
        unit AS unidad,
        price AS costo,
        quantity AS cantidad,
        (price * quantity) AS total
    FROM order_items
    WHERE order_id = (
        SELECT id FROM orders WHERE order_number = ? AND order_type = 'venta'
    )
");
$stmtItems->execute([$order_number]);
$items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

$uploadsDir = __DIR__ . '/../../uploads/ordenes/';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

$fileName = 'orden_venta_' . str_pad($order_number, 6, "0", STR_PAD_LEFT) . '.pdf';
$filePath = $uploadsDir . $fileName;
$fileUrl  = 'https://5185af65eb8a.ngrok-free.app/allura/allura-backend/uploads/ordenes/' . $fileName;

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(15, 10, 15);

$pdf->Image("../../fpdf/logo.png", 15, 10, 30);
$pdf->SetFont("Arial", "B", 16);
$pdf->Cell(0, 10, utf8_decode("ALLURA SOLUCIONES INTEGRALES"), 0, 1, "C");
$pdf->SetFont("Arial", "", 11);
$pdf->Cell(0, 6, utf8_decode("Correo: allurard@gmail.com | Tel: +1 809-000-0000"), 0, 1, "C");
$pdf->Ln(5);
$pdf->SetFont("Arial", "B", 14);
$pdf->Cell(0, 10, utf8_decode("ORDEN DE VENTA (ACTUALIZADA)"), 0, 1, "C");
$pdf->Ln(3);

$pdf->SetFont("Arial", "", 12);
$pdf->Cell(0, 8, utf8_decode("No. de Orden: ") . str_pad($order["order_number"], 6, "0", STR_PAD_LEFT), 0, 1, "R");
$pdf->Cell(0, 8, utf8_decode("Código NFC: " . ($order["nfc_code"] ?: "N/A")), 0, 1, "R");
$pdf->Ln(8);

$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(0, 8, utf8_decode("Datos del Cliente"), 0, 1);
$pdf->SetFont("Arial", "", 11);
$pdf->Cell(100, 6, utf8_decode("Cliente: ") . ($order["proveedor"] ?? ""), 0, 0);
$pdf->Cell(0, 6, utf8_decode("RNC / Cédula: ") . ($order["rnc"] ?? ""), 0, 1);
$pdf->Cell(100, 6, utf8_decode("Teléfono: ") . ($order["telefono"] ?? ""), 0, 0);
$pdf->Cell(0, 6, utf8_decode("Fecha: ") . ($order["fecha"] ?? date("d/m/Y")), 0, 1);
$pdf->Ln(8);

$pdf->SetFont("Arial", "B", 11);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(70, 8, "Producto", 1, 0, "C", true);
$pdf->Cell(20, 8, "Unidad", 1, 0, "C", true);
$pdf->Cell(25, 8, "Precio", 1, 0, "C", true);
$pdf->Cell(25, 8, "Cantidad", 1, 0, "C", true);
$pdf->Cell(35, 8, "Total", 1, 1, "C", true);

$pdf->SetFont("Arial", "", 11);
$totalGeneral = 0;

foreach ($items as $item) {
    $pdf->Cell(70, 8, utf8_decode($item["producto"]), 1);
    $pdf->Cell(20, 8, $item["unidad"], 1, 0, "C");
    $pdf->Cell(25, 8, "RD$ " . number_format($item["costo"], 2), 1, 0, "R");
    $pdf->Cell(25, 8, $item["cantidad"], 1, 0, "C");
    $pdf->Cell(35, 8, "RD$ " . number_format($item["total"], 2), 1, 1, "R");
    $totalGeneral += (float)$item["total"];
}

$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(140, 10, "Total General", 1, 0, "R");
$pdf->Cell(35, 10, "RD$ " . number_format($totalGeneral, 2), 1, 1, "R");
$pdf->Ln(15);

$pdf->SetFont("Arial", "", 12);
$pdf->Cell(90, 10, "Cliente:", 0, 0, "C");
$pdf->Cell(90, 10, "Vendedor:", 0, 1, "C");
$pdf->Ln(20);
$pdf->Cell(90, 10, "________________________", 0, 0, "C");
$pdf->Cell(90, 10, "________________________", 0, 1, "C");

if (file_exists($filePath)) {
    unlink($filePath);
}

$pdf->Output("F", $filePath);

try {
    $stmt = $pdo->prepare("UPDATE orders SET orden_pdf_url = :url WHERE order_number = :num");
    $stmt->execute([':url' => $fileUrl, ':num' => $order_number]);
} catch (Exception $e) {
    error_log("Error al registrar PDF actualizado: " . $e->getMessage());
}

$pdf->Output("I", $fileName);
?>
