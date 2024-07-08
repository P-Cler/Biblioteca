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

$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

$order = isset($_GET['order']) ? $_GET['order'] : 'asc';
$sort_by = isset($_GET['sort_by']) && in_array($_GET['sort_by'], ['id', 'nome_aluno', 'nome_livro', 'data_inicio', 'data_fim', 'ativo']) ? $_GET['sort_by'] : 'id';

// Inicializar variáveis
$emprestimos = [];
$total_pages = 0;

// Consulta total de empréstimos
$sql_total = "SELECT COUNT(*) AS total FROM emprestimos";
$result_total = $conn->query($sql_total);

if ($result_total && $result_total->num_rows > 0) {
    $row_total = $result_total->fetch_assoc();
    $total_pages = ceil($row_total["total"] / $results_per_page);
}

// Consulta para listar os empréstimos
$sql = "SELECT id, nome_aluno, id_aluno, nome_livro, id_livro, data_inicio, data_fim, ativo 
        FROM emprestimos ORDER BY $sort_by $order LIMIT $start_from, $results_per_page";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $emprestimos[] = $row;
    }
} else {
    $emprestimos = [];
}

$conn->close();

function calculateInterval($data_fim) {
    $dataAtual = date('Y-m-d');
    $intervalo = strtotime($data_fim) - strtotime($dataAtual);
    return floor($intervalo / (60 * 60 * 24));
}

function getRowClass($row) {
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
        <h1>Lista de Empréstimos</h1>
        <div id="mensagem"></div>
        <table>
            <thead>
                <tr>
                    <th><a href="?sort_by=id&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>">ID</a></th>
                    <th><a href="?sort_by=nome_aluno&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>">Nome do Aluno</a></th>
                    <th>ID do Aluno</th>
                    <th><a href="?sort_by=nome_livro&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>">Nome do Livro</a></th>
                    <th>ID do Livro</th>
                    <th><a href="?sort_by=data_inicio&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>">Data de Início</a></th>
                    <th><a href="?sort_by=data_fim&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>">Data de Fim</a></th>
                    <th><a href="?sort_by=ativo&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>">Ativo</a></th>
                    <th>Ações</th>
                    <th><a href="?sort_by=intervalo&order=<?php echo $order === 'asc' ? 'desc' : 'asc'; ?>">Intervalo</th>
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
                    <form action='./php/finalizar.php?page=" . $page . "' method='post' style='display:inline-block;'>
                        <input type='hidden' name='id' value='" . $row["id"] . "'>
                        <button type='submit' class='finalizar' " . ($row["ativo"] ? "" : "disabled") . ">Finalizar</button>
                    </form>
                </td>";
                $intervalo = calculateInterval($row["data_fim"]);
                echo "<td class='intervalo'>" . $intervalo . " " . ($intervalo === 1 ? "dia" : "dias") . "</td>";
                echo "</tr>";
            }

            if (empty($emprestimos)) {
                echo "<tr><td colspan='10'>Nenhum empréstimo encontrado</td></tr>";
            }
            ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php
            if ($total_pages > 1) {
                if ($page > 1) {
                    echo "<a href='index.php?page=" . ($page - 1) . "&sort_by=$sort_by&order=$order' class='prev'>Anterior</a>";
                }
            
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $page) {
                        echo "<a class='active' href='index.php?page=" . $i . "&sort_by=$sort_by&order=$order'>" . $i . "</a>";
                    } else {
                        echo "<a href='index.php?page=" . $i . "&sort_by=$sort_by&order=$order'>" . $i . "</a>";
                    }
                }
            
                if ($page < $total_pages) {
                    echo "<a href='index.php?page=" . ($page + 1) . "&sort_by=$sort_by&order=$order' class='next'>Próxima</a>";
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
                        row.querySelector('.data-fim').textContent = data.nova_data_fim;
                        row.querySelector('.intervalo').textContent = data.novo_intervalo;
                        row.className = data.classe;
                        alert('Empréstimo estendido com sucesso!');
                    } else {
                        alert('Erro ao estender empréstimo: ' + data.message);
                    }
                })
                .catch(error => console.error('Erro:', error));
            });
        });
    });
</script>
</body>
</html>
