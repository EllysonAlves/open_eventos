<?php
require_once '../../conexao/Conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

// Obtém os dados do formulário
$idEvento = $_POST['id_evento'] ?? null;
$nomeLista = $_POST['nome_lista'] ?? null;
$criadoPor = $_SESSION['id'];

if (!$idEvento || !$nomeLista) {
    // Redireciona de volta com uma mensagem de erro
    $_SESSION['message'] = 'Erro: Dados inválidos ao adicionar a lista.';
    header("location: gerenciarListas.php?id_evento=$idEvento");
    exit();
}

try {
    $conn = Conexao::getConexao();

    // Insere a nova lista no banco de dados
    $stmt = $conn->prepare("INSERT INTO listas (nome_lista, id_evento, criado_por) VALUES (?, ?, ?)");
    $stmt->execute([$nomeLista, $idEvento, $criadoPor]);

    // Redireciona com uma mensagem de sucesso
    $_SESSION['message'] = 'Lista adicionada com sucesso!';
    header("location: gerenciarListas.php?id_evento=$idEvento");
    exit();
} catch (Exception $e) {
    // Redireciona de volta com uma mensagem de erro
    $_SESSION['message'] = 'Erro ao adicionar lista: ' . $e->getMessage();
    header("location: gerenciarListas.php?id_evento=$idEvento");
    exit();
}
