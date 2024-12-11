<?php
require_once '../../conexao/Conexao.php';

session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

$receptionists = [];

try {
    $conn = Conexao::getConexao();
    $stmt = $conn->prepare("SELECT id, nome, email, status FROM usuario WHERE role = 'recepcionista'");
    $stmt->execute();
    $receptionists = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<p>Erro ao buscar recepcionistas.</p>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../components/sidebar/style.css?v=3">
    <link rel="stylesheet" href="style.css?v=4">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>RECEPCIONISTAS</title>
</head>
<body>
    <?php
    require_once '../../components/sidebar/sideBar.php';
    ?>
    <main class="main">
        <div>
            <h1>RECEPCIONISTAS</h1>
            <p><a href="../eventos/">HOME </a>/ RECEPCIONISTAS</p>
        </div>

        <div class="topo">
            <button id="addReceptionist">Adicionar Recepcionista</button>
            <div class="pesquisa">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input name="pesquisaReceptionist" id="pesquisaReceptionist" type="text" placeholder="Pesquisar por recepcionistas">
            </div>
        </div>
        <div class="meio">
            <div class="listaAdmin <?= empty($receptionists) ? 'empty' : ''; ?>" id="listaReceptionist">
                <?php
                if (empty($receptionists)) {
                    echo '<img src="assets/group.png" alt="Sem recepcionistas" style="width: 150px; height: 150px;">
                    <h2>Não há recepcionistas cadastrados no momento.</h2>
                    <p class="mensagem-vazia" id="mensagemReceptionist" style="color: gray; font-style: italic; text-align: center;" aria-live="polite">
                        Adicione os recepcionistas para ajudá-lo na organização dos eventos.
                    </p>';
                } else {
                    foreach ($receptionists as $receptionist) {
                        $borderColor = $receptionist['status'] == 1 ? 'green' : 'red';
                        ?>
                        <div class="cardAdmin" style="border-left: 3px solid <?= $borderColor; ?>;">
                            <h2><?= htmlspecialchars($receptionist['nome']); ?></h2>
                            <p><?= htmlspecialchars($receptionist['email']); ?></p>
                            <p>Status: <?= $receptionist['status'] == 1 ? 'Ativo' : 'Inativo'; ?></p>
                            <div class="actions">
                                <button class="btn-status" data-id="<?= $receptionist['id']; ?>" data-status="<?= $receptionist['status']; ?>" title="Alterar Status">
                                    <i class="fa-solid fa-repeat"></i>
                                </button>
                                <button class="btn-delete" data-id="<?= $receptionist['id']; ?>" title="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <div class="info">
                <h2>Recepcionistas</h2>
                <p>Os recepcionistas são responsáveis por organizar os eventos e gerenciar a entrada dos convidados.</p>
                <p>Eles possuem permissões limitadas, podendo gerenciar apenas os eventos aos quais estão associados.</p>
            </div>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../components/sidebar/main.js?v=2"></script>
</body>
</html>
<script>
    const addReceptionist = document.getElementById('addReceptionist').addEventListener('click', () => { 
        window.location.href = 'addReceptionist.php'; 
    });

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', () => {
            const userId = button.getAttribute('data-id');
            Swal.fire({
                title: 'Tem certeza?',
                text: "Você não poderá desfazer esta ação!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('deleteReceptionist.php', { id: userId }, function(response) {
                        if (response.success) {
                            Swal.fire('Excluído!', 'O recepcionista foi excluído.', 'success');
                            location.reload();
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        });
    });

    document.querySelectorAll('.btn-status').forEach(button => {
        button.addEventListener('click', () => {
            const userId = button.getAttribute('data-id');
            const currentStatus = button.getAttribute('data-status');
            const newStatus = currentStatus == 1 ? 2 : 1;

            $.post('updateStatus.php', { id: userId, status: newStatus }, function(response) {
                if (response.success) {
                    Swal.fire('Atualizado!', 'O status foi alterado.', 'success');
                    location.reload();
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            }, 'json');
        });
    });

    $(document).ready(function () {
        $('#pesquisaReceptionist').on('input', function () {
            const query = $(this).val();
            $.ajax({
                url: 'searchReceptionist.php',
                method: 'POST',
                data: { query },
                success: function (data) {
                    $('#listaReceptionist').html(data);
                },
                error: function () {
                    Swal.fire('Erro', 'Não foi possível carregar os resultados.', 'error');
                }
            });
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
