# Biblioteca - Sistema de Empréstimos

Este é um sistema de gerenciamento de empréstimos para auxiliar as bibliotecas da rede FIRJAN SENAI durante o período de falta de rede. Esta aplicação é simples e foca em trazer um sistema fácil e rápido para gerenciar de maneira prática os empréstimos nesse período. A aplicação roda sem a necessidade de estar conectada à internet, então é só baixar os arquivos e sistemas necessários.

Importante ressaltar que essa aplicação não vai rodar nos computadores da rede, pois eles bloqueiam a execução de arquivos instaladores (nesse caso o XAMPP). Você pode baixar todos os arquivos necessários (arquivos de código e instalador) e então colocá-los em um pen drive para executar em notebooks da sua unidade.

## Requisitos

- XAMPP
- Arquivos da aplicação
- Permissão para baixar

## Instalação

1. Baixe os arquivos no seu sistema:

[Download do arquivo ZIP](https://github.com/P-Cler/Biblioteca/archive/refs/tags/1.0.zip)

2.1. Descompacte o arquivo e altere o nome para bilioteca

2. Baixe o XAMPP:

    [Link para download do XAMPP](https://www.apachefriends.org/index.html)

2.1. Execute o arquivo de instalação do XAMPP:

2.2. Siga as instruções de instalação, utilizando a configuração padrão do XAMPP:

3. Configure o banco de dados:

3.1. Inicie os serviços Apache e MySQL no XAMPP:

![Serviços XAMPP](https://github.com/P-Cler/Biblioteca/assets/156240431/da1985aa-debb-4a03-a548-2dfe317bb29e)
)

3.2. Acesse o phpMyAdmin no endereço [http://localhost/phpmyadmin](http://localhost/phpmyadmin).

3.3. Crie um banco de dados com o nome `biblioteca`:

(![banco](https://github.com/P-Cler/Biblioteca/assets/156240431/a4cc5055-3f92-41f7-ad05-6d3d576d0ada)
)


3.4. Criar a tabela emprestimos:


Entre na guia especificada na imagem a seguir
![cadastro](https://github.com/P-Cler/Biblioteca/assets/156240431/4348427c-6c21-492a-89e1-5346a5ab4be6)

dentro dessa parte copie o código a seguir e cole no espaço em branco

    CREATE TABLE emprestimos (
    id INT AUTO_INCREMENT PRIMARY KEY, -- ID único para cada empréstimo
    nome_aluno VARCHAR(255) NOT NULL, -- Nome do aluno
    id_aluno VARCHAR(255) NOT NULL, -- ID do aluno
    nome_livro VARCHAR(255) NOT NULL, -- Nome do livro
	id_livro VARCHAR(255) NOT NULL, -- ID do aluno
    data_inicio DATE NOT NULL, -- Data do início do empréstimo
    data_fim DATE NOT NULL, -- Data do fim do empréstimo (15 dias após o início)
    ativo BOOLEAN NOT NULL -- Se o empréstimo está ativo ou não
    );


  deve ficar do seguinte jeito:

![emprestimo](https://github.com/P-Cler/Biblioteca/assets/156240431/96631793-c566-49d4-b2ac-5c267c5407b0)

então clique em executar

5. Coloque a pasta dos arquivos no diretório `htdocs` do XAMPP:

    Selecione a pasta com os arquivos da aplicação que você baixou e os cole dentro da pasta htdocs no diretorio do xampp.

6. Acesse o projeto no navegador:

    ```sh
    http://localhost/biblioteca/index.php
    ```

## Estrutura do Projeto

- `index.php`: Página principal que lista os empréstimos.
- `cadastro.php`: Página para cadastrar novos empréstimos.
- `css/`: Diretório com os arquivos CSS para estilização.
- `php/`: Diretório com os arquivos PHP para manipulação de dados.

## Funcionalidades

- Cadastro de novos empréstimos.
- Listagem de empréstimos.
- Extensão de empréstimos existentes.
- Indicação visual de empréstimos próximos ao vencimento ou vencidos.

## Como Usar

1. Acesse a página de cadastro para adicionar novos empréstimos.
2. Na página principal, visualize a lista de empréstimos.
3. Utilize os botões de "Estender" e "Finalizar" para gerenciar os empréstimos.

## Contribuições

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues e enviar pull requests.

## Contato

Para dúvidas ou sugestões, entre em contato através de [pedro.clerl@estudante.firjan.senai.br].
