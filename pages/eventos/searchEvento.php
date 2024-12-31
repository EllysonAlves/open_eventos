<?php
require_once '../../conexao/Conexao.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $query = trim($_POST['query']); // Texto da pesquisa
    try {
        $conn = Conexao::getConexao();
        $stmt = $conn->prepare(
            "SELECT id_evento, nome_evento, data_hora_inicio, data_hora_termino, local_evento, descricao, status 
             FROM eventos 
             WHERE nome_evento LIKE ? OR local_evento LIKE ?"
        );
        $stmt->execute(["%$query%", "%$query%"]);
        $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($eventos) > 0) {
            foreach ($eventos as $evento) {
                // Define estilo e texto do botão de status
                $buttonColor = $evento['status'] == 1 ? 'green' : 'gray';
                $buttonText = $evento['status'] == 1 ? 'Aberto' : 'Finalizado';

                // Verifica permissão do usuário para manipular eventos finalizados
                $usuarioPodeEditar = in_array($_SESSION['role'] ?? 'guest', ['admin', 'master']);
                $desabilitarListas = $evento['status'] == 0 && !$usuarioPodeEditar; // Evento finalizado e não é admin/master
                ?>
                <div class="cardEvento">
                    <h2><?= htmlspecialchars($evento['nome_evento']); ?></h2>
                    <p>Início: <?= htmlspecialchars($evento['data_hora_inicio']); ?></p>
                    <p>Término: <?= htmlspecialchars($evento['data_hora_termino']); ?></p>
                    <p>Local: <?= htmlspecialchars($evento['local_evento']); ?></p>
                    <p><?php echo $evento['descricao'] ?></p>
                    <div class="actions">
                        <?php if (!in_array($_SESSION['role'] ?? 'guest', ['recepcionista', 'user', 'promoter'])): ?>
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
                        <a href="gerenciarListas.php?id_evento=<?= $evento['id_evento']; ?>&status_evento=<?= $evento['status']; ?>" 
                           class="btn-lists <?= $desabilitarListas ? 'disabled' : ''; ?>" 
                           title="<?= $desabilitarListas ? 'Somente administradores podem manipular eventos finalizados.' : 'Gerenciar Listas'; ?>" 
                           <?= $desabilitarListas ? 'tabindex="-1" aria-disabled="true" onclick="return false;"' : ''; ?>>
                            <i class="fa-solid fa-users"></i> Listas
                        </a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>Nenhum evento encontrado.</p>";
        }
    } catch (Exception $e) {
        echo "<p>Erro ao buscar eventos.</p>";
    }
}
?>

<style>
    .btn-aberto {
        background-color: green;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    .btn-finalizado {
        background-color: gray;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    .cardEvento {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        margin: 10px 0;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .cardEvento h2 {
        margin: 0 0 5px;
        font-size: 1.5em;
    }

    .cardEvento p {
        margin: 5px 0;
        color: #555;
    }

    .actions {
        margin-top: 10px;
    }
</style>
