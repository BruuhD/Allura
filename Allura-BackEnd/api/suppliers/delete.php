<?php
header('Content-Type: application/json');
include('../../config/database.php');
include('../../utils/response.php');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    sendResponse(false, "Método no permitido");
}

$input = json_decode(file_get_contents("php://input"), true);
$id = $input['id'] ?? null;

if (!$id) {
    sendResponse(false, "ID de suplidor requerido");
}

try {
    // Soft delete
    $stmt = $pdo->prepare("UPDATE suppliers SET active = 0 WHERE id = ?");
    $stmt->execute([$id]);

    sendResponse(true, "Suplidor eliminado correctamente");
} catch (Exception $e) {
    sendResponse(false, "Error al eliminar suplidor: " . $e->getMessage());
}
?>
