<?php
require_once '../../conexao/Conexao.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_evento']) && isset($_POST['status'])) {
    try {
        $idEvento = intval($_POST['id_evento']);
        $novoStatus = intval($_POST['status']) === 1 ? 0 : 1; // Alterna o status

        // Verifica permissões (apenas admin/master podem modificar)
        if (!in_array($_SESSION['role'] ?? 'guest', ['admin', 'master'])) {
            echo json_encode(['success' => false, 'message' => 'Permissão negada.']);
            exit;
        }

        $conn = Conexao::getConexao();
        $stmt = $conn->prepare("UPDATE eventos SET status = ? WHERE id_evento = ?");
        $stmt->execute([$novoStatus, $idEvento]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'new_status' => $novoStatus]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhuma alteração feita.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status.']);
    }
}
