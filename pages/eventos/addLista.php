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
$statusEvento = $_POST['status_evento'] ?? null;
$idPromoters = $_POST['id_promoter'] ?? []; // Agora é um array de promoters
$criadoPor = $_SESSION['id'];
$publico = $_POST['publica'] ?? null;


// Verifica se o usuário tem permissão (role)
$permissoesPermitidas = ['admin', 'master', 'promoter'];
if (!in_array($_SESSION['role'] ?? 'guest', $permissoesPermitidas)) {
    $_SESSION['message'] = 'Erro: Você não tem permissão para adicionar listas.';
    header("location: gerenciarListas.php?id_evento=$idEvento&status_evento=$statusEvento");
    exit();
}


if (!$idEvento || !$nomeLista) {
    // Redireciona de volta com uma mensagem de erro
    $_SESSION['message'] = 'Erro: Dados inválidos ao adicionar a lista.';
    header("location: gerenciarListas.php?id_evento=$idEvento&status_evento=$statusEvento");
    exit();
}

try {
    $conn = Conexao::getConexao();

    // Insere a nova lista no banco de dados
    $stmt = $conn->prepare("INSERT INTO listas (nome_lista, id_evento, criado_por,publica) VALUES (?, ?, ?,?)");
    $stmt->execute([$nomeLista, $idEvento, $criadoPor,$publico]);

    $eventoId = $conn->lastInsertId(); // Pega o ID do evento recém inserido

            // Associar múltiplos promoters ao evento
            foreach ($idPromoters as $idPromoter) {
                $stmt = $conn->prepare("INSERT INTO promoters_listas (id_lista, id_promoter) VALUES (?, ?)");
                $stmt->execute([$eventoId, $idPromoter]);
            }

    // Redireciona com uma mensagem de sucesso
    $_SESSION['message'] = 'Lista adicionada com sucesso!';
    header("location: gerenciarListas.php?id_evento=$idEvento&status_evento=$statusEvento");
    exit();
} catch (Exception $e) {
    // Redireciona de volta com uma mensagem de erro
    $_SESSION['message'] = 'Erro ao adicionar lista: ' . $e->getMessage();
    header("location: gerenciarListas.php?id_evento=$idEvento&status_evento=$statusEvento");
    exit();
}
?>
