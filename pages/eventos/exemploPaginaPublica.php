<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar à Lista</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .evento-info { margin-bottom: 20px; }
        .evento-info img { max-width: 100%; height: auto; border-radius: 10px; }
        .form-adicionar { margin-top: 20px; }
        .form-adicionar input, .form-adicionar button {
            display: block; margin: 10px 0; padding: 10px; width: 100%; max-width: 300px;
        }
        .sucesso { color: green; }
        .erro { color: red; }
    </style>
</head>
<body>
    <h1>Adicionar à Lista: PUBLICA</h1>

    <div class="evento-info">
        <h2>Evento: EVENTO TESTE</h2>
        <img src="assets/images.jpeg" alt="Foto do Evento">
        <p><strong>Local: OPEN BEACH</p>
    </div>

    <form class="form-adicionar" method="POST">
        <label for="nome_cliente">Seu Nome:</label>
        <input type="text" id="nome_cliente" name="nome_cliente" required placeholder="Digite seu nome">
        <button type="submit">Adicionar à Lista</button>
    </form>
</body>
</html>
