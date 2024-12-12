<?php
require_once '../../conexao/Conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

$idEvento = $_GET['id_evento'] ?? null;

if (!$idEvento) {
    echo "Evento não especificado.";
    exit();
}

$conn = Conexao::getConexao();

// Busca todos os usuários que podem ser promoters
$stmt = $conn->prepare("SELECT id, nome, email FROM usuario WHERE role = 'promoter'");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Busca promoters já vinculados ao evento
$stmtPromoters = $conn->prepare("SELECT usuario.id, usuario.nome FROM eventos_promoters 
    INNER JOIN usuario ON usuario.id = evento_promoter.id_promoter
    WHERE evento_promoter.id_evento = ?");
$stmtPromoters->execute([$idEvento]);
$promoters = $stmtPromoters->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Gerenciar Promoters</title>
</head>
<body>
    <h1>Gerenciar Promoters para o Evento</h1>
    <h2>Promoters já adicionados:</h2>
    <ul>
        <?php foreach ($promoters as $promoter): ?>
            <li>
                <?= htmlspecialchars($promoter['nome']); ?>
                <button class="btn-remove" data-id="<?= $promoter['id']; ?>">Remover</button>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Adicionar Promoters:</h2>
    <form id="addPromoterForm">
        <select name="id_promoter" id="id_promoter">
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario['id']; ?>"><?= htmlspecialchars($usuario['nome']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="button" id="addPromoterBtn">Adicionar</button>
    </form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $('#addPromoterBtn').click(() => {
            const idPromoter = $('#id_promoter').val();
            $.post('addPromoter.php', { id_evento: <?= $idEvento ?>, id_promoter: idPromoter }, (response) => {
                alert(response.message);
                location.reload();
            }, 'json');
        });

        $('.btn-remove').click(function () {
            const idPromoter = $(this).data('id');
            $.post('removePromoter.php', { id_evento: <?= $idEvento ?>, id_promoter: idPromoter }, (response) => {
                alert(response.message);
                location.reload();
            }, 'json');
        });
    </script>
</body>
</html>
