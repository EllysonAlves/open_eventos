<?php
require_once '../../conexao/Conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $query = trim($_POST['query']); // Texto da pesquisa
    try {
        $conn = Conexao::getConexao();
        $stmt = $conn->prepare(
            "SELECT id, nome, email, status 
             FROM usuario 
             WHERE role = 'promoter' AND 
                   (nome LIKE ? OR email LIKE ?)"
        );
        $stmt->execute(["%$query%", "%$query%"]);
        $promoters = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($promoters) > 0) {
            foreach ($promoters as $promoter) {
                $borderColor = $promoter['status'] == 1 ? 'green' : 'red';
                ?>
                <div class="cardAdmin" style="border-left: 3px solid <?= $borderColor; ?>;">
                    <h2><?= htmlspecialchars($promoter['nome']); ?></h2>
                    <p><?= htmlspecialchars($promoter['email']); ?></p>
                    <p>Status: <?= $promoter['status'] == 1 ? 'Ativo' : 'Inativo'; ?></p>
                    <div class="actions">
                        <button class="btn-status" data-id="<?= $promoter['id']; ?>" data-status="<?= $promoter['status']; ?>" title="Alterar Status">
                            <i class="fa-solid fa-repeat"></i>
                        </button>
                        <button class="btn-delete" data-id="<?= $promoter['id']; ?>" title="Excluir">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>Nenhum administrador encontrado.</p>";
        }
    } catch (Exception $e) {
        echo "<p>Erro ao buscar promoters.</p>";
    }
}
