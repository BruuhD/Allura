<?php
header('Content-Type: application/json');
include('../../config/database.php');
include('../../utils/response.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, "Método no permitido");
}

$input = json_decode(file_get_contents("php://input"), true);

// Campos principales
$name             = trim($input['name'] ?? '');
$email            = trim($input['email'] ?? '');
$phone            = trim($input['phone'] ?? '');
$ruc              = trim($input['ruc'] ?? '');
$bank_name        = trim($input['bank_name'] ?? '');
$gender           = trim($input['gender'] ?? '');
$commercial_name  = trim($input['commercial_name'] ?? '');
$product          = trim($input['product'] ?? '');

// Validar obligatorios mínimos
if (empty($name) || empty($ruc) || empty($gender) || empty($product)) {
    sendResponse(false, "Campos obligatorios incompletos (nombre, RNC, género y producto).");
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO clients 
        (name, commercial_name, gender, product, ruc, phone, email, bank_name, active, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
    ");
    $stmt->execute([
        $name,
        !empty($commercial_name) ? $commercial_name : null,
        $gender,
        $product,
        $ruc,
        !empty($phone) ? $phone : null,
        !empty($email) ? $email : null,
        !empty($bank_name) ? $bank_name : null
    ]);

    sendResponse(true, "Cliente agregado correctamente.");
} catch (Exception $e) {
    sendResponse(false, "Error al agregar cliente: " . $e->getMessage());
}
?>
