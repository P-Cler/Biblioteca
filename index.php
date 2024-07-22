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

$sql = "SELECT id, nome_aluno, id_aluno, nome_livro, id_livro, data_inicio, data_fim, ativo FROM emprestimos";
$result = $conn->query($sql);
$emprestimos = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $emprestimos[] = $row;
    }
}

// Fechar conexão após obter os dados
$conn->close();

?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Empréstimos</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="images.jpeg" type="image/x-icon">
    <style>

    .amarelo {
        background-color: yellow !important;
    }

    .vermelho {
        background-color: red !important;
    }

    .finalizado {
        background-color: white !important;
       opacity: 0.5;
    }

    </style>
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
    <div class="container">
        <h1>Lista de Empréstimos</h1>
        <div id="mensagem"></div> <!-- Div para exibir mensagens -->
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
                    <th>Intervalo</th> <!-- Nova coluna para exibir o intervalo -->
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
                            <form action='finalizar.php' method='post' style='display:inline-block;'>
                                <input type='hidden' name='id' value='" . $row["id"] . "'>
                                <button type='submit' class='finalizar' " . ($row["ativo"] ? "" : "disabled") . ">Finalizar</button>
                            </form>
                          </td>";
                    echo "<td class='intervalo'></td>"; // Espaço para o intervalo em dias
                    echo "</tr>";
                }                

                if (empty($emprestimos)) {
                    echo "<tr><td colspan='10'>Nenhum empréstimo encontrado</td></tr>";
                }

                function getRowClass($row) {
                    $dataAtual = date('Y-m-d'); // Data atual
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
            </tbody>
        </table>
    </div>
</div>

<script>
    const dataAtual = new Date();
    dataAtual.setUTCHours(0, 0, 0, 0); // Zerar horas para considerar apenas a data atual

    document.addEventListener('DOMContentLoaded', function() {
        const emprestimos = <?php echo json_encode($emprestimos); ?>;

        emprestimos.forEach(emprestimo => {
            const row = document.getElementById('row-' + emprestimo.id);
            const dataFim = new Date(emprestimo.data_fim);

            // Calcular o intervalo em dias
            const diffTime = dataFim.getTime() - dataAtual.getTime();
            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); // Calcular apenas a parte inteira

            // Ajustar intervalo para exibir corretamente números negativos
            let intervaloText = diffDays === 1 ? 'dia' : 'dias';


            if (diffDays < 0) {
                row.classList.add('vermelho');
            } else if (diffDays <= 3) {
                row.classList.add('amarelo');
            }

            // Exibir o intervalo em dias na tabela
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

                fetch('estender.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        dataFimTd.textContent = data.nova_data_fim;
                        const dataFim = new Date(data.nova_data_fim);

                        // Recalcular o intervalo após a extensão
                        const diffTime = dataFim.getTime() - dataAtual.getTime();
                        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); // Calcular apenas a parte inteira
                      
                        // Ajustar intervalo para exibir corretamente números negativos
                        let intervaloText = diffDays === 1 ? 'dia' : 'dias';
                        if (diffDays < 0) {
                            diffDays *= -1; // Transforma em valor positivo para exibir corretamente
                        }

                        let className = '';
                        if (diffDays < 0) {
                            className = 'vermelho';
                        } else if (diffDays <= 3) {
                            className = 'amarelo';
                        }

                        // Atualizar classes e intervalo na linha da tabela
                        
                        if (className) {
                            row.classList.add(className);
                        }
                        row.querySelector('.intervalo').textContent = (diffDays < 0 ? '-' : '') + diffDays + ' ' + intervaloText;

                        mensagemDiv.innerHTML = '<p style="color:green;">' + data.message + '</p>';
                    } else {
                        mensagemDiv.innerHTML = '<p style="color:red;">' + data.message + '</p>';
                    }
                })
                .catch(error => {
                    mensagemDiv.innerHTML = '<p style="color:red;">Erro ao enviar formulário: ' + error.message + '</p>';
                });
            });
        });
    });
</script>


</body>
</html>
