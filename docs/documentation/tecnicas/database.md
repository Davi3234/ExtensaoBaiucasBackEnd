# Database

O Provider `Database` fornece uma camada de abstração para a comunicação com o banco de dados

## Interfaces

### `IDatabaseConnection`

Esta interface fornece uma interface simples de uma conexão com o banco, não possuindo a responsabilidade das operações realizadas com o banco, mas sim com a própria conexão em si com o banco

```php
namespace App\Provider\Database\Interface;

interface IDatabaseConnection {

  /**
   * Estabelece a conexão com o banco de dados
   * @return void
   */
  function connect();

  /**
   * Fecha a conexão com o banco de dados
   * @return void
   */
  function close();

  /**
   * Retorna uma mensagem de erro da última operação de banco de dados
   * @return string Mensagem de erro da última operação realizada
   */
  function getError(): string;

  /**
   * Retorna o status atual da conexão banco de dados conectado
   * @return bool Status atual da conexão com o banco de dados
   */
  function status(): bool;
}
```

### `IDatabase`

Esta interface é uma extensão da interface de conexão com o banco ([`IDatabaseConnection`](#idatabaseconnection)), onde este por sua vez é responsável pelas operações e transações com o banco

```php
namespace App\Provider\Database\Interface;

use App\Provider\Sql\Builder\SQLBuilder;

interface IDatabase extends IDatabaseConnection {

  /**
   * Executa uma instrução SQL direta.
   *
   * Executa a consulta SQL fornecida como string e, opcionalmente, recebe parâmetros para substituição. 
   * Retorna o resultado como array ou `false` em caso de erro.
   *
   * @param string $sql String contendo a instrução SQL a ser executada.
   * @param array $params Parâmetros opcionais para a execução do SQL.
   * @return array|bool Retornará `true` caso tenha dado sucesso ou um array com o resultado obtido
   */
  function exec(string $sql, $params = []): array|bool;

  /**
   * Executa uma consulta SELECT direta.
   *
   * Executa a consulta SELECT fornecida como string, utilizando parâmetros opcionais para substituição,
   * e retorna os resultados como um array.
   *
   * @param string $sql String contendo a consulta SELECT
   * @param array $params Parâmetros opcionais para a consulta
   * @return array Resultado da consulta como um array de dados
   */
  function query(string $sql, $params = []): array;

  /**
   * Executa um comando SQL a partir de um SQLBuilder.
   *
   * Este método utiliza uma instância de SQLBuilder para gerar uma consulta SQL. 
   * Ele executa a consulta e retorna o resultado como array, ou `false` em caso de erro.
   *
   * @param SQLBuilder $sqlBuilder Instância de SQLBuilder que cria a consulta SQL
   * @return array|bool Retornará `true` caso tenha dado sucesso ou um array com o resultado obtido
   */
  function execFromSqlBuilder(SQLBuilder $sqlBuilder): array|bool;

  /**
   * Executa uma consulta SELECT a partir de um SQLBuilder.
   *
   * Este método utiliza um SQLBuilder para construir e executar uma consulta do tipo SELECT,
   * retornando os resultados como um array.
   *
   * @param SQLBuilder $sqlBuilder Instância de SQLBuilder que gera a consulta SELECT
   * @return array Resultado da consulta como um array de dados
   */
  function queryFromSqlBuilder(SQLBuilder $sqlBuilder): array;

  /**
   * Cria uma instância da transação da conexão atual com o banco sem iniciar o bloco de transação (BEGIN)
   * @return ITransaction Instância de transação
   */
  function transaction(): ITransaction;

  /**
   * Cria uma instância da transação da conexão atual com o banco com o bloco de transação já iniciado (BEGIN)
   * @return ITransaction Instância de transação
   */
  function begin(): ITransaction;
}
```

### `ITransaction`

Interface responsável pela transação do banco de dados, fornecendo métodos de manipulação da transação. Para mais sobre Transação com o banco PostgreSQL, acesse [aqui](https://www.postgresql.org/docs/current/tutorial-transactions.html)

```php
namespace App\Provider\Database\Interface;

interface ITransaction {

  /**
   * Inicia uma nova transação
   * @return static Retorna a própria instância da transação
   */
  function begin(): static;

  /**
   * Reverte todas as operações realizadas desde o início da transação
   * @return static Retorna a própria instância da transação
   */
  function rollback(): static;

  /**
   * Confirma todas as operações realizadas durante a transação
   * @return static Retorna a própria instância da transação
   */
  function commit(): static;

  /**
   * Cria uma instância de checkpoint da transação da conexão atual com o banco sem iniciar o save do checkpoint (SAVE)
   * @return ITransactionCheckpoint Instância de um checkpoint na transação
   */
  function checkpoint(): ITransactionCheckpoint;

  /**
   * Cria uma instância de checkpoint da transação da conexão atual com o banco com o save do checkpoint já iniciado (SAVE)
   * @return ITransactionCheckpoint Instância de um checkpoint na transação
   */
  function save(): ITransactionCheckpoint;
}
```

### `ITransactionCheckpoint`

Interface responsável pelo checkpoint do bloco de transação do banco de dados, fornecendo

```php
namespace App\Provider\Database\Interface;

interface ITransactionCheckpoint {

  /**
   * Salva o estado atual da transação no checkpoint
   * @return static Retorna a própria instância do checkpoint
   */
  function save(): static;

  /**
   * Libera o ponto de salvamento, confirmando as operações realizadas até o save do checkpoint
   * @return static Retorna a própria instância do checkpoint
   */
  function release(): static;

  /**
   * Reverte as operações até o ponto de salvamento
   * @return static Retorna a própria instância do checkpoint
   */
  function rollback(): static;
}
```

## Da classe `DatabaseConnection`

A classe `DatabaseConnection` implementa a classe `IDatabaseConnection`, implementando os métodos usando os recursos do [PostgreSQL Functions](https://www.php.net/manual/en/book.pgsql.php)

API da classe `DatabaseConnection`:
```php
namespace App\Provider\Database;

use App\Provider\Database\Interface\IDatabaseConnection;
use PgSql\Connection as PostgresConnection;

/**
 * Classe DatabaseConnection que implementa da interface IDatabaseConnection, gerenciando a conexão com o banco de dados PostgreSQL
 */
class DatabaseConnection implements IDatabaseConnection {

  /**
   * @param PostgresConnection|string|null $connection Instância de conexão PostgreSQL ou a String da URL de Conexão com o banco. Caso não informado, será considerado a URL de conexão definida na variável de ambiente `DATABASE_URL`
   */
  function __construct(PostgresConnection|string|null $connection = null);

  /**
   * Returna uma instância da própria classe usando a conexão global com o banco
   * @param PostgresConnection|string|null $connection String da URL de Conexão com o banco ou a própria conexão nativa com o banco. Caso não informado, será considerado a URL de conexão definida na variável de ambiente `DATABASE_URL` para fazer a conexão. Se a conexão global já tiver sido estabelecida, este parâmetro será ignorado
   * @return static Instância da classe DatabaseConnection com a conexão global
   */
  static function getGlobalConnection(PostgresConnection|string|null $connection = null): static;

  /**
   * Retorna uma instância da própria classe e já realiza a conexão com o banco de dados
   * @param ?string $databaseUrl URL de Conexão com o banco de dados. Caso não definida, irá considerar da varável env `DATABASE_URL`
   * @return static Instância da própria classe com uma nova conexão
   */
  static function newConnection(?string $databaseUrl = null): static;

  /**
   * Cria uma instância da própria classe a partir de uma conexão PostgreSQL já existente
   * @param PostgresConnection $connection Conexão PostgreSQL a ser usada na nova instância
   * @return static Nova instância da própria classe utilizando a conexão fornecida
   */
  static function fromConnection(PostgresConnection $connection): static;

  /**
   * Função que cria uma conexão com o banco de dados
   * @param string $databaseUrl URL de conexão com o banco de dados
   * @return PostgresConnection Conexão com o banco de dados PostgreSQL
   */
  static function newPostgresConnection(string $databaseUrl): PostgresConnection;

  /**
   * Retorna a conexão atual com o banco de dados PostgreSQL
   * @return PostgresConnection Conexão PostgreSQL atual
   */
  function getConnection(): PostgresConnection;

  /* Demais métodos implementados conforme a interface IDatabaseConnection... */
}
```

### Formas de criar uma nova conexão

- Criando uma instância de `DatabaseConnection` e realizando a conexão manualmente:
  - Forma 1:
    ```php
    use App\Provider\Database\DatabaseConnection;

    $databaseUrl = 'dbname=example';

    $database = new DatabaseConnection($databaseUrl);
    $database->connect();
    ```
  - Forma 2 (Alias para a Forma 1):
    ```php
    use App\Provider\Database\DatabaseConnection;

    $databaseUrl = 'dbname=example';

    /**
     * Simplifica o uso do new e da chamada ao método connect
    */
    $database = DatabaseConnection::newConnection($databaseUrl);
    ```

- Caso já tenha uma conexão prévia com o banco de dados usando o `pg_connect` nativo e apenas queira importá-la para a classe `DatabaseConnection`, basta usar o método `fromConnection`:
  - Forma 1:
    ```php
    use App\Provider\Database\DatabaseConnection;

    $databaseUrl = 'dbname=example';

    $connection = pg_connect($databaseUrl);

    $database = new DatabaseConnection($connection);
    ```
  - Forma 2:
    ```php
    use App\Provider\Database\DatabaseConnection;

    $databaseUrl = 'dbname=example';

    $connection = pg_connect($databaseUrl);

    $database = DatabaseConnection::fromConnection($connection);
    ```
- Para criar uma simples conexão nativa com PostgreSQL, pode-se usar o método `newPostgresConnection`, este retornará uma instância de `\PgSql\Connection`:
  ```php
  use App\Provider\Database\DatabaseConnection;

  $databaseUrl = 'dbname=example';

  // Conexão global com o banco
  $connection = DatabaseConnection::newPostgresConnection($databaseUrl);
  ```

- Caso queira trabalhar com [**singleton**](https://imasters.com.br/back-end/o-padrao-singleton-com-php), utiliza-se o método `getGlobalConnection`, que irá retornar (ou criar, caso ainda não esteja criado) uma instância de `DatabaseConnection` com o link de conexão global:
  - Forma 1:
    ```php
    use App\Provider\Database\DatabaseConnection;

    $databaseUrl = 'dbname=example';

    // Conexão global com o banco
    $database = DatabaseConnection::getGlobalConnection($databaseUrl);
    ```
  - Forma 2:
    ```php
    use App\Provider\Database\DatabaseConnection;

    $databaseUrl = 'dbname=example';

    $connection = pg_connect($databaseUrl);

    // Conexão global com o banco
    $database = DatabaseConnection::getGlobalConnection($connection);
    ```

O parâmetro da URL do banco de dados para os métodos usados acima é opcional, sendo possível não informar a URL de conexão com o banco. Assim, por baixo dos panos, ele irá considerar a URL de conexão com o banco definida na variável de ambiente `DATABASE_URL`;

## Da classe `Database`

A classe `Database` implementa a classe `IDatabase`, implementando os métodos para realizar as operações de transação, como de execução e consulta

```php
namespace App\Provider\Database;

use App\Provider\Database\Interface\IDatabase;
use App\Provider\Sql\Builder\SQLBuilder;
use App\Exception\Exception;

/**
 * Classe Database que implementa de operações de banco de dados baseadas em SQL, utilizando a conexão PostgreSQL
 * Extende a classe DatabaseConnection e implementa a interface IDatabase
 */
class Database extends DatabaseConnection implements IDatabase {

  /**
   * Envia uma operação SQL para o PostgreSQL utilizando parâmetros
   * @param string $sql Instrução SQL a ser executada
   * @param array $params Parâmetros a serem substituídos na consulta
   * @return array Resultado da operação como array
   */
  private function sendPgQueryParam($sql, $params = []): array;

  /* Demais métodos implementados conforme a interface IDatabase... */
}
```

## Da classe `Transaction`

A classe `Transaction` implementa a classe `ITransaction`, responsável por gerenciar as transações do banco de dados

```php
namespace App\Provider\Database;

use App\Provider\Database\Interface\IDatabase;
use App\Provider\Database\Interface\ITransaction;

/**
 * Classe Transaction que implementa a interface ITransaction, gerenciando transações de banco de dados
 */
class Transaction implements ITransaction {

  function __construct(IDatabase $database);

  /**
   * Cria uma nova instância de Transaction a partir de uma conexão de banco de dados
   *
   * @param IDatabase $connection Instância de conexão de banco de dados
   * @return static Nova instância de Transaction associada à conexão fornecida
   */
  static function fromDatabase(IDatabase $connection): static;

  /* Demais métodos implementados conforme a interface IDatabase... */
}
```