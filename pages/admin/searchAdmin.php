<?php
require_once '../../conexao/Conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $query = trim($_POST['query']); // Texto da pesquisa
    try {
        $conn = Conexao::getConexao();
        $stmt = $conn->prepare(
            "SELECT id, nome, email, status 
             FROM usuario 
             WHERE role = 'admin' AND 
                   (nome LIKE ? OR email LIKE ?)"
        );
        $stmt->execute(["%$query%", "%$query%"]);
        $administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($administradores) > 0) {
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
        } else {
            echo "<p>Nenhum administrador encontrado.</p>";
        }
    } catch (Exception $e) {
        echo "<p>Erro ao buscar administradores.</p>";
    }
}