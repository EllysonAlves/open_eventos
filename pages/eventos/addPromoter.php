<?php
require_once '../../conexao/Conexao.php';
session_start();

$idEvento = $_POST['id_evento'] ?? null;
$idPromoter = $_POST['id_promoter'] ?? null;

if ($idEvento && $idPromoter) {
    $conn = Conexao::getConexao();
    $stmt = $conn->prepare("INSERT INTO promoters_eventos (id_evento, id_promoter) VALUES (?, ?)");
    $stmt->execute([$idEvento, $idPromoter]);
    echo json_encode(['message' => 'Promoter adicionado com sucesso.']);
} else {
    echo json_encode(['message' => 'Dados inválidos.']);
}
