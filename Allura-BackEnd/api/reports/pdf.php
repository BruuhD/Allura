<?php
require_once("../../fpdf/fpdf.php");
include __DIR__ . "/../../config/database.php";

// ✅ Evitar salida previa al PDF
ob_clean();
header('Content-Type: application/pdf; charset=utf-8');
ini_set('display_errors', 0); // o 1 si deseas depurar

$data = json_decode(file_get_contents("php://input"), true);

$reportType = $data["report"] ?? "financiero";
$filters = $data["filters"] ?? [];

$from = $filters["from"] ?? date("Y-m-01");
$to   = $filters["to"] ?? date("Y-m-d");

// ✅ Selecciona orientación según el tipo de reporte
if ($reportType === 'compras' || $reportType === 'ventas') {
    $pdf = new FPDF('L', 'mm', 'A4'); // horizontal
} else {
    $pdf = new FPDF('P', 'mm', 'A4'); // vertical
}

$pdf->AddPage();
$pdf->SetMargins(15, 10, 15);
$pdf->SetFont("Arial", "B", 14);
$pdf->Cell(0, 10, utf8_decode("Reporte - ALLURA SOLUCIONES INTEGRALES"), 0, 1, "C");
$pdf->Ln(5);

$pdf->SetFont("Arial", "", 11);
$pdf->Cell(0, 6, utf8_decode("Tipo de Reporte: ") . ucfirst($reportType), 0, 1);
$pdf->Cell(0, 6, utf8_decode("Período: $from al $to"), 0, 1);
$pdf->Ln(8);

// 🔗 Incluir la sección correspondiente
switch ($reportType) {
    case "financiero":
        include __DIR__ . "/sections/report_finanzas.php";
        break;
    case "compras":
        include __DIR__ . "/sections/report_compras.php";
        break;
    case "ventas":
        include __DIR__ . "/sections/report_ventas.php";
        break;
    case "gestion":
        include __DIR__ . "/sections/report_gestion_general.php";
        break;
    default:
        $pdf->SetFont("Arial", "I", 12);
        $pdf->Cell(0, 8, utf8_decode("Tipo de reporte no reconocido."), 0, 1, "C");
        break;
}

// 🧾 Salida final
$pdf->Output("I", "Reporte_Allura_" . date("Ymd_His") . ".pdf");
exit;
?>
