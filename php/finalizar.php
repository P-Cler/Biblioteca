<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "biblioteca";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    $sql = "UPDATE emprestimos SET ativo=FALSE WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header('Location: ../index.php?page=' . $page);
        exit;
    } else {
        echo "Erro ao finalizar empréstimo: " . $conn->error . ". <a href='../index.php?page=" . $page . "'>Voltar à lista de empréstimos</a>";
    }
}

$conn->close();
?>
