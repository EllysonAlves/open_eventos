<?php
require_once '../../conexao/Conexao.php';

session_start();


if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

$eventos = [];

try {
    $conn = Conexao::getConexao();
    $stmt = $conn->prepare("SELECT id_evento, nome_evento, data_hora_inicio, data_hora_termino, local_evento, descricao, status FROM eventos");
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<p>Erro ao buscar eventos.</p>";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Eventos</title>
</head>
<body>
    <?php
    require_once '../../components/sidebar/sideBar.php';
    ?>
    <main class="main">
        <div>
            <h1>EVENTOS</h1>
            <p><a href="../eventos/">HOME </a>/ EVENTOS</p>
        </div>

        <div class="topo">
        <?php if (!in_array($_SESSION['role'] ?? 'guest', ['recepcionista','promoter','user'])): ?>
            <button id="addEvento">Adicionar Evento</button>
        <?php endif; ?>
            <div class="pesquisa">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input name="pesquisaEvento" id="pesquisaEvento" type="text" placeholder="Pesquisar por eventos">
            </div>
        </div>
        <div class="meio">
            <div class="listaEventos <?= empty($eventos) ? 'empty' : ''; ?>" id="listaEventos">
                <?php
                if (empty($eventos)) {
                    echo '<img src="assets/evento.png" alt="Sem eventos" style="width: 150px; height: 150px;">
                    <h2>Não há eventos cadastrados no momento.</h2>
                    <p class="mensagem-vazia" id="mensagemEvento" style="color: gray; font-style: italic; text-align: center;" aria-live="polite">
                        Adicione os eventos que deseja gerenciar.
                    </p>';
                } else {
                    
                    ?>
                    <?php foreach ($eventos as $evento): ?>
                    <?php 
                    $buttonColor = $evento['status'] == 1 ? 'green' : 'gray';
                    $buttonText = $evento['status'] == 1 ? 'Aberto' : 'Finalizado';

                    // Verifica se o usuário tem permissão para manipular eventos finalizados
                    $usuarioPodeEditar = in_array($_SESSION['role'] ?? 'guest', ['admin', 'master']);
                    $desabilitarListas = $evento['status'] == 0 && !$usuarioPodeEditar; // Se evento finalizado e não é admin/master
                    ?>
                    <div class="cardEvento">
                        <h2><?= htmlspecialchars($evento['nome_evento']); ?></h2>
                        <p>Início: <?= htmlspecialchars($evento['data_hora_inicio']); ?></p>
                        <p>Término: <?= htmlspecialchars($evento['data_hora_termino']); ?></p>
                        <p>Local: <?= htmlspecialchars($evento['local_evento']); ?></p>
                        <p><?php echo $evento['descricao'] ?></p>
                        <div class="actions">
                            <?php if (!in_array($_SESSION['role'] ?? 'guest', ['recepcionista','user','promoter'])): ?>
                                <button class="btn-edit" data-id="<?= $evento['id_evento']; ?>" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button class="btn-delete" data-id="<?= $evento['id_evento']; ?>" title="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            <?php endif; ?>
                            <button class="btn-status" data-id="<?= $evento['id_evento']; ?>" data-status="<?= $evento['status']; ?>" style="background-color: <?= $buttonColor; ?>; color: white;">
                                <?= $buttonText; ?>
                            </button>
                            <a href="gerenciarListas.php?id_evento=<?= $evento['id_evento']; ?>&status_evento=<?= $evento['status'] ?>" 
                            class="btn-lists <?= $desabilitarListas ? 'disabled' : ''; ?>" 
                            title="<?= $desabilitarListas ? 'Somente administradores podem manipular eventos finalizados.' : 'Gerenciar Listas'; ?>" 
                            <?= $desabilitarListas ? 'tabindex="-1" aria-disabled="true" onclick="return false;"' : ''; ?>>
                                <i class="fa-solid fa-users"></i> Listas
                            </a>
                        </div>
                    </div>
                <?php endforeach; } ?>                
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../components/sidebar/main.js?v=2"></script>
</body>
</html>
<script>
    const addEvento = document.getElementById('addEvento');

    if (addEvento) { // Verifica se o elemento existe
        addEvento.addEventListener('click', () => {
            window.location.href = 'addEvento.php';
        });
    }

    


    document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        const target = e.target.closest('button'); // Captura o botão mais próximo

        if (!target) return; // Sai se não for um botão

        // Evento de DELETE
        if (target.classList.contains('btn-delete')) {
            const idEventoDelete = target.getAttribute('data-id');

            Swal.fire({
                title: 'Tem certeza?',
                text: 'Você não poderá desfazer esta ação!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(
                        'deleteEvento.php',
                        { id: idEventoDelete },
                        function (response) {
                            if (response.success) {
                                Swal.fire('Excluído!', 'O evento foi excluído.', 'success');
                                location.reload();
                            } else {
                                Swal.fire('Erro!', response.message, 'error');
                            }
                        },
                        'json'
                    );
                }
            });
        }

        // Evento de EDIT
        else if (target.classList.contains('btn-edit')) {
            const idEventoEdit = target.getAttribute('data-id');
            window.location.href = `editEvento.php?id_evento=${idEventoEdit}`;
        }

        // Evento de STATUS
        else if (target.classList.contains('btn-status')) {
            const idEvento = target.getAttribute('data-id');
            const currentStatus = parseInt(target.getAttribute('data-status'));

            fetch('updateStatusEvento.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    id_evento: idEvento,
                    status: currentStatus,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        const newStatus = data.new_status;
                        target.setAttribute('data-status', newStatus);
                        target.style.backgroundColor = newStatus === 1 ? 'green' : 'gray';
                        target.textContent = newStatus === 1 ? 'Aberto' : 'Finalizado';
                    } else {
                        alert(data.message || 'Erro ao atualizar status.');
                    }
                })
                .catch((err) => {
                    console.error('Erro na requisição:', err);
                    alert('Erro ao atualizar status.');
                });
        }
    });
});


    // Ação de pesquisa via AJAX
    $(document).ready(function () {
        $('#pesquisaEvento').on('input', function () {
            const query = $(this).val(); // Captura o valor do input

            // Envia a requisição AJAX
            $.ajax({
                url: 'searchEvento.php', // Endpoint para buscar os dados
                method: 'POST',
                data: { query }, // Envia a pesquisa
                success: function (data) {
                    $('#listaEventos').html(data); // Atualiza a lista
                },
                error: function () {
                    Swal.fire('Erro', 'Não foi possível carregar os resultados.', 'error');
                }
            });
        });
    });
    </script>