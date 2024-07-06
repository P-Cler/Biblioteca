<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Empréstimo</title>
    <link rel="stylesheet" href="./css/cadastro.css">
    <link rel="icon" href="./favicon/images.jpeg" type="image/x-icon">
</head>
<body>
    <div class="cabecalho">
        <div class="referencias">
            <nav>
                <div class="inic-nav">
                <a href="index.php">Emprestimos</a>
            </div>
                <div class="projet-nav">
                <a href="cadastro.php"  class="pagina-atual">Cadastro</a>
            </div>
            </nav>
        </div>
    </div>
    <div class="bloco">
        <div class="newCad">
            <h1>Adicionar Novo Empréstimo</h1>
            <form id="emprestimoForm">
                <label for="nome_aluno">Nome do Aluno:</label>
                <input type="text" id="nome_aluno" name="nome_aluno" required>
    
                <label for="id_aluno">ID do Aluno:</label>
                <input type="text" id="id_aluno" name="id_aluno" required>
    
                <label for="nome_livro">Nome do Livro:</label>
                <input type="text" id="nome_livro" name="nome_livro" required>
    
                <label for="id_livro">ID do Livro:</label>
                <input type="number" id="id_livro" name="id_livro" required>
    
                <label for="data_inicio">Data de Início:</label>
                <input type="date" id="data_inicio" name="data_inicio" required>
    
                <input type="submit" value="Adicionar Empréstimo">
            </form>
            <div id="mensagem"></div> 
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('emprestimoForm');
            const dataInicio = document.getElementById('data_inicio');
            const mensagemDiv = document.getElementById('mensagem');
            
            const today = new Date();
            const todayFormatted = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
            
            dataInicio.value = todayFormatted;
            dataInicio.max = todayFormatted;
            
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                
                const selectedDate = new Date(dataInicio.value);
                if (selectedDate > today) {
                    mensagemDiv.innerHTML = '<p style="color:red;">A data de início não pode ser no futuro.</p>';
                    return;
                }
                
                const formData = new FormData(form);
                fetch('./php/emprestimo.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
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
    </script>
</body>
        </html>