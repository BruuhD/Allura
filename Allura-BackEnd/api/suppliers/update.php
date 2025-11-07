<?php
header('Content-Type: application/json');
include('../../config/database.php');
include('../../utils/response.php');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    sendResponse(false, "Método no permitido");
}

$input = json_decode(file_get_contents("php://input"), true);
$id = $input['id'] ?? null;

if (!$id) {
    sendResponse(false, "ID del suplidor requerido");
}

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$ruc = trim($input['ruc'] ?? '');
$address = trim($input['address'] ?? '');
$bank_name = trim($input['bank_name'] ?? '');
$gender = trim($input['gender'] ?? '');
$commercial_name = trim($input['commercial_name'] ?? '');
$product = trim($input['product'] ?? '');

if (empty($name) || empty($ruc)) {
    sendResponse(false, "Nombre y RUC son obligatorios");
}

try {
    $stmt = $pdo->prepare("
        UPDATE suppliers
        SET name = ?, commercial_name = ?, gender = ?, product = ?, email = ?, phone = ?, ruc = ?, 
            address = ?, bank_name = ?, updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    $stmt->execute([
        $name,
        !empty($commercial_name) ? $commercial_name : null,
        $gender,
        $product,
        !empty($email) ? $email : null,
        !empty($phone) ? $phone : null,
        $ruc,
        !empty($address) ? $address : null,
        !empty($bank_name) ? $bank_name : null,
        $id
    ]);

    sendResponse(true, "Suplidor actualizado correctamente");
} catch (Exception $e) {
    sendResponse(false, "Error al actualizar suplidor: " . $e->getMessage());
}
?>
