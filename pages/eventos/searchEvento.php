<?php
require_once '../../conexao/Conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $query = trim($_POST['query']); // Texto da pesquisa
    try {
        $conn = Conexao::getConexao();
        $stmt = $conn->prepare(
            "SELECT id_evento, nome_evento, data_hora_inicio, data_hora_termino, status 
             FROM eventos 
             WHERE nome_evento LIKE ? OR local_evento LIKE ?"
        );
        $stmt->execute(["%$query%", "%$query%"]);
        $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($eventos) > 0) {
            foreach ($eventos as $evento) {
                // Define o estilo do botão com base no status do evento
                $buttonClass = $evento['status'] == 'aberto' ? 'btn-aberto' : 'btn-finalizado';
                $buttonText = $evento['status'] == 'aberto' ? 'Aberto' : 'Finalizado';
                ?>
                <div class="cardEvento">
                    <h2><?= htmlspecialchars($evento['nome_evento']); ?></h2>
                    <p>Início: <?= htmlspecialchars($evento['data_hora_inicio']); ?></p>
                    <p>Término: <?= htmlspecialchars($evento['data_hora_termino']); ?></p>
                    <p>Status: <?= htmlspecialchars($buttonText); ?></p>
                    <div class="actions">
                        <button class="<?= $buttonClass; ?>" data-id="<?= $evento['id_evento']; ?>" data-status="<?= $evento['status']; ?>">
                            <?= htmlspecialchars($buttonText); ?>
                        </button>
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
