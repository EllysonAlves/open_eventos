<?php
require_once '../../conexao/Conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

$idLista = $_POST['id_lista'] ?? null;
$nomeCliente = $_POST['nome_cliente'] ?? null;
$emailCliente = $_POST['email_cliente'] ?? null;
$cpfCliente = $_POST['cpf_cliente'] ?? null;
$role = $_SESSION['role'];
$userId = $_SESSION['id'];

if (!$idLista || !$nomeCliente || !$emailCliente) {
    echo "Dados inválidos.";
    exit();
}

try {
    $conn = Conexao::getConexao();

    // Verifica se a lista é pública ou se o usuário tem permissão para adicionar
    $stmt = $conn->prepare("SELECT publica,id_evento FROM listas WHERE id_lista = ?");
    $stmt->execute([$idLista]);
    $lista = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lista) {
        echo "Lista não encontrada.";
        exit();
    }

    if ($role === 'user' && !$lista['publica']) {
        echo "Você não tem permissão para adicionar clientes a esta lista.";
        exit();
    }

    // Inicia uma transação para garantir que ambos os inserts ocorram juntos
    $conn->beginTransaction();

    // Inserir o cliente na tabela clientes
    $stmt = $conn->prepare("INSERT INTO clientes (nome_cliente, email_cliente, cpf_cliente) VALUES (?, ?, ?)");
    $stmt->execute([$nomeCliente, $emailCliente, $cpfCliente]);

    // Obtém o id_cliente gerado
    $idCliente = $conn->lastInsertId();

    // Agora insere o cliente na tabela clientes_listas
    $stmt = $conn->prepare("INSERT INTO clientes_listas (id_lista, id_cliente, criado_por, nome_cliente) VALUES (?, ?, ?, ?)");
    $stmt->execute([$idLista, $idCliente, $userId, $nomeCliente]);

    // Confirma a transação
    $conn->commit();

    $idEvento = $lista['id_evento'];

    // Redireciona para a página de gerenciamento da lista
    header("location: gerenciarListas.php?id_evento=$idEvento");
    exit();

} catch (Exception $e) {
    // Caso ocorra algum erro, faz rollback e exibe a mensagem
    $conn->rollBack();
    echo "Erro ao adicionar cliente: " . $e->getMessage();
}
?>
