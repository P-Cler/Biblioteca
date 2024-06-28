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

    // Buscar o registro atual
    $sql = "SELECT data_fim FROM emprestimos WHERE id=$id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $data_fim = $row["data_fim"];
        
        // Estender a data de fim em 7 dias
        $nova_data_fim = date('Y-m-d', strtotime($data_fim. ' + 7 days'));

        // Atualizar o registro no banco de dados
        $sql = "UPDATE emprestimos SET data_fim='$nova_data_fim' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo "Empréstimo estendido com sucesso. <a href='index.php'>Voltar à lista de empréstimos</a>";
        } else {
            echo "Erro ao estender empréstimo: " . $conn->error;
        }
    } else {
        echo "Empréstimo não encontrado. <a href='index.php'>Voltar à lista de empréstimos</a>";
    }
}

$conn->close();
?>
