<?php
require_once '../../conexao/Conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

$id_evento = $_GET['id_evento'] ?? null;

if (!$id_evento) {
    echo "<p>ID do evento não fornecido.</p>";
    exit();
}

try {
    $conn = Conexao::getConexao();
    $stmt = $conn->prepare("SELECT * FROM eventos WHERE id_evento = :id");
    $stmt->bindParam(':id', $id_evento, PDO::PARAM_INT);
    $stmt->execute();
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        echo "<p>Evento não encontrado.</p>";
        exit();
    }
} catch (Exception $e) {
    echo "<p>Erro ao buscar o evento.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../components/sidebar/style.css?v=8">
    <link rel="stylesheet" href="style.css?v=10">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tiny.cloud/1/m73hzmy60uak9ekfkfsavr5hyhsvcds7sfu1yhrhir00ggsk/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <title>Editar Evento</title>
    <style>
    /* Estilização geral do formulário */
    .form-container {
        background: #ffffff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 40px auto;
    }

    .form-container h1 {
        font-family: 'Montserrat', sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: #333333;
        margin-bottom: 20px;
        text-align: center;
    }

    .form-container label {
        font-family: 'Roboto', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: #333333;
        margin-bottom: 5px;
        display: block;
    }

    .form-container input[type="text"],
    .form-container input[type="datetime-local"],
    .form-container textarea,
    .form-container select {
        width: 100%;
        padding: 10px;
        border: 1px solid #cccccc;
        border-radius: 4px;
        font-size: 14px;
        font-family: 'Roboto', sans-serif;
        margin-bottom: 15px;
        transition: border-color 0.3s;
    }

    .form-container input:focus,
    .form-container textarea:focus,
    .form-container select:focus {
        border-color: #0bb5d5;
        outline: none;
    }

    .form-container .btn-submit {
        display: block;
        width: 100%;
        padding: 12px;
        background: #0bb5d5;
        color: #ffffff;
        font-family: 'Roboto', sans-serif;
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .form-container .btn-submit:hover {
        background: #088fb2;
    }

    /* Botão voltar */
    .voltarBtn {
        display: block;
        padding: 12px;
        width: 200px;
        margin: 20px auto 0;
        text-align: center;
        font-family: 'Roboto', sans-serif;
        font-size: 14px;
        font-weight: 500;
        text-transform: uppercase;
        color: #333333;
        border: 2px solid #0bb5d5;
        border-radius: 4px;
        background: none;
        cursor: pointer;
        transition: all 0.3s;
    }

    .voltarBtn:hover {
        background: #0bb5d5;
        color: #ffffff;
    }

    /* Estilização do título e breadcrumbs */
    .main > div {
        margin-bottom: 20px;
        text-align: center;
    }

    .main h1 {
        font-family: 'Montserrat', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: #333333;
    }

    .main p a {
        font-family: 'Roboto', sans-serif;
        font-size: 14px;
        font-weight: 400;
        color: #0bb5d5;
        text-decoration: none;
    }

    .main p a:hover {
        text-decoration: underline;
    }
</style>

<script>
      tinymce.init({
        selector: '#descricao',
        plugins: 'lists link image table code',
        toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
        height: 300
      });
</script>

</head>
<body>
    <?php
    require_once '../../components/sidebar/sideBar.php';
    ?>
    <main class="main">
        <div>
            <h1>EDITAR EVENTO</h1>
            <p><a href="../eventos/">HOME </a>/ EVENTOS / EDITAR</p>
        </div>
        <div class="form-container">
            <form id="editEventoForm" action="updateEvento.php" method="POST">
                <input type="hidden" name="id_evento" value="<?= htmlspecialchars($evento['id_evento']); ?>">

                <label for="nome_evento">Nome do Evento:</label>
                <input type="text" id="nome_evento" name="nome_evento" value="<?= htmlspecialchars($evento['nome_evento']); ?>" required>

                <label for="data_hora_inicio">Data e Hora de Início:</label>
                <input type="datetime-local" id="data_hora_inicio" name="data_hora_inicio" value="<?= date('Y-m-d\TH:i', strtotime($evento['data_hora_inicio'])); ?>" required>

                <label for="data_hora_termino">Data e Hora de Término:</label>
                <input type="datetime-local" id="data_hora_termino" name="data_hora_termino" value="<?= date('Y-m-d\TH:i', strtotime($evento['data_hora_termino'])); ?>" required>

                <label for="local_evento">Local do Evento:</label>
                <input type="text" id="local_evento" name="local_evento" value="<?= htmlspecialchars($evento['local_evento']); ?>" required>

                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="4" required><?php echo $evento['descricao'] ?></textarea>

                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="1" <?= $evento['status'] == 1 ? 'selected' : ''; ?>>Aberto</option>
                    <option value="0" <?= $evento['status'] == 0 ? 'selected' : ''; ?>>Finalizado</option>
                </select>

                <button type="submit" class="btn-submit">Salvar Alterações</button>
            </form>

        </div>
        <button id="voltarBtn" class="voltarBtn">VOLTAR</button>
    </main>

    <script src="../../components/sidebar/main.js?v=2"></script>
    <script>
        document.getElementById('editEventoForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Impede o envio tradicional do formulário
            
            const formData = new FormData(this); // Coleta os dados do formulário
            
            // Envia os dados via AJAX
            fetch('updateEvento.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Espera que a resposta seja em formato JSON
            .then(data => {
                if (data.success) {
                    // Exibe a mensagem de sucesso utilizando SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Evento atualizado com sucesso!',
                    }).then(() => {
                        window.location.href = '../eventos/'; // Redireciona após sucesso
                    });
                } else {
                    // Exibe a mensagem de erro utilizando SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Erro ao atualizar o evento: ' + data.error,
                    });
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro na requisição, tente novamente.',
                });
            });
        });

        const voltarBtn = document.getElementById('voltarBtn').addEventListener('click', () =>{ window.location.href = '/open_eventos/pages/eventos/';})
    </script>
</body>
</html>
