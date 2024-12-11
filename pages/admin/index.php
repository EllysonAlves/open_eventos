<?php
require_once '../../conexao/Conexao.php';

session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

$administradores = [];

try {
    $conn = Conexao::getConexao();
    $stmt = $conn->prepare("SELECT id, nome, email, status FROM usuario WHERE role = 'admin'");
    $stmt->execute();
    $administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<p>Erro ao buscar administradores.</p>";
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
    <title>Administradores</title>
</head>
<body>
    <?php
    require_once '../../components/sidebar/sideBar.php';
    ?>
    <main class="main">
        <div>
            <h1>ADMINISTRADORES</h1>
            <p><a href="../eventos/">HOME </a>/ ADMINISTRADORES</p>
        </div>

        <div class="topo">
            <button id="addAdmin">Adicionar Administradores</button>
            <div class="pesquisa">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input name="pesquisaAdmin" id="pesquisaAdmin" type="text" placeholder="Pesquisar por administradores">
            </div>
        </div>
        <div class="meio">
            <div class="listaAdmin <?= empty($administradores) ? 'empty' : ''; ?>" id="listaAdmin">
                <?php
                if (empty($administradores)) {
                    echo '<img src="assets/group.png" alt="Sem administradores" style="width: 150px; height: 150px;">
                    <h2>Não há administradores cadastrados no momento.</h2>
                    <p class="mensagem-vazia" id="mensagemAdmin" style="color: gray; font-style: italic; text-align: center;" aria-live="polite">
                        Adicione as pessoas que vão ajudá-lo na administração.
                    </p>';

                } else {
                    foreach ($administradores as $admin) {
                        $borderColor = $admin['status'] == 1 ? 'green' : 'red';
                        ?>
                        <div class="cardAdmin" style="border-left: 3px solid <?= $borderColor; ?>;">
                            <h2><?= htmlspecialchars($admin['nome']); ?></h2>
                            <p><?= htmlspecialchars($admin['email']); ?></p>
                            <p>Status: <?= $admin['status'] == 1 ? 'Ativo' : 'Inativo'; ?></p>
                            <div class="actions">
                                <button class="btn-status" data-id="<?= $admin['id']; ?>" data-status="<?= $admin['status']; ?>" title="Alterar Status">
                                    <i class="fa-solid fa-repeat"></i>
                                </button>
                                <button class="btn-delete" data-id="<?= $admin['id']; ?>" title="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <?php
                                }
                        
                            
                            ?>
                
            </div>
            <div class="info">
                <h2>Administradores</h2>
                <p>Os administradores são pessoas de sua confiança.</p>
                <p>Eles têm acesso total à plataforma. Podem criar e excluir eventos e listas, adicionar e remover outros usuários, visualizar todos os convidados e relatórios. Além de poderem efetuar o check-in dos convidados.</p>
            </div>
            <?php 
                }

                ?>
            
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../components/sidebar/main.js?v=2"></script>
</body>
</html>
<script>
    const addAdmin = document.getElementById('addAdmin').addEventListener('click', () => { 
        window.location.href = 'addAdmin.php'; 
    });

    // Ação de excluir
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
                    $.post('deleteAdmin.php', { id: userId }, function(response) {
                        if (response.success) {
                            Swal.fire('Excluído!', 'O administrador foi excluído.', 'success');
                            location.reload();
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        });
    });

    // Ação de trocar status
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

    // Ação de pesquisa via AJAX
    $(document).ready(function () {
        $('#pesquisaAdmin').on('input', function () {
            const query = $(this).val(); // Captura o valor do input

            // Envia a requisição AJAX
            $.ajax({
                url: 'searchAdmin.php', // Endpoint para buscar os dados
                method: 'POST',
                data: { query }, // Envia a pesquisa
                success: function (data) {
                    $('#listaAdmin').html(data); // Atualiza a lista
                },
                error: function () {
                    Swal.fire('Erro', 'Não foi possível carregar os resultados.', 'error');
                }
            });
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>