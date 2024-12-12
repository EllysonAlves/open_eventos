<?php
require_once '../../conexao/Conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

// Verifica se o ID do evento foi fornecido
if (!isset($_GET['id'])) {
    echo "<p>ID do evento não especificado.</p>";
    exit();
}

$idEvento = $_GET['id'];

// Busca os detalhes do evento
try {
    $conn = Conexao::getConexao();
    $stmt = $conn->prepare("SELECT * FROM eventos WHERE id_evento = ?");
    $stmt->execute([$idEvento]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        echo "<p>Evento não encontrado.</p>";
        exit();
    }
} catch (Exception $e) {
    echo "<p>Erro ao carregar os dados do evento.</p>";
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Gerenciar Evento</title>
</head>
<body>
    <?php require_once '../../components/sidebar/sideBar.php'; ?>

    <main class="main">
        <div>
            <h1>Gerenciar Evento</h1>
            <p><a href="index.php">EVENTOS</a> / <?= htmlspecialchars($evento['nome_evento']); ?></p>
        </div>

        <div class="evento-detalhes">
            <h2><?= htmlspecialchars($evento['nome_evento']); ?></h2>
            <p><strong>Data de Início:</strong> <?= htmlspecialchars($evento['data_hora_inicio']); ?></p>
            <p><strong>Data de Término:</strong> <?= htmlspecialchars($evento['data_hora_termino']); ?></p>
            <p><strong>Local:</strong> <?= htmlspecialchars($evento['local_evento']); ?></p>
            <p><strong>Descrição:</strong> <?= htmlspecialchars($evento['descricao']); ?></p>
        </div>

        <div class="listas-gerenciar">
            <h3>Listas</h3>
            <button id="addLista">Adicionar Nova Lista</button>
            <ul>
                <li><a href="listaClientes.php?tipo=vip&id_evento=<?= $idEvento; ?>">Clientes VIP</a></li>
                <li><a href="listaClientes.php?tipo=isento&id_evento=<?= $idEvento; ?>">Clientes Isentos</a></li>
            </ul>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../components/sidebar/main.js"></script>
</body>
</html>
