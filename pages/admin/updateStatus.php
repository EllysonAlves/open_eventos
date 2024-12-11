<?php
require_once '../../conexao/Conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    try {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare("UPDATE usuario SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['id']]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao alterar o status do administrador.']);
    }
}
