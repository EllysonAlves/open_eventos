<?php
require_once '../../conexao/Conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

$idLista = $_GET['id_lista'] ?? null;
$idEvento = $_GET['id_evento'] ?? null;
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

    $stmtEvento = $conn->prepare("SELECT * FROM eventos WHERE id_evento = ? ");
    $stmtEvento->execute([$idEvento]);
    $evento = $stmtEvento->fetch(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <title>Adicionar Cliente à Lista - <?= htmlspecialchars($lista['nome_lista']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
        }
        .main {
            flex: 1;
            padding: 20px;
            background-color: #f4f4f9;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 24px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
        }
        input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 15px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php require_once '../../components/sidebar/sideBar.php'; ?>

    <!-- Main Content -->
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

        <a href="gerenciarListas.php?id_lista=<?= $idLista; ?>&id_evento=<?= $idEvento; ?>&status_evento=<?= $evento['status'] ?>" class="back-link">&larr; Voltar para Gerenciar Listas</a>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
