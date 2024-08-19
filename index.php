<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "biblioteca";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$results_per_page = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

$order = isset($_GET['order']) ? $_GET['order'] : 'asc';
$sort_by = isset($_GET['sort_by']) && in_array($_GET['sort_by'], ['id', 'nome_aluno', 'nome_livro', 'data_inicio', 'data_fim', 'ativo']) ? $_GET['sort_by'] : 'id';


$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';


$emprestimos = [];
$total_pages = 0;


$sql_total = "SELECT COUNT(*) AS total FROM emprestimos";
if (!empty($search_term)) {
    $sql_total .= " WHERE nome_aluno LIKE ?";
}

$stmt_total = $conn->prepare($sql_total);
if (!empty($search_term)) {
    $search_term_param = "%$search_term%";
    $stmt_total->bind_param("s", $search_term_param);
}
$stmt_total->execute();
$result_total = $stmt_total->get_result();


if ($result_total && $result_total->num_rows > 0) {
    $row_total = $result_total->fetch_assoc();
    $total_pages = ceil($row_total["total"] / $results_per_page);
}


$sql = "SELECT id, nome_aluno, id_aluno, nome_livro, id_livro, data_inicio, data_fim, ativo 
        FROM emprestimos";
if (!empty($search_term)) {
    $sql .= " WHERE nome_aluno LIKE '%$search_term%'";
}
$sql .= " ORDER BY $sort_by $order LIMIT $start_from, $results_per_page";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['intervalo'] = calculateInterval($row["data_fim"]);
        $emprestimos[] = $row;
    }
} else {
    $emprestimos = [];
}

$conn->close();

function calculateInterval($data_fim)
{
    $dataAtual = date('Y-m-d');
    $intervalo = strtotime($data_fim) - strtotime($dataAtual);
    return floor($intervalo / (60 * 60 * 24));
}

function getRowClass($row)
{
    $dataAtual = date('Y-m-d');
    $dataFim = $row['data_fim'];

    if (!$row['ativo']) {
        return 'finalizado';
    }

    $intervalo = strtotime($dataFim) - strtotime($dataAtual);
    $dias = floor($intervalo / (60 * 60 * 24));

    if ($dias < 0) {
        return 'vermelho';
    } elseif ($dias <= 3) {
        return 'amarelo';
    } else {
        return '';
    }
}


if ($sort_by == 'intervalo') {
    usort($emprestimos, function ($a, $b) use ($order) {
        return $order === 'asc' ? $a['intervalo'] - $b['intervalo'] : $b['intervalo'] - $a['intervalo'];
    });
}



?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Empréstimos</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="icon" href="./favicon/images.jpeg" type="image/x-icon">
</head>

<body>
    <div class="cabecalho">
        <div class="referencias">
            <nav>
                <div class="inic-nav">
                    <a href="index.php" class="pagina-atual">Empréstimos</a>
                </div>
                <div class="projet-nav">
                    <a href="cadastro.php">Cadastro</a>
                </div>
            </nav>
        </div>
    </div>

    <div class="bloco">
        <div class="lista">
            <div id="mensagem"></div>
            <div class="titulo-e-pesquisa">
                <h1>Lista de Empréstimos</h1>
                <form method="GET" action="index.php" class="pesquisa-container">
                    <input type="text" name="search" placeholder="Pesquisar pelo nome do aluno"
                        value="<?php echo htmlspecialchars($search_term); ?>">
                    <div class="botoes-container">
                        <button type="submit">Pesquisar</button>
                        <a href="index.php" style="text-decoration: none;">
                            <button type="button">Limpar Pesquisa</button>
                        </a>
                    </div>
                    <input type="hidden" name="sort_by" value="<?php echo $sort_by; ?>">
                    <input type="hidden" name="order" value="<?php echo $order; ?>">
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th><a
                                href="?sort_by=id&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>&page=<?php echo $page; ?>&search=<?php echo $search_term; ?>">ID</a>
                        </th>
                        <th><a
                                href="?sort_by=nome_aluno&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>&page=<?php echo $page; ?>&search=<?php echo $search_term; ?>">Nome
                                do Aluno</a></th>
                        <th>ID do Aluno</th>
                        <th><a
                                href="?sort_by=nome_livro&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>&page=<?php echo $page; ?>&search=<?php echo $search_term; ?>">Nome
                                do Livro</a></th>
                        <th>ID do Livro</th>
                        <th><a
                                href="?sort_by=data_inicio&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>&page=<?php echo $page; ?>&search=<?php echo $search_term; ?>">Data
                                de Início</a></th>
                        <th><a
                                href="?sort_by=data_fim&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>&page=<?php echo $page; ?>&search=<?php echo $search_term; ?>">Data
                                de Fim</a></th>
                        <th><a
                                href="?sort_by=ativo&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>&page=<?php echo $page; ?>&search=<?php echo $search_term; ?>">Ativo</a>
                        </th>
                        <th>Ações</th>
                        <th><a
                                href="?sort_by=intervalo&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>&page=<?php echo $page; ?>&search=<?php echo $search_term; ?>">Intervalo</a>
                        </th>
                    </tr>
                </thead>
                <tbody id="emprestimos-body">
                    <?php
                    foreach ($emprestimos as $row) {
                        echo "<tr id='row-{$row['id']}' class='" . ($row["ativo"] ? getRowClass($row) : "finalizado") . "'>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["nome_aluno"] . "</td>";
                        echo "<td>" . $row["id_aluno"] . "</td>";
                        echo "<td>" . $row["nome_livro"] . "</td>";
                        echo "<td>" . $row["id_livro"] . "</td>";
                        echo "<td class='data-inicio'>" . $row["data_inicio"] . "</td>";
                        echo "<td class='data-fim'>" . $row["data_fim"] . "</td>";
                        echo "<td class='ativo'>" . ($row["ativo"] ? "Sim" : "Não") . "</td>";
                        echo "<td>
                    <form class='estenderForm' style='display:inline-block;'>
                        <input type='hidden' name='id' value='" . $row["id"] . "'>
                        <button type='submit' class='estender' " . ($row["ativo"] ? "" : "disabled") . ">Estender</button>
                    </form>
                    <form action='./php/finalizar.php?page=" . $page . "&sort_by=$sort_by&order=$order' method='post' style='display:inline-block;'>
                        <input type='hidden' name='id' value='" . $row["id"] . "'>
                        <button type='submit' class='finalizar' " . ($row["ativo"] ? "" : "disabled") . ">Finalizar</button>
                    </form>
                </td>";
                        echo "<td>" . calculateInterval($row["data_fim"]) . " dias</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php
                if ($total_pages > 1) {
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<a href='?page=$i&sort_by=$sort_by&order=$order&search=$search_term'" . ($i == $page ? " class='active'" : "") . ">$i</a>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.estenderForm').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const id = formData.get('id');
            
            fetch('./php/estender.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const row = document.getElementById(`row-${id}`);
                if (data.success) {
                    // Atualize a data de fim na tabela
                    row.querySelector('.data-fim').textContent = data.nova_data_fim;
                    
                    // Atualize o intervalo na tabela
                    row.querySelector('.intervalo').textContent = data.novo_intervalo + ' dias';
                    
                    // Atualize a classe da linha com a nova classe fornecida
                    row.className = data.classe; 
                    
                    console.log('Empréstimo estendido com sucesso!');
                } else {
                    console.log('Erro ao estender empréstimo: ' + data.message);
                }
            })
            .catch(error => console.error('Erro:', error));
        });
    });
});
</script>


</body>

</html>