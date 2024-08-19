<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "biblioteca";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Conexão falhou: " . $conn->connect_error)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_aluno = $_POST["nome_aluno"];
    $id_aluno = $_POST["id_aluno"];
    $nome_livro = $_POST["nome_livro"];
    $id_livro = $_POST["id_livro"];
    $data_inicio = $_POST["data_inicio"];
    $data_fim = date('Y-m-d', strtotime($data_inicio . ' + 15 days'));
    $ativo = 1; 

    $stmt = $conn->prepare("INSERT INTO emprestimos (nome_aluno, id_aluno, nome_livro, id_livro, data_inicio, data_fim, ativo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $nome_aluno, $id_aluno, $nome_livro, $id_livro, $data_inicio, $data_fim, $ativo);

    if ($stmt->execute() === TRUE) {
        echo json_encode(array("status" => "success", "message" => "Novo empréstimo adicionado com sucesso"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Erro: " . $stmt->error));
    }

    $stmt->close();
    $conn->close();
}
?>
