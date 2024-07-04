<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "biblioteca";
$port = "3346";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar conexão
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
    $ativo = TRUE;

    $sql = "INSERT INTO emprestimos (nome_aluno, id_aluno, nome_livro, id_livro, data_inicio, data_fim, ativo)
            VALUES ('$nome_aluno', '$id_aluno', '$nome_livro', '$id_livro', '$data_inicio', '$data_fim', $ativo)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("status" => "success", "message" => "Novo empréstimo adicionado com sucesso"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Erro: " . $sql . "<br>" . $conn->error));
    }

    $conn->close();
}
?>
