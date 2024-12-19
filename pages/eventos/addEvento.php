<?php
require_once '../../conexao/Conexao.php';

session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeEvento = $_POST['nome_evento'] ?? '';
    $dataHoraInicio = $_POST['data_hora_inicio'] ?? '';
    $dataHoraTermino = $_POST['data_hora_termino'] ?? '';
    $localEvento = $_POST['local_evento'] ?? '';
    $descricao = $_POST['descricao'] ?? '';

    // Diretório para salvar as imagens
    $uploadDir = '../../uploads/eventos/';
    $fotoEvento = null;

    // Validação simples
    if (!empty($nomeEvento) && !empty($dataHoraInicio) && !empty($dataHoraTermino) && !empty($localEvento)) {
        try {
            // Verificar e processar o upload do arquivo
            if (isset($_FILES['foto_evento']) && $_FILES['foto_evento']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['foto_evento']['tmp_name'];
                $fileName = uniqid() . '_' . basename($_FILES['foto_evento']['name']);
                $filePath = $uploadDir . $fileName;

                // Certifique-se de que o diretório existe
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Mover o arquivo para o diretório de destino
                if (move_uploaded_file($tmpName, $filePath)) {
                    $fotoEvento = $filePath;
                } else {
                    throw new Exception('Erro ao salvar a foto do evento.');
                }
            }

            $conexao = Conexao::getConexao();
            $idUsuario = $_SESSION['id']; // Recupera o ID do usuário logado

            // Inserir o evento no banco
            $stmt = $conexao->prepare("
                INSERT INTO eventos (nome_evento, data_hora_inicio, data_hora_termino, local_evento, descricao, foto_evento, criado_por, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nomeEvento, $dataHoraInicio, $dataHoraTermino, $localEvento, $descricao, $fotoEvento, $idUsuario, 1]);

            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Evento Adicionado!',
                        text: 'O evento foi cadastrado com sucesso.',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
        } catch (Exception $e) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao Adicionar',
                        text: 'Ocorreu um erro ao tentar cadastrar o evento. Detalhes: " . $e->getMessage() . "',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
        }
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos Incompletos',
                    text: 'Por favor, preencha todos os campos obrigatórios.',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../components/sidebar/style.css?v=3">
    <link rel="stylesheet" href="style.css?v=3">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/m73hzmy60uak9ekfkfsavr5hyhsvcds7sfu1yhrhir00ggsk/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <title>Adicionar Evento</title>
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
    <?php require_once '../../components/sidebar/sideBar.php'; ?>
    <main class="main">
        <div>
            <h1>ADICIONAR EVENTOS</h1>
            <p><a href="../eventos/">HOME </a>/ ADICIONAR EVENTOS</p>
        </div>

        <div class="meio">
            <div class="listaAdmin">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-row">
                    <div>
                        <label for="nome_evento">Nome do Evento</label>
                        <input name="nome_evento" id="nome_evento" type="text" placeholder="Nome do evento" required>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="data_hora_inicio">Data e Hora de Início</label>
                        <input name="data_hora_inicio" id="data_hora_inicio" type="datetime-local" required>
                    </div>
                    <div>
                        <label for="data_hora_termino">Data e Hora de Término</label>
                        <input name="data_hora_termino" id="data_hora_termino" type="datetime-local" required>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="local_evento">Local</label>
                        <input name="local_evento" id="local_evento" type="text" placeholder="Local do evento" required>
                    </div>
                    
                </div>
                <div class="form-row">
                    <div>
                        <label for="descricao">Descrição</label>
                        <textarea name="descricao" id="descricao" placeholder="Descrição do evento"></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="foto_evento">Foto do Evento</label>
                        <input type="file" name="foto_evento" id="foto_evento" accept="image/*" required>
                    </div>
                </div>
                <button type="submit">Adicionar</button>
            </form>

               
               <button id="voltarBtn" class="voltarBtn">VOLTAR</button>
            </div>
            <div class="info">
                <h2>Eventos</h2>
                <p>Gerencie seus eventos com facilidade.</p>
                <p>Adicione informações importantes como data, local e descrições detalhadas para cada evento.</p>
            </div>
        </div>
    </main>

<script>
    const voltarBtn = document.getElementById('voltarBtn').addEventListener('click', () =>{ window.location.href = '/open_eventos/pages/eventos/';})
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../components/sidebar/main.js?v=4"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


    
</body>
</html>
