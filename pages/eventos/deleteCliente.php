<?php
require_once '../../conexao/Conexao.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCliente = $_POST['id_cliente'] ?? null;

    if (!isset($_SESSION['role'])) {
        echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
        exit();
    }

    $role = $_SESSION['role'];

    // Permitir apenas roles específicas
    $rolesPermitidas = ['promoter', 'admin', 'master'];
    if (!in_array($role, $rolesPermitidas)) {
        echo json_encode(['success' => false, 'message' => 'Permissão negada.']);
        exit();
    }

    if (!$idCliente) {
        echo json_encode(['success' => false, 'message' => 'Cliente inválido.']);
        exit();
    }

    try {
        $conn = Conexao::getConexao();
        $stmt = $conn->prepare("DELETE FROM clientes_listas WHERE id_cliente = ?");
        $stmt->execute([$idCliente]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao remover cliente.']);
    }
}
