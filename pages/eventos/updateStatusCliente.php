<?php
require_once '../../conexao/Conexao.php';

header('Content-Type: application/json');

$idCliente = $_POST['id_cliente'] ?? null;
$newStatus = $_POST['status'] ?? null;

if (!$idCliente || !$newStatus) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    exit();
}

try {
    $conn = Conexao::getConexao();

    // Atualizar o status do cliente
    $stmt = $conn->prepare("UPDATE clientes_listas SET status_confirmacao = ? WHERE id_cliente = ?");
    $stmt->execute([$newStatus, $idCliente]);

    echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o status: ' . $e->getMessage()]);
}
