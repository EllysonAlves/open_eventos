<?php
require_once '../../conexao/Conexao.php';
session_start();

$idEvento = $_GET['id_evento'] ?? null;
if (!$idEvento) {
    echo "Evento inválido.";
    exit();
}

try {
    $conn = Conexao::getConexao();

    // Buscar informações do evento
    $stmtEvento = $conn->prepare("SELECT nome_evento FROM eventos WHERE id_evento = ?");
    $stmtEvento->execute([$idEvento]);
    $evento = $stmtEvento->fetch(PDO::FETCH_ASSOC);

    // Buscar listas associadas
    $stmtListas = $conn->prepare("SELECT * FROM listas WHERE id_evento = ?");
    $stmtListas->execute([$idEvento]);
    $listas = $stmtListas->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Erro ao carregar informações do evento.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../components/sidebar/style.css?v=3">
    <link rel="stylesheet" href="style.css?v=6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <title>Gerenciar Listas - <?= htmlspecialchars($evento['nome_evento']); ?></title>
</head>
<body>
    <?php
    require_once '../../components/sidebar/sideBar.php';
    ?>
    <main class="main">
        <h1>Gerenciar Listas: <?= htmlspecialchars($evento['nome_evento']); ?></h1>

        <!-- Formulário para Adicionar Lista -->
        <div class="topo">
            <button id="addLista" class="btn-add">Adicionar Lista</button>
        </div>
        <div id="formAddLista" style="display:none;">
            <form action="addLista.php" method="POST">
                <input type="hidden" name="id_evento" value="<?= htmlspecialchars($idEvento); ?>">
                <label for="nome_lista">Nome da Lista:</label>
                <input type="text" id="nome_lista" name="nome_lista" required>
                <button type="submit">Adicionar Lista</button>
            </form>
        </div>

        <div class="listas">
            <?php if (empty($listas)) { ?>
                <div class="lista-empty">
                    <h2>Sem Listas</h2>
                    <p>Não há listas cadastradas para este evento ainda.</p>
                </div>
            <?php } else { ?>
                <div class="lista-container">
                    <?php foreach ($listas as $lista) { ?>
                        <div class="card-lista">
                            <div class="card-header">
                                <h3><?= htmlspecialchars($lista['nome_lista']); ?></h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Clientes na lista:</strong></p>
                                <?php
                                $stmtClientes = $conn->prepare("SELECT * FROM clientes_listas WHERE id_lista = ?");
                                $stmtClientes->execute([$lista['id_lista']]);
                                $clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

                                if (!empty($clientes)) {
                                    echo '<ul class="clientes-lista">';
                                    foreach ($clientes as $cliente) {
                                        echo "<li>" . htmlspecialchars($cliente['nome_cliente']) . "</li>";
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<p>Não há clientes cadastrados nesta lista.</p>';
                                }
                                ?>
                            </div>
                            <div class="card-actions">
                                <a href="adicionarCliente.php?id_lista=<?= htmlspecialchars($lista['id_lista']); ?>" class="btn-action btn-add-cliente">
                                    Adicionar Clientes
                                </a>
                                <button class="btn-action btn-delete-lista" data-id="<?= $lista['id_lista']; ?>">Excluir</button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../components/sidebar/main.js"></script>
    <script>
        // Ação para mostrar o formulário de adicionar lista
        document.getElementById('addLista').addEventListener('click', () => { 
            document.getElementById('formAddLista').style.display = 'block';
        });

        // Excluir Lista
        document.querySelectorAll('.btn-delete-lista').forEach(button => {
            button.addEventListener('click', () => {
                const idLista = button.getAttribute('data-id');
                Swal.fire({
                    title: 'Tem certeza?',
                    text: "Esta ação não pode ser desfeita!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('deleteLista.php', { id_lista: idLista }, function(response) {
                            if (response.success) {
                                Swal.fire('Excluído!', 'A lista foi excluída.', 'success');
                                location.reload();
                            } else {
                                Swal.fire('Erro!', response.message, 'error');
                            }
                        }, 'json');
                    }
                });
            });
        });
    </script>
</body>
</html>
