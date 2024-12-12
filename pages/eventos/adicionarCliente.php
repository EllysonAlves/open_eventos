<?php
require_once '../../conexao/Conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

$idLista = $_GET['id_lista'] ?? null;

if (!$idLista) {
    echo "Lista inválida.";
    exit();
}

try {
    $conn = Conexao::getConexao();

    // Buscar informações sobre a lista
    $stmt = $conn->prepare("SELECT nome_lista FROM listas WHERE id_lista = ?");
    $stmt->execute([$idLista]);
    $lista = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lista) {
        echo "Lista não encontrada.";
        exit();
    }
} catch (Exception $e) {
    echo "Erro ao carregar informações da lista.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../components/sidebar/style.css?v=3">
    <link rel="stylesheet" href="style.css?v=4">
    <title>Adicionar Cliente à Lista - <?= htmlspecialchars($lista['nome_lista']); ?></title>
</head>
<body>
    <main class="main">
        <h1>Adicionar Cliente à Lista: <?= htmlspecialchars($lista['nome_lista']); ?></h1>
        
        <!-- Formulário de Adição de Cliente -->
        <form method="POST" action="addClienteLista.php">
            <input type="hidden" name="id_lista" value="<?= $idLista; ?>">

            <label for="nome_cliente">Nome do Cliente:</label>
            <input type="text" name="nome_cliente" id="nome_cliente" placeholder="Digite o nome do cliente" required>

            <label for="email_cliente">Email do Cliente:</label>
            <input type="text" name="email_cliente" id="email_cliente" placeholder="Digite o email do cliente" required>

            <label for="cpf_cliente">CPF do Cliente:</label>
            <input type="text" name="cpf_cliente" id="cpf_cliente" placeholder="Digite o CPF do cliente">

            <button type="submit">Adicionar Cliente</button>
        </form>

        <br>

        <a href="gerenciarListas.php?id_lista=<?= $idLista; ?>">Voltar para Gerenciar Listas</a>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
