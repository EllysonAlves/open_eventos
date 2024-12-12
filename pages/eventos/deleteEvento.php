<?php
require_once '../../conexao/Conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare("DELETE FROM eventos WHERE id_evento = ?");
        $stmt->execute([$_POST['id']]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir o evento.']);
    }
}
