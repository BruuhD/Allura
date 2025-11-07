<?php
header('Content-Type: application/json');
include('../config/database.php');
include('../utils/response.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, "Método no permitido");
}

$input = json_decode(file_get_contents("php://input"), true);
$username = trim($input['username'] ?? '');
$password = trim($input['password'] ?? '');

if (empty($username) || empty($password)) {
    sendResponse(false, "Usuario y contraseña requeridos");
}

// 🔹 Buscar usuario activo
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND active = 1 LIMIT 1");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    sendResponse(false, "Usuario no encontrado o inactivo");
}

// 🔹 Verificar contraseña
if ($user['password'] !== md5($password)) {
    sendResponse(false, "Contraseña incorrecta");
}

// 🔹 Generar token
$token = bin2hex(random_bytes(32));
$expiresAt = date('Y-m-d H:i:s', strtotime('+8 hour'));

$insert = $pdo->prepare("INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
$insert->execute([$user['id'], $token, $expiresAt]);

session_start();
$_SESSION['user'] = [
    'id' => $user['id'],
    'username' => $user['username'],
    'role' => $user['role'],
    'token' => $token
];

// 🔹 Obtener proveedores activos
$suppliersStmt = $pdo->prepare("
    SELECT 
        ID,
        NAME,
        COMMERCIAL_NAME,
        GENDER,
        PRODUCT,
        RUC,
        PHONE,
        EMAIL,
        ADDRESS
    FROM suppliers WHERE active = 1");
$suppliersStmt->execute();
$suppliers = $suppliersStmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Responder con todo
sendResponse(true, "Login exitoso", [
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role']
    ],
    'token' => $token,
    'expires_at' => $expiresAt,
    'suppliers' => $suppliers  
]);
?>
