<?php
require_once '../../conexao/Conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

$idLista = $_POST['id_lista'] ?? null;
if (!$idLista) {
    echo "Erro: Lista não fornecida.";
    exit();
}

try {
    $conn = Conexao::getConexao();

    // Iniciar uma transação para garantir a consistência dos dados
    $conn->beginTransaction();

    // Excluir os registros relacionados na tabela clientes_listas
    $stmtClientesListas = $conn->prepare("DELETE FROM clientes_listas WHERE id_lista = ?");
    $stmtClientesListas->execute([$idLista]);

    // Excluir a lista da tabela listas
    $stmtLista = $conn->prepare("DELETE FROM listas WHERE id_lista = ?");
    $stmtLista->execute([$idLista]);

    // Commit da transação
    $conn->commit();

    echo json_encode(["success" => true, "message" => "Lista excluída com sucesso!"]);
} catch (Exception $e) {
    // Rollback da transação em caso de erro
    $conn->rollBack();
    echo json_encode(["success" => false, "message" => "Erro ao excluir lista: " . $e->getMessage()]);
}
?>
