<?php
// ===============================================
// 📦 Endpoint: Obtener Clientes y Suplidores
// ===============================================
include('../config/database.php');

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

try {
    // =============================
    // CLIENTES
    // =============================
    $sqlClientes = "
        SELECT 
            c.id,
            c.name,
            c.ruc
        FROM clients c
        INNER JOIN (
            SELECT MIN(id) AS id_min
            FROM clients
            WHERE active = 1
            AND ruc IS NOT NULL
            AND ruc <> ''
            GROUP BY ruc
        ) AS t ON t.id_min = c.id
        ORDER BY c.name ASC;

    ";
    $stmt = $pdo->query($sqlClientes);
    $clientes = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $clientes[] = [
            'id' => $row['id'],
            'nombre' => $row['name'],
            'ruc' => $row['ruc']
        ];
    }

    // =============================
    // SUPLIDORES
    // =============================
    $sqlSuplidores = "
        SELECT 
            c.id,
            c.name,
            c.ruc
        FROM suppliers c
        INNER JOIN (
            SELECT MIN(id) AS id_min
            FROM suppliers
            WHERE active = 1
            AND ruc IS NOT NULL
            AND ruc <> ''
            GROUP BY ruc
        ) AS t ON t.id_min = c.id
        ORDER BY c.name ASC;
    ";
    $stmt = $pdo->query($sqlSuplidores);
    $suplidores = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $suplidores[] = [
            'id' => $row['id'],
            'nombre' => $row['name'],
            'ruc' => $row['ruc']
        ];
    }

    echo json_encode([
        'status' => true,
        'message' => 'Clientes y suplidores obtenidos correctamente.',
        'data' => [
            'clientes' => $clientes,
            'suplidores' => $suplidores
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Error al obtener datos: ' . $e->getMessage()
    ]);
}
?>
