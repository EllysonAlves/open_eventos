<?php
require_once '../../conexao/Conexao.php';

$idLista = $_GET['id_lista'] ?? null;

if (!$idLista) {
    die('Lista inválida.');
}

try {
    $conn = Conexao::getConexao();

    // Busque informações da lista e do evento relacionado
    $stmt = $conn->prepare("
        SELECT l.id_lista, l.nome_lista, e.nome_evento, e.foto_evento, e.local_evento, e.descricao
        FROM listas l
        INNER JOIN eventos e ON l.id_evento = e.id_evento
        WHERE l.id_lista = ? AND l.publica = 1
    ");
    $stmt->execute([$idLista]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dados) {
        die('Lista não encontrada ou privada.');
    }

    // Adicionar cliente à lista
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nomeCliente = $_POST['nome_cliente'] ?? null;

        if ($nomeCliente) {
            $stmtAdd = $conn->prepare("INSERT INTO clientes_listas (id_lista, nome_cliente) VALUES (?, ?)");
            $stmtAdd->execute([$idLista, $nomeCliente]);
            $sucesso = "Você foi adicionado à lista com sucesso!";
        } else {
            $erro = "O nome é obrigatório!";
        }
    }
} catch (Exception $e) {
    die('Erro ao carregar os dados: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar à Lista</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; display: flex; flex-direction: column; align-items: center; }
        .evento-info { margin-bottom: 20px; display: flex; flex-direction: column; align-items: center; }
        .evento-info img { max-width: 100%; height: auto; border-radius: 10px; }
        .form-adicionar { margin-top: 20px; }
        .form-adicionar input, .form-adicionar button {
            display: block; margin: 10px 0; padding: 10px; width: 100%; max-width: 300px;
        }
        .sucesso { color: green; }
        .erro { color: red; }
        .descricao{
            width: 50%;
        }
        .banner{
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }
        .banner::before{
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('<?= htmlspecialchars($dados['foto_evento']); ?>');
            background-size: cover;
            background-position: center;
            filter: blur(10px);
            z-index: 1; /* Coloca o fundo abaixo do conteúdo */
            
        }
        .banner img{
            position: relative;
            z-index: 2; /* Garante que a imagem fique acima do fundo */
            width: 150px; /* Ajuste o tamanho conforme necessário */
            height: auto;
            margin: auto;
        }

    </style>
</head>
<body>
    <div class="banner">
        <?php if ($dados['foto_evento']): ?>
            <img src="<?= htmlspecialchars($dados['foto_evento']); ?>" alt="Foto do Evento">
        <?php endif; ?>
    </div>
    <h1>Adicionar à Lista: <?= htmlspecialchars($dados['nome_lista']); ?></h1>

    <div class="evento-info">
        <h2>Evento: <?= htmlspecialchars($dados['nome_evento']); ?></h2>
        <p><strong>Local:</strong> <?= htmlspecialchars($dados['local_evento']); ?></p>
        <p class="descricao"><strong>Sobre:</strong><br><?php echo $dados['descricao'] ?></p>
    </div>

    <?php if (!empty($sucesso)): ?>
        <p class="sucesso"><?= htmlspecialchars($sucesso); ?></p>
    <?php elseif (!empty($erro)): ?>
        <p class="erro"><?= htmlspecialchars($erro); ?></p>
    <?php endif; ?>

    <form class="form-adicionar" method="POST">
        <label for="nome_cliente">Seu Nome:</label>
        <input type="text" id="nome_cliente" name="nome_cliente" required placeholder="Digite seu nome">
        <button type="submit">Adicionar à Lista</button>
    </form>
</body>
</html>
