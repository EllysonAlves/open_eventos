<?php
require_once '../../conexao/Conexao.php';
session_start();

// Verificar se a sessão está ativa
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado']);
    exit();
}

// Verificar se os dados do formulário foram enviados
$id_evento = $_POST['id_evento'] ?? null;
$nome_evento = $_POST['nome_evento'] ?? null;
$data_hora_inicio = $_POST['data_hora_inicio'] ?? null;
$data_hora_termino = $_POST['data_hora_termino'] ?? null;
$local_evento = $_POST['local_evento'] ?? null;
$descricao = $_POST['descricao'] ?? null;
$status = $_POST['status'] ?? null;

if (!$id_evento || !$nome_evento || !$data_hora_inicio || !$data_hora_termino || !$local_evento || !$descricao || !isset($status)) {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
    exit();
}

try {
    // Conectar ao banco de dados
    $conn = Conexao::getConexao();

    // Preparar a query de atualização
    $stmt = $conn->prepare("UPDATE eventos SET nome_evento = :nome_evento, data_hora_inicio = :data_hora_inicio, 
                            data_hora_termino = :data_hora_termino, local_evento = :local_evento, descricao = :descricao, 
                            status = :status WHERE id_evento = :id_evento");

    // Vincular os parâmetros
    $stmt->bindParam(':nome_evento', $nome_evento);
    $stmt->bindParam(':data_hora_inicio', $data_hora_inicio);
    $stmt->bindParam(':data_hora_termino', $data_hora_termino);
    $stmt->bindParam(':local_evento', $local_evento);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':id_evento', $id_evento, PDO::PARAM_INT);

    // Executar a query
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar o evento']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Erro ao processar a solicitação: ' . $e->getMessage()]);
}
?>
