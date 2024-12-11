<?php
require_once '../../conexao/Conexao.php';

session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../../login/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $sobrenome = $_POST['sobrenome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Validação simples
    if (!empty($nome) && !empty($sobrenome) && !empty($email) && !empty($senha)) {
        try {
            $conexao = Conexao::getConexao();

            // Verifica se o e-mail já existe
            $checkEmailStmt = $conexao->prepare("SELECT COUNT(*) FROM usuario WHERE email = ?");
            $checkEmailStmt->execute([$email]);
            $emailExists = $checkEmailStmt->fetchColumn();

            if ($emailExists > 0) {
                // E-mail já cadastrado
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'warning',
                            title: 'E-mail já cadastrado',
                            text: 'O e-mail informado já está registrado no sistema.',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>";
            } else {
                // Insere o novo administrador
                $stmt = $conexao->prepare("INSERT INTO usuario (nome, sobrenome, email, senha, role,status) VALUES (?, ?, ?, ?, ?,?)");
                $stmt->execute([$nome, $sobrenome, $email, md5($senha), 'promoter', 1]);
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Administrador Adicionado!',
                            text: 'O administrador foi cadastrado com sucesso.',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>";
            }
        } catch (Exception $e) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao Adicionar',
                        text: 'Ocorreu um erro ao tentar cadastrar o administrador. Detalhes: " . $e->getMessage() . "',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
        }
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos Incompletos',
                    text: 'Por favor, preencha todos os campos.',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../components/sidebar/style.css?v=3">
    <link rel="stylesheet" href="style.css?v=3">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Adicionar Promoters</title>
</head>
<body>
    <?php require_once '../../components/sidebar/sideBar.php'; ?>
    <main class="main">
        <div>
            <h1>ADICIONAR PROMOTERS</h1>
            <p><a href="../eventos/">HOME </a>/ ADICIONAR PROMOTERS</p>
        </div>

        <div class="meio">
            <div class="listaAdmin">
               <form method="POST" action="">
                    <div class="form-row">
                        <div>
                            <label for="nome">Nome</label>
                            <input name="nome" id="nome" type="text" placeholder="Nome do promoters" required>
                        </div>
                        <div>
                            <label for="sobrenome">Sobrenome</label>
                            <input name="sobrenome" id="sobrenome" type="text" placeholder="Sobrenome do promoters" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div>
                            <label for="email">Email</label>
                            <input name="email" id="email" type="email" placeholder="E-mail do promoters" required>
                        </div>
                        <div>
                            <label for="senha">Senha</label>
                            <input name="senha" id="senha" type="password" placeholder="Senha do promoters" required>
                        </div>
                    </div>
                    <button type="submit">Adicionar</button>
               </form>
               
               <button id="voltarBtn" class="voltarBtn">VOLTAR</button>
            </div>
            <div class="info">
            <h2>Promoters</h2>
                <p>Os promoters são as pessoas que vão trabalhar na divulgação do seus eventos.</p>
                <p>Eles podem adicionar convidados às listas através do aplicativo (iOS e Android) ou utilizando um computador.</p>
                <p>Importante: Os promoters não podem criar eventos, nem adicionar outros promoters e nem visualizar todos os dados e relátorios. Eles só podem adicionar convidados nas listas que forem adicionados e só conseguem ver os relatórios referentes aos convidados que ele próprio adicionou.</p>
            </div>
        </div>
    </main>

<script>
    const voltarBtn = document.getElementById('voltarBtn').addEventListener('click', () =>{ window.location.href = '/pages/promoters/';})
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../components/sidebar/main.js?v=3"></script>
</body>
</html>