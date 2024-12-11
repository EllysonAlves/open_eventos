<?php
require_once '../../conexao/Conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $query = trim($_POST['query']); // Texto da pesquisa
    try {
        $conn = Conexao::getConexao();
        $stmt = $conn->prepare(
            "SELECT id, nome, email, status 
             FROM usuario 
             WHERE role = 'recepcionista' AND 
                   (nome LIKE ? OR email LIKE ?)"
        );
        $stmt->execute(["%$query%", "%$query%"]);
        $recepcionistas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($recepcionistas) > 0) {
            foreach ($recepcionistas as $recepcionista) {
                $borderColor = $recepcionista['status'] == 1 ? 'green' : 'red';
                ?>
                <div class="cardAdmin" style="border-left: 3px solid <?= $borderColor; ?>;">
                    <h2><?= htmlspecialchars($recepcionista['nome']); ?></h2>
                    <p><?= htmlspecialchars($recepcionista['email']); ?></p>
                    <p>Status: <?= $recepcionista['status'] == 1 ? 'Ativo' : 'Inativo'; ?></p>
                    <div class="actions">
                        <button class="btn-status" data-id="<?= $recepcionista['id']; ?>" data-status="<?= $recepcionista['status']; ?>" title="Alterar Status">
                            <i class="fa-solid fa-repeat"></i>
                        </button>
                        <button class="btn-delete" data-id="<?= $recepcionista['id']; ?>" title="Excluir">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>Nenhum recepcionista encontrado.</p>";
        }
    } catch (Exception $e) {
        echo "<p>Erro ao buscar recepcionista.</p>";
    }
}
