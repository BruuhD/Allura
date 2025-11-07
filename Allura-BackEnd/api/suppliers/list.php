<?php
header('Content-Type: application/json; charset=utf-8');
include('../../config/database.php');
include('../../utils/response.php');

try {
    $stmt = $pdo->query("
        SELECT id, name, commercial_name, gender, product, ruc, phone, email, address, bank_name, active, created_at
        FROM suppliers
        WHERE active = 1
        ORDER BY id DESC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        sendResponse(true, "No hay suplidores registrados.", []);
        exit;
    }

    $grouped = [];

    foreach ($rows as $row) {
        $ruc = $row['ruc'] ?? 'SIN-RUC';

        if (!isset($grouped[$ruc])) {
            $grouped[$ruc] = [
                'id'              => $row['id'],
                'ruc'             => $ruc,
                'name'            => $row['name'] ?? '',
                'commercial_name' => $row['commercial_name'] ?? '',
                'gender'          => $row['gender'] ?? '',
                'products'        => [],
                'phone'           => $row['phone'] ?? '',
                'email'           => $row['email'] ?? '',
                'address'         => $row['address'] ?? '',
                'bank_name'       => $row['bank_name'] ?? '',
                'created_at'      => $row['created_at'] ?? '',
            ];
        }

        $product = trim($row['product'] ?? '');
        if ($product !== '' && !in_array($product, $grouped[$ruc]['products'])) {
            $grouped[$ruc]['products'][] = $product;
        }

        foreach (['name', 'commercial_name', 'gender', 'phone', 'email', 'address', 'bank_name'] as $campo) {
            if (empty($grouped[$ruc][$campo]) && !empty($row[$campo])) {
                $grouped[$ruc][$campo] = $row[$campo];
            }
        }
    }
    $data = array_values($grouped);

    sendResponse(true, "Listado de suplidores obtenido correctamente.", $data);
} catch (Exception $e) {
    sendResponse(false, "Error al obtener suplidores: " . $e->getMessage());
}
