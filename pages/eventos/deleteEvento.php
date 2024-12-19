<?php 

require_once '../../conexao/Conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $conexao = Conexao::getConexao();

        // Iniciar transação para garantir que as exclusões ocorram corretamente
        $conexao->beginTransaction();

        // Excluir os registros dependentes na tabela clientes_listas antes de excluir a lista
        $stmt_clientes_listas = $conexao->prepare("DELETE FROM clientes_listas WHERE id_lista IN (SELECT id_lista FROM listas WHERE id_evento = ?)");
        $stmt_clientes_listas->execute([$_POST['id']]);

        $stmt_promoters_evento = $conexao->prepare("DELETE FROM promoters_listas WHERE id_lista IN (SELECT id_lista FROM listas WHERE id_lista = ?)");
        $stmt_promoters_evento->execute([$_POST['id']]);

        // Excluir registros na tabela listas
        $stmt_lista = $conexao->prepare("DELETE FROM listas WHERE id_evento = ?");
        $stmt_lista->execute([$_POST['id']]);

        // Excluir o evento
        $stmt_evento = $conexao->prepare("DELETE FROM eventos WHERE id_evento = ?");
        $stmt_evento->execute([$_POST['id']]);

        // Confirmar a transação
        $conexao->commit();

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        // Em caso de erro, realizar rollback
        $conexao->rollBack();
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir o evento.']);
    }
}
?>
