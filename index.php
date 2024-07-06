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

$results_per_page = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page-1) * $results_per_page;

$sql_total = "SELECT COUNT(*) AS total FROM emprestimos";
$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_pages = ceil($row_total["total"] / $results_per_page);

$sql = "SELECT id, nome_aluno, id_aluno, nome_livro, id_livro, data_inicio, data_fim, ativo FROM emprestimos LIMIT $start_from, $results_per_page";
$result = $conn->query($sql);
$emprestimos = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $emprestimos[] = $row;
    }
} else {
    $emprestimos = []; // Garante que $emprestimos seja um array
}

$conn->close();

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
                <a href="cadastro.html">Cadastro</a>
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
                    <th>ID</th>
                    <th>Nome do Aluno</th>
                    <th>ID do Aluno</th>
                    <th>Nome do Livro</th>
                    <th>ID do Livro</th>
                    <th>Data de Início</th>
                    <th>Data de Fim</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                    <th>Intervalo</th> 
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
    echo "<td class='intervalo'></td>"; 
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
                    echo "<a href='index.php?page=".($page-1)."' class='prev'>Anterior</a>";
                }

                for ($i=1; $i<=$total_pages; $i++) {
                    if ($i == $page) {
                        echo "<a class='active' href='index.php?page=".$i."'>".$i."</a>";
                    } else {
                        echo "<a href='index.php?page=".$i."'>".$i."</a>";
                    }
                }

                if ($page < $total_pages) {
                    echo "<a href='index.php?page=".($page+1)."' class='next'>Próximo</a>";
                }
            }
            ?>
        </div>
    </div>
</div>

<script>
    const dataAtual = new Date();
    dataAtual.setUTCHours(0, 0, 0, 0); 

    document.addEventListener('DOMContentLoaded', function() {
        const emprestimos = <?php echo json_encode($emprestimos); ?>;

        emprestimos.forEach(emprestimo => {
            const row = document.getElementById('row-' + emprestimo.id);
            const dataFim = new Date(emprestimo.data_fim);

            const diffTime = dataFim.getTime() - dataAtual.getTime();
            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 

            let intervaloText = diffDays === 1 ? 'dia' : 'dias';

            if (diffDays < 0) {
                row.classList.add('vermelho');
            } else if (diffDays <= 3) {
                row.classList.add('amarelo');
            }

            const intervaloCell = row.querySelector('.intervalo');
            if (intervaloCell) {
                intervaloCell.textContent = (diffDays < 0 ? '' : '') + diffDays + ' ' + intervaloText;
            }
        });

        document.querySelectorAll('.estenderForm').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(form);
                const row = document.getElementById('row-' + formData.get('id'));
                const mensagemDiv = document.getElementById('mensagem');
                const dataFimTd = row.querySelector('.data-fim');

                fetch('./php/estender.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        dataFimTd.textContent = data.nova_data_fim;
                        const dataFim = new Date(data.nova_data_fim);

                        const diffTime = dataFim.getTime() - dataAtual.getTime();
                        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                        let intervaloText = diffDays === 1 ? 'dia' : 'dias';

                        row.classList.remove('vermelho', 'amarelo', 'finalizado');
                        if (diffDays < 0) {
                            row.classList.add('vermelho');
                        } else if (diffDays <= 3) {
                            row.classList.add('amarelo');
                        }

                        const intervaloCell = row.querySelector('.intervalo');
                        if (intervaloCell) {
                            intervaloCell.textContent = (diffDays < 0 ? '' : '') + diffDays + ' ' + intervaloText;
                        }

                        mensagemDiv.innerHTML = '<p class="mensagem-sucesso">Prazo estendido com sucesso!</p>';
                    } else {
                        mensagemDiv.innerHTML = '<p class="mensagem-erro">Erro ao estender o prazo.</p>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao estender prazo:', error);
                    mensagemDiv.innerHTML = '<p class="mensagem-erro">Erro ao estender o prazo.</p>';
                });
            });
        });
    });
</script>
</body>
</html>
