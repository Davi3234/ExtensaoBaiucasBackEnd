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

  /**
   * Retorna um boolean indicando se a transação está ativa ou não
   * @return bool Status da transação
   */
  function isActive(): bool;
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

### Utilização dos métodos

- Criando uma instância de `DatabaseConnection` e realizando a conexão manualmente
  - Forma 1
    ```php
    use App\Provider\Database\DatabaseConnection;

    $databaseUrl = 'dbname=example';

    $databaseConnection = new DatabaseConnection($databaseUrl);
    $databaseConnection->connect();
    ```

  - Forma 2 (Alias para a Forma 1)
    ```php
    use App\Provider\Database\DatabaseConnection;

    $databaseUrl = 'dbname=example';

    /**
     * Simplifica o uso do new e da chamada ao método connect
    */
    $databaseConnection = DatabaseConnection::newConnection($databaseUrl);
    ```

- Caso já tenha uma conexão prévia com o banco de dados usando o `pg_connect` nativo e apenas queira importá-la para a classe `DatabaseConnection`, basta usar o método `fromConnection`
  - Forma 1
    ```php
    use App\Provider\Database\DatabaseConnection;

    $databaseUrl = 'dbname=example';

    $connection = pg_connect($databaseUrl);

    $databaseConnection = new DatabaseConnection($connection);
    ```

  - Forma 2
    ```php
    use App\Provider\Database\DatabaseConnection;

    $databaseUrl = 'dbname=example';

    $connection = pg_connect($databaseUrl);

    $databaseConnection = DatabaseConnection::fromConnection($connection);
    ```

- Para criar uma simples conexão nativa com PostgreSQL, pode-se usar o método `newPostgresConnection`, este retornará uma instância de `\PgSql\Connection`
  ```php
  use App\Provider\Database\DatabaseConnection;

  $databaseUrl = 'dbname=example';

  // Conexão global com o banco
  $connection = DatabaseConnection::newPostgresConnection($databaseUrl);
  ```

- Caso queira trabalhar com [**singleton**](https://imasters.com.br/back-end/o-padrao-singleton-com-php), utiliza-se o método `getGlobalConnection`, que irá retornar (ou criar, caso ainda não esteja criado) uma instância de `DatabaseConnection` com o link de conexão global
  - Forma 1
    ```php
    use App\Provider\Database\DatabaseConnection;

    $databaseUrl = 'dbname=example';

    // Conexão global com o banco
    $databaseConnection = DatabaseConnection::getGlobalConnection($databaseUrl);
    ```

  - Forma 2
    ```php
    use App\Provider\Database\DatabaseConnection;

    $databaseUrl = 'dbname=example';

    $connection = pg_connect($databaseUrl);

    // Conexão global com o banco
    $databaseConnection = DatabaseConnection::getGlobalConnection($connection);
    ```

O parâmetro da URL do banco de dados para os métodos usados nos exemplos acima é opcional para todos, sendo possível não informar a URL de conexão com o banco. Assim, por baixo dos panos, ele irá considerar a URL de conexão com o banco definida na variável de ambiente `DATABASE_URL`

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
  #[\Override]
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
  #[\Override]
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
  #[\Override]
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
  #[\Override]
  function queryFromSqlBuilder(SQLBuilder $sqlBuilder): array;

  /**
   * Cria uma instância da transação da conexão atual com o banco sem iniciar o bloco de transação (BEGIN)
   * @return ITransaction Instância de transação
   */
  #[\Override]
  function transaction(): ITransaction;

  /**
   * Cria uma instância da transação da conexão atual com o banco com o bloco de transação já iniciado (BEGIN)
   * @return ITransaction Instância de transação
   */
  #[\Override]
  function begin(): ITransaction;
}
```

Como a classe `Database` extende a classe `DatabaseConnection`, é possível usar os mesmos métodos para manipular a conexão com o banco assim como no `DatabaseConnection`, já que nela é aplicado o conceito de [**Late Static Bindings**](https://www.php.net/manual/en/language.oop5.late-static-bindings.php)

```php
$databaseUrl = 'dbname=example';

$database = new Database($databaseUrl);
$database->connect();

$database = Database::newConnection($databaseUrl);

$connection = Database::newPostgresConnection($databaseUrl);

$database = Database::fromConnection($connection);

$database = Database::getGlobalConnection($connection);
```

### Utilização dos métodos

Para prevenção de SQL Injection, será usado templates SQL e parâmetros separadamente, onde no template, no local onde seria o próprio valor que está recebendo externamente, será colocado um `?` ou `$1` (`$1`, `$2`, `$3`, ...) para indicar que ali espera-se um parâmetro

- O envia de operações de **INSERT**, **UPDATE** e **DELETE** são muito parecidas. Exemplo com a operação de **INSERT**
  ```php
  $name = $_POST['name'];
  $login = $_POST['login'];

  try {
    $database = Database::getGlobalConnection();

    $sql = "INSERT INTO users (name, login) VALUES ($1, $2)";
    $params = [$name, $login];

    $database->exec($sql, $params);
  } catch(DatabaseException $err) {
    echo $err->getMessage();
  }
  ```

  - Para cada operação, é sempre possível retornar os registros que foram inseridos/atualizados/deletados utilizando o [**RETURNING**](https://www.postgresql.org/docs/current/dml-returning.html)
    ```php
    $name = 'John Doe';
    $login = 'john.doe@example.com';

    try {
      $database = Database::getGlobalConnection();

      $sql = "INSERT INTO users (name, login) VALUES ($1, $2) RETURNING *";
      $params = [$name, $login];

      $rowAffected = $database->exec($sql, $params);

      var_dump($rowAffected[0]);
      // ['id' => 1, 'name' => 'John Doe', 'login' => 'john.doe@example.com']
    } catch(DatabaseException $err) {
      echo $err->getMessage();
    }
    ```

- Realizando consultas
  ```php
  $id = 1;

  try {
    $database = Database::getGlobalConnection();

    $sql = "SELECT * FROM users WHERE id = $1";
    $params = [$id];

    $rows = $database->query($sql, $params);

    var_dump($rows[0]);
    // ['id' => 1, 'name' => 'John Doe', 'login' => 'john.doe@example.com']
  } catch(DatabaseException $err) {
    echo $err->getMessage();
  }
  ```

- Realizando as operações com [`SQL Builder`](sql-builder.md). Ele constrói o SQL já separando os parâmetros e preparando a String do SQL usando o `$1` (`$1`, `$2`, ...)
  - Exemplo com **INSERT**
    ```php
    use App\Provider\Sql\SQL;

    $name = 'John Doe';
    $login = 'john.doe@example.com';

    try {
      $database = Database::getGlobalConnection();

      $rowAffected = $database->execFromSqlBuilder(
        SQL::insertInto('users')
        ->params('name', 'login')
        ->values([
          'name' => $name,
          'login' => $login,
        ])
        ->returning('*')
      );

      var_dump($rowAffected[0]);
      // ['id' => 1, 'name' => 'John Doe', 'login' => 'john.doe@example.com']
    } catch(DatabaseException $err) {
      echo $err->getMessage();
    }
    ```

  - Exemplo de consulta
    ```php
    use App\Provider\Sql\SQL;

    $id = 1;

    try {
      $database = Database::getGlobalConnection();

      $rowAffected = $database->queryFromSqlBuilder(
        SQL::select()
          ->from('users')
          ->where([
            SQL::eq('id', $id)
          ])
          ->limit(1)
      );

      var_dump($rowAffected[0]);
      // ['id' => 1, 'name' => 'John Doe', 'login' => 'john.doe@example.com']
    } catch(DatabaseException $err) {
      echo $err->getMessage();
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

  /**
   * Inicia uma nova transação
   * @return static Retorna a própria instância da transação
   */
  #[\Override]
  function begin(): static;

  /**
   * Reverte todas as operações realizadas desde o início da transação
   * @return static Retorna a própria instância da transação
   */
  #[\Override]
  function rollback(): static;

  /**
   * Confirma todas as operações realizadas durante a transação
   * @return static Retorna a própria instância da transação
   */
  #[\Override]
  function commit(): static;

  /**
   * Cria uma instância de checkpoint da transação da conexão atual com o banco sem iniciar o save do checkpoint (SAVE)
   * @return ITransactionCheckpoint Instância de um checkpoint na transação
   */
  #[\Override]
  function checkpoint(): ITransactionCheckpoint;

  /**
   * Cria uma instância de checkpoint da transação da conexão atual com o banco com o save do checkpoint já iniciado (SAVE)
   * @return ITransactionCheckpoint Instância de um checkpoint na transação
   */
  #[\Override]
  function save(): ITransactionCheckpoint;

  /**
   * Retorna um boolean indicando se a transação está ativa ou não
   * @return bool Status da transação
   */
  #[\Override]
  function isActive(): bool;
}
```

### Utilização dos métodos

- Criando uma transação
  - A implementação da classe `Transaction` foi feita para que seja independente do banco, portanto, deve-se injetar a instância de um `IDatabase` nela desta forma:
    ```php
    use App\Provider\Database\Database;
    use App\Provider\Database\Transaction;

    $database = Database::getGlobalConnection();

    $transaction = new Transaction($database);
    ```

  - Outra forma é a pela própria classe `IDatabase`. Este método simplifica a necessidade de usar o `new` para instanciar a transação, instanciando e injetando nela a própria instância do banco
    ```php
    use App\Provider\Database\Database;

    $database = Database::getGlobalConnection();

    $transaction = $database->transaction();
    ```

- Iniciando um bloco de transação
  - Após ter a instância da transação, é possível chamar o método begin da mesma
    ```php
    $database = Database::getGlobalConnection();

    $transaction = $database->transaction();
    $transaction->begin();
    ```

  - Outra forma seria usar o método begin do própria banco de dados, assim, além de criar a instância do banco e injetar nela a instância do próprio banco, ele já inicializa o bloco de transação, retornando a instância da transação
    ```php
    $database = Database::getGlobalConnection();

    $transaction = $database->begin();
    ```

- Concluindo o bloco de transação
  - Após realizar as operações com o banco, é possível concluir/reverter a transação usando o `commit` para efetivá-la ou `rollback` para reverte-la
    ```php
    $database = Database::getGlobalConnection();

    $transaction = $database->transaction();

    try {
      $transaction->begin(); // Iniciando a transação

      $database->exec('UPDATE users SET active = FALSE WHERE id = ?', [1]);

      $transaction->commit(); // Efetivando as operações
    } catch(DatabaseException $err) {
      $transaction->rollback(); // Descartando as operações

      echo $err->getMessage();
    }
    ```

## Da classe `TransactionCheckpoint`

A classe `TransactionCheckpoint` implementa a classe `ITransactionCheckpoint` server para gerir os Checkpoints de uma transação. Para saber mais sobre **Checkpoints**, acesse [aqui](https://www.postgresql.org/docs/current/sql-checkpoint.html)

```php
namespace App\Provider\Database;

use App\Provider\Database\Interface\IDatabase;
use App\Provider\Database\Interface\ITransactionCheckpoint;

class TransactionCheckpoint implements ITransactionCheckpoint {

  function __construct(IDatabase $database);

  /**
   * Cria uma nova instância de TransactionCheckpoint a partir de uma conexão de banco de dados
   *
   * @param IDatabase $connection Instância de conexão de banco de dados
   * @return static Nova instância de TransactionCheckpoint associada à conexão fornecida
   */
  static function fromDatabase(IDatabase $connection): static;

  /**
   * Salva o estado atual da transação no checkpoint
   * @return static Retorna a própria instância do checkpoint
   */
  #[\Override]
  function save(): static;

  /**
   * Libera o ponto de salvamento, confirmando as operações realizadas até o save do checkpoint
   * @return static Retorna a própria instância do checkpoint
   */
  #[\Override]
  function release(): static;

  /**
   * Reverte as operações até o ponto de salvamento
   * @return static Retorna a própria instância do checkpoint
   */
  #[\Override]
  function rollback(): static;
}
```

- Para criar um checkpoint de uma transação, utiliza-se o método `checkpoint` da classe `Transaction`
  ```php
  use App\Provider\Database\Database;

  $database = Database::getGlobalConnection();

  $transaction = $database->transaction();

  $checkpoint = $transaction->checkpoint();
  ```

- Iniciando um checkpoint
  - Para iniciar o checkpoint, usa-se o método `save`
    ```php
    use App\Provider\Database\Database;

    $database = Database::getGlobalConnection();

    // Iniciando uma transação
    $transaction = $database->begin();

    $checkpoint = $transaction->checkpoint();
    $checkpoint->save(); // O save deve ocorrer após a inicialização do escopo de transação
    ```

  - Da mesma forma que a transação, é possível simplificar isso chamando diretamente o método `save` da transação
    ```php
    use App\Provider\Database\Database;

    $database = Database::getGlobalConnection();

    $transaction = $database->begin();

    $checkpoint = $transaction->save();
    ```

- Concluindo o bloco do checkpoint
  - Após realizar as operações com o banco, é possível concluir/reverter o checkpoint usando o `release` para liberar o checkpoint, efetivando o mesmo, ou rollback para reverte-la
    ```php
    use App\Provider\Database\Database;

    $database = Database::getGlobalConnection();

    $transaction = $database->transaction();

    try {
      $transaction->begin(); // Iniciando a transação

      $checkpoint = $transaction->checkpoint();

      $user = $database->query('SELECT id FROM users WHERE active = TRUE LIMIT 1')[0];

      try {
        $checkpoint->save(); // Iniciando o checkpoint

        $database->exec('UPDATE users SET active = FALSE WHERE id = ?', [$user['id']]);

        $checkpoint->release(); // Liberando o checkpoint para efetivá-lo
      } catch(DatabaseException $err) {
        $checkpoint->rollback(); // Revertendo as operações do checkpoint

        echo $err->getMessage();
      }

      $transaction->commit(); // Efetivando todas as operações feitas dentro de checkpoints liberados (released)
    } catch(DatabaseException $err) {
      $transaction->rollback(); // Revertendo todas as operações da transação

      echo $err->getMessage();
    }
    ```