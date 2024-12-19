<?php
require_once '../../conexao/Conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

// Verifica se o usuário tem permissão (role)
$permissoesPermitidas = ['admin', 'master', 'promoter'];
if (!in_array($_SESSION['role'] ?? 'guest', $permissoesPermitidas)) {
    echo json_encode(["success" => false, "message" => "Erro: Você não tem permissão para excluir listas."]);
    exit();
}

// Obtém o ID da lista
$idLista = $_POST['id_lista'] ?? null;

if (!$idLista) {
    echo json_encode(["success" => false, "message" => "Erro: Lista não fornecida."]);
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
