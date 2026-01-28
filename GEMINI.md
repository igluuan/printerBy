# GEMINI.md

## Visão Geral do Projeto

Este projeto é uma aplicação web para gerenciar uma frota de impressoras. É escrito em PHP e utiliza um banco de dados MySQL. A aplicação permite aos usuários realizar operações CRUD (Criar, Ler, Atualizar, Excluir) em impressoras e suas peças associadas.

As principais características da aplicação incluem:

*   **Listagem de Impressoras:** Uma página principal que lista todas as impressoras com opções de filtragem, ordenação e paginação.
*   **Operações CRUD:** Funcionalidade para adicionar, editar e excluir impressoras.
*   **Rastreamento de Peças:** Um sistema para rastrear peças que foram removidas de impressoras.
*   **UI Dinâmica:** A interface do usuário é dinâmica, com recursos como dropdowns dependentes para marca e modelo da impressora.

## Construção e Execução

Para executar este projeto, você precisará de um servidor web com PHP e um banco de dados MySQL.

1.  **Configuração do Banco de Dados:**
    *   Crie um banco de dados chamado `impressoras_db`.
    *   Importe o arquivo `impressoras_db.sql` para configurar as tabelas necessárias.

2.  **Configuração:**
    *   Crie um arquivo `config/database.php` com o seguinte conteúdo, substituindo os espaços reservados pelas suas credenciais de banco de dados:

    ```php
    <?php
    class Database {
        private static $instance = null;
        private function __construct() {}
        private function __clone() {}
        public static function getInstance() {
            if (self::$instance === null) {
                $db_host = 'localhost';
                $db_name = 'impressoras_db';
                $db_user = 'YOUR_DB_USER';
                $db_pass = 'YOUR_DB_PASSWORD';
                try {
                    self::$instance = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    // Em um ambiente de produção, logue o erro em vez de exibi-lo
                    error_log('Connection Error: ' . $e->getMessage());
                    // Lançar uma exceção mais genérica para o usuário
                    throw new Exception("Não foi possível conectar ao banco de dados. Por favor, tente novamente mais tarde.");
                }
            }
            return self::$instance;
        }
    }
    ```

3.  **Executando a Aplicação:**
    *   Coloque os arquivos do projeto na raiz do documento do seu servidor web.
    *   Acesse o arquivo `index.php` no seu navegador.
    *   O script `start.sh` incluído pode ser usado para executar a aplicação usando o servidor web PHP embutido:
    ```bash
    ./start.sh
    ```

## Convenções de Desenvolvimento

*   **Estrutura de Arquivos:** O projeto é organizado com arquivos separados para diferentes funcionalidades (por exemplo, `cadastrar.php`, `editar.php`, `deletar.php`).
*   **Interação com o Banco de Dados:** As consultas ao banco de dados são tratadas usando PDO (PHP Data Objects).
*   **UI:** A interface do usuário é construída com Bootstrap e inclui recursos dinâmicos usando JavaScript.
*   **Tratamento de Erros:** A aplicação inclui tratamento de erros para capturar e exibir erros de conexão com o banco de dados e outras exceções.
*   **Estilo de Código:** O código é bem comentado, com cabeçalhos em cada arquivo explicando o propósito e a funcionalidade.