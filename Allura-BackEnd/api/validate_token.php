<?php
header('Content-Type: application/json');
include('../config/database.php');
include('../utils/response.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, "Método no permitido");
}

$input = json_decode(file_get_contents("php://input"), true);
$token = trim($input['token'] ?? '');

if (empty($token)) {
    sendResponse(false, "Token requerido");
}

$stmt = $pdo->prepare("
    SELECT u.id, u.username, u.role, t.expires_at
    FROM user_tokens t
    INNER JOIN users u ON u.id = t.user_id
    WHERE t.token = ?
    LIMIT 1
");
$stmt->execute([$token]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    sendResponse(false, "Token inválido o no encontrado");
}

// Verificar expiración
if (strtotime($data['expires_at']) < time()) {
    sendResponse(false, "Token expirado");
}

sendResponse(true, "Token válido", [
    'user' => [
        'id' => $data['id'],
        'username' => $data['username'],
        'role' => $data['role']
    ],
    'expires_at' => $data['expires_at']
]);
?>
