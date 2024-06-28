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

// Buscar registros de empréstimos
$sql = "SELECT id, nome_aluno, id_aluno, nome_livro, id_livro, data_inicio, data_fim, ativo FROM emprestimos";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Empréstimos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="cabecalho">
        <div class="referencias">
            <nav>
                <div class="inic-nav">
                <a href="index.php" class="pagina-atual">Emprestimos</a>
            </div>
                <!-- <a href="eu.html">Quem Sou Eu</a> -->
                <div class="projet-nav">
                <a href="cadastro.html">Cadastro</a>
            </div>
            </nav>
        </div>
    </div>

    <div class="bloco">
    <div class="container">
        <h1>Lista de Empréstimos</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Aluno</th>
                    <th>ID do Aluno</th>
                    <th>Nome do Livro</th>
                    <th>ID do Livro</th>
                    <th>Data de Início</th>
                    <th>Data de Fim</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if ($result->num_rows > 0) {
                    // Exibir os dados de cada linha
                    while ($row = $result->fetch_assoc()) {
                        $isActive = $row["ativo"];
                        $class = $isActive ? "" : "finalizado";
                        $disabled = $isActive ? "" : "disabled";

                        echo "<tr class='$class'>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["nome_aluno"] . "</td>";
                        echo "<td>" . $row["id_aluno"] . "</td>";
                        echo "<td>" . $row["nome_livro"] . "</td>";
                        echo "<td>" . $row["id_livro"] . "</td>";
                        echo "<td>" . $row["data_inicio"] . "</td>";
                        echo "<td>" . $row["data_fim"] . "</td>";
                        echo "<td>" . ($isActive ? "Sim" : "Não") . "</td>";
                        echo "<td>
                                <form action='estender.php' method='post' style='display:inline-block;'>
                                    <input type='hidden' name='id' value='" . $row["id"] . "'>
                                    <button type='submit' class='estender' $disabled>Estender</button>
                                </form>
                                <form action='finalizar.php' method='post' style='display:inline-block;'>
                                    <input type='hidden' name='id' value='" . $row["id"] . "'>
                                    <button type='submit' class='finalizar' $disabled>Finalizar</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Nenhum empréstimo encontrado</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
