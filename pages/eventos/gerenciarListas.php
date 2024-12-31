<?php
require_once '../../conexao/Conexao.php';
session_start();


// Função para verificar permissões
function podeManipularEvento($statusEvento, $roleUsuario) {
    return ($statusEvento !== '0' || in_array($roleUsuario, ['admin', 'master']));
}

// Exemplo de uso no backend
$statusEvento = $_GET['status_evento']; // Pegue o status do banco de dados
$roleUsuario = $_SESSION['role']; // Obtenha a role do usuário atual

if (!podeManipularEvento($statusEvento, $roleUsuario)) {
    http_response_code(403);
    echo json_encode(['error' => 'Ação não permitida.']);
    exit;
}


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

    if ($roleUsuario == 'promoter') {
        $stmtListas = $conn->prepare("SELECT * FROM listas WHERE id_evento = ? AND id_lista IN (SELECT id_lista FROM promoters_listas WHERE id_promoter = ?)");
        $stmtListas->execute([$idEvento, $_SESSION['id']]);
    } else {
        // Caso contrário, trazer todas as listas do evento
        $stmtListas = $conn->prepare("SELECT * FROM listas WHERE id_evento = ?");
        $stmtListas->execute([$idEvento]);
    }

    $listas = $stmtListas->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Erro ao carregar informações do evento.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../components/sidebar/style.css?v=8">
    <link rel="stylesheet" href="style.css?v=14">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
        <?php if (in_array($_SESSION['role'] ?? 'guest', ['admin', 'master'])): ?>
            <div class="topo">
                <button id="addLista" class="btn-add">Adicionar Lista</button>
            </div>
        <?php endif; ?>
        <div id="formAddLista" style="display:none;">
            <form action="addLista.php?id_evento=<?= $idEvento ?>&status_evento=<?= $statusEvento ?>" method="POST">
                <input type="hidden" name="id_evento" value="<?= htmlspecialchars($idEvento); ?>">
                <input type="hidden" name="status_evento" value="<?= htmlspecialchars($statusEvento); ?>">

                <div>
                    <label for="nome_lista">Nome da Lista:</label>
                    <input type="text" id="nome_lista" name="nome_lista" required>
                </div>
                <div>
                    <label for="nome_lista">Numero maximo de clientes:</label>
                    <input type="number" id="lenght_lista" name="lenght_lista" required>
                </div>
                <div id="select">
                    <label for="promoter">Promoters</label>
                    <select name="id_promoter[]" id="id_promoter" multiple="multiple" required>
                        <?php
                        try {
                            $conn = Conexao::getConexao();
                            $stmt = $conn->prepare("SELECT id, nome FROM usuario WHERE role = 'promoter'");
                            $stmt->execute();
                            $promoters = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($promoters as $promoter) {
                                echo "<option value='" . $promoter['id'] . "'>" . htmlspecialchars($promoter['nome']) . "</option>";
                            }
                        } catch (Exception $e) {
                            echo "<p>Erro ao carregar promoters.</p>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Checkbox para lista pública -->
                <div class="public-list-option">
                    <input type="checkbox" id="publica" name="publica" value="1">
                    <label for="publica">Tornar esta lista pública</label>
                    <p class="info-text">
                        Será criado um link para uma página pública que pode ser compartilhado. 
                        <a href="http://localhost/open_eventos/pages/eventos/exemploPaginaPublica.php" target="_blank">CLIQUE AQUI</a> e veja um exemplo.
                    </p>
                </div>

                <button type="submit">Adicionar Lista</button>
            </form>

                    
        </div>
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
                            <?php if ($lista['publica'] === 1): ?>
                                <p><strong>Link para esta lista pública:</strong></p>
                                <input type="text" 
                                    readonly 
                                    id="input-link"
                                    value="<?= 'http://localhost/open_eventos/pages/eventos/autoAdicionar.php?id_lista=' . $lista['id_lista']; ?>" 
                                    class="input-link">
                                <button id="btn-copiar-link" class="btn-copiar-link">Copiar Link</button>
                            <?php else: ?>
                                <p>Esta lista é privada.</p>
                            <?php endif; ?>
                                <p><strong>Clientes na lista:</strong></p>
                                <?php
                                $stmtClientes = $conn->prepare("SELECT * FROM clientes_listas WHERE id_lista = ?");
                                $stmtClientes->execute([$lista['id_lista']]);
                                $clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

                                if (!empty($clientes)) {
                                    echo '<ul class="clientes-lista">';
                                    $permissoesPermitidas = ['admin', 'master', 'promoter']; // Definindo as roles permitidas

                                    foreach ($clientes as $cliente) {
                                        echo "<li>" . htmlspecialchars($cliente['nome_cliente']);
                                    
                                        // Verifica o status atual do cliente
                                        $statusConfirmacao = htmlspecialchars($cliente['status_confirmacao'] ?? 'pendente');
                                    
                                        // Botão para alterar o status

                                        if(in_array($roleUsuario, $permissoesPermitidas)){
                                        echo "
                                            <div class='status-actions'>
                                                <span class='status-label' data-status='$statusConfirmacao'>
                                                    Status: $statusConfirmacao
                                                </span>
                                                <button 
                                                    class='btn-status-cliente' 
                                                    data-id=" . $cliente['id_cliente'] . " 
                                                    data-status='$statusConfirmacao'>
                                                    <i class='status-icon'></i> Alterar Status
                                                </button>
                                            </div>

                                        ";
                                    }
                                        // Verifica permissões para exclusão
                                        if (in_array($_SESSION['role'] ?? 'guest', $permissoesPermitidas)) {
                                            echo "
                                                <button class='btn-delete-cliente' data-id='" . $cliente['id_cliente'] . "'>
                                                    <i class='fa fa-trash'></i>
                                                </button>
                                            ";
                                        }
                                    
                                        echo "</li>";
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<p>Não há clientes cadastrados nesta lista.</p>';
                                }
                                ?>
                            </div>
                            <div class="card-actions">
                                
                                <?php if (in_array($_SESSION['role'] ?? 'guest', ['admin', 'master', 'promoter'])): ?>
                                    <a href="adicionarCliente.php?id_lista=<?= htmlspecialchars($lista['id_lista']); ?>&id_evento=<?= htmlspecialchars($idEvento); ?>" class="btn-action btn-add-cliente">
                                        Adicionar Clientes
                                    </a>
                                <?php endif; ?>
                                <?php if (in_array($_SESSION['role'] ?? 'guest', ['admin', 'master'])): ?>
                                    <button class="btn-action btn-delete-lista" data-id="<?= $lista['id_lista']; ?>">Excluir</button>
                                <?php endif; ?>
                                
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../components/sidebar/main.js?v=6"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>

        // Selecionar os elementos
    const inputLink = document.getElementById('input-link');
    const btnCopiarLink = document.getElementById('btn-copiar-link');

    if(btnCopiarLink){
        // Adicionar evento ao botão
    btnCopiarLink.addEventListener('click', function () {
        // Selecionar o texto no input
        inputLink.select();
        inputLink.setSelectionRange(0, 99999); // Para dispositivos móveis

        // Copiar o texto para a área de transferência
        navigator.clipboard.writeText(inputLink.value)
            .then(() => {
                alert('Link copiado para a área de transferência!');
            })
            .catch(err => {
                console.error('Erro ao copiar o link: ', err);
            });
    });
    }

        $(document).ready(function() {
            // Inicializa o Select2 no campo de seleção múltipla
            $('#id_promoter').select2({
                width: '200px',
                placeholder: 'Selecione os promoters', // Texto que aparece quando nenhum item é selecionado
                allowClear: true // Permite limpar a seleção
            });
        });
        // Verificar se o botão 'addLista' existe
        const addListaButton = document.getElementById('addLista');
        if (addListaButton) {
            addListaButton.addEventListener('click', () => { 
                const formAddLista = document.getElementById('formAddLista');
                if (formAddLista) {
                    formAddLista.style.display = 'flex';
                }
            });
        } 


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


        document.querySelectorAll('.btn-delete-cliente').forEach(button => {
        button.addEventListener('click', () => {
            const idCliente = button.getAttribute('data-id');

            Swal.fire({
                title: 'Tem certeza?',
                text: 'Esta ação não pode ser desfeita!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('deleteCliente.php', { id_cliente: idCliente }, function (response) {
                        if (response.success) {
                            Swal.fire('Excluído!', 'O cliente foi removido da lista.', 'success');
                            location.reload();
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        });
    });



    const userRole = '<?= $_SESSION['role'] ?? 'guest'; ?>'; // Se não autenticado, define como "guest";
    document.addEventListener('DOMContentLoaded', () => {
    if (!['admin', 'promoter', 'master'].includes(userRole)) {
        document.querySelectorAll('.btn-delete-cliente').forEach(button => {
            button.style.display = 'none'; // Oculta o botão
        });
    }
});

document.querySelectorAll('.btn-status-cliente').forEach(button => {
    button.addEventListener('click', () => {
        const idCliente = button.getAttribute('data-id');
        const currentStatus = button.getAttribute('data-status');

        Swal.fire({
            title: 'Alterar Status',
            input: 'select',
            inputOptions: {
                pendente: 'Pendente',
                confirmado: 'Confirmado',
                cancelado: 'Cancelado'
            },
            inputValue: currentStatus,
            showCancelButton: true,
            confirmButtonText: 'Atualizar',
            cancelButtonText: 'Cancelar',
            preConfirm: (newStatus) => {
                return new Promise((resolve) => {
                    $.post('updateStatusCliente.php', { id_cliente: idCliente, status: newStatus }, function(response) {
                        if (response.success) {
                            resolve(response.message);
                        } else {
                            Swal.showValidationMessage(`Erro: ${response.message}`);
                        }
                    }, 'json');
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Atualizado!', 'O status foi alterado.', 'success').then(() => {
                    location.reload();
                });
            }
        });
    });
});


document.querySelectorAll('.btn-status-cliente').forEach(button => {
    const status = button.getAttribute('data-status');
    const icon = button.querySelector('.status-icon');

    // Atribuir ícones diferentes para cada status
    if (status === "Ativo") {
        icon.innerHTML = "✔️"; // Exemplo de ícone
    } else if (status === "Inativo") {
        icon.innerHTML = "❌";
    } else if (status === "Pendente") {
        icon.innerHTML = "⏳";
    }
});


    </script>
</body>
</html>
