<?php
header('Content-Type: application/json; charset=utf-8');
include __DIR__ . "/../../config/database.php";

try {
    if (!isset($_POST['order_number']) || !isset($_POST['monto']) || !isset($_FILES['file'])) {
        throw new Exception("Faltan parámetros obligatorios.");
    }

    $orderNumber = trim($_POST['order_number']);
    $montoAbono = floatval($_POST['monto']);
    $file = $_FILES['file'];

    if ($montoAbono <= 0) {
        throw new Exception("El monto abonado debe ser mayor a 0.");
    }

    $stmt = $pdo->prepare("SELECT id, total, abono, estado FROM orders WHERE order_number = ? AND order_type = 'venta'");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception("Orden de venta no encontrada.");
    }

    if (strtolower($order['estado']) === 'pagado') {
        throw new Exception("La orden de venta ya está pagada completamente.");
    }

    $abonoActual = floatval($order['abono']);
    $totalOrden = floatval($order['total']);
    $restante = $totalOrden - $abonoActual;

    if ($montoAbono > $restante) {
        throw new Exception("El monto abonado no puede superar el saldo pendiente (RD$ " . number_format($restante, 2) . ").");
    }

    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Solo se permiten archivos PDF o imágenes (JPG, PNG).");
    }

    $uploadsDir = __DIR__ . '/../../uploads/comprobantes/';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }

    $fileName = uniqid('comp_') . "_" . basename($file['name']);
    $filePath = $uploadsDir . $fileName;
    $fileUrl = "https://5185af65eb8a.ngrok-free.app/allura/allura-backend/uploads/comprobantes/" . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception("Error al subir el archivo.");
    }

    $nuevoAbono = $abonoActual + $montoAbono;
    $nuevoEstado = ($nuevoAbono >= $totalOrden) ? "Pagado" : "Pendiente";

    $update = $pdo->prepare("
        UPDATE orders 
        SET abono = ?, estado = ?, comprobante_url = ?
        WHERE order_number = ? AND order_type = 'venta'
    ");
    $update->execute([$nuevoAbono, $nuevoEstado, $fileUrl, $orderNumber]);

    $insertFile = $pdo->prepare("
        INSERT INTO order_files (order_number, file_url, amount)
        VALUES (?, ?, ?)
    ");
    $insertFile->execute([$orderNumber, $fileUrl, $montoAbono]);

    echo json_encode([
        "status" => true,
        "msg" => "Comprobante de venta guardado correctamente.",
        "nuevo_abono" => number_format($nuevoAbono, 2),
        "estado" => $nuevoEstado
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => false,
        "msg" => $e->getMessage()
    ]);
}
?>
