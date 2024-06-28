<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "biblioteca";
$port = 3346;  // Porta do MySQL

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];

    // Atualizar o registro no banco de dados para definir o empréstimo como inativo
    $sql = "UPDATE emprestimos SET ativo=FALSE WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        // Redirecionar para a página principal após finalizar o empréstimo
        header('Location: index.php');
        exit;
    } else {
        echo "Erro ao finalizar empréstimo: " . $conn->error . ". <a href='index.php'>Voltar à lista de empréstimos</a>";
    }
}

$conn->close();
?>
