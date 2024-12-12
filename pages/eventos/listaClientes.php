<?php
require_once '../../conexao/Conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

// Verifica se os parâmetros estão definidos
if (!isset($_GET['tipo']) || !isset($_GET['id_evento'])) {
    echo "<p>Parâmetros inválidos.</p>";
    exit();
}

$tipo = $_GET['tipo'];
$idEvento = $_GET['id_evento'];

try {
    $conn = Conexao::getConexao();
    $stmt = $conn->prepare("SELECT * FROM listas WHERE tipo = ? AND id_evento = ?");
    $stmt->execute([$tipo, $idEvento]);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<p>Erro ao carregar os dados da lista.</p>";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de <?= htmlspecialchars(ucfirst($tipo)); ?></title>
</head>
<body>
    <h1>Lista de <?= htmlspecialchars(ucfirst($tipo)); ?></h1>
    <a href="gerenciarEvento.php?id=<?= $idEvento; ?>">Voltar</a>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= htmlspecialchars($cliente['nome']); ?></td>
                    <td><?= htmlspecialchars($cliente['email']); ?></td>
                    <td>
                        <button>Remover</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
