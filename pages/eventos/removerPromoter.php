<?php
require_once '../../conexao/Conexao.php';
session_start();

$idEvento = $_POST['id_evento'] ?? null;
$idPromoter = $_POST['id_promoter'] ?? null;

if ($idEvento && $idPromoter) {
    $conn = Conexao::getConexao();
    $stmt = $conn->prepare("DELETE FROM evento_promoter WHERE id_evento = ? AND id_promoter = ?");
    $stmt->execute([$idEvento, $idPromoter]);
    echo json_encode(['message' => 'Promoter removido com sucesso.']);
} else {
    echo json_encode(['message' => 'Dados inválidos.']);
}
