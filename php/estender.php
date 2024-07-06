<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "biblioteca";
$port = 3346;  


$conn = new mysqli($servername, $username, $password, $dbname, $port);


if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];

    
    $sql = "SELECT data_fim FROM emprestimos WHERE id=$id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $data_fim = $row["data_fim"];
        
        
        $nova_data_fim = date('Y-m-d', strtotime($data_fim. ' + 7 days'));

        
        $sql = "UPDATE emprestimos SET data_fim='$nova_data_fim' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            $response = [
                'status' => 'success',
                'message' => "Empréstimo $id estendido com sucesso",
                'nova_data_fim' => $nova_data_fim
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => "Erro ao estender empréstimo: " . $conn->error
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => "Empréstimo não encontrado"
        ];
    }

    echo json_encode($response);
}

$conn->close();
?>
