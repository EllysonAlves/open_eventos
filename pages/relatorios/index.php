<?php
require_once '../../conexao/Conexao.php';

session_start();

if(!isset($_SESSION['id']) ){
    header("location: ../../login/index.php");
}else{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../components/sidebar/style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <title>Administradores</title>
</head>
<body>
    <?php
    require_once '../../components/sidebar/sideBar.php';
    ?>
    



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../components/sidebar/main.js"></script>
</body>
</html>










<?php
}
?>
