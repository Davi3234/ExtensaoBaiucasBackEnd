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
   * @return self Retorna a própria instância da transação
   */
  function begin(): self;

  /**
   * Reverte todas as operações realizadas desde o início da transação
   * @return self Retorna a própria instância da transação
   */
  function rollback(): self;

  /**
   * Confirma todas as operações realizadas durante a transação
   * @return self Retorna a própria instância da transação
   */
  function commit(): self;

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
   * @return self Retorna a própria instância do checkpoint
   */
  function save(): self;

  /**
   * Libera o ponto de salvamento, confirmando as operações realizadas até o save do checkpoint
   * @return self Retorna a própria instância do checkpoint
   */
  function release(): self;

  /**
   * Reverte as operações até o ponto de salvamento
   * @return self Retorna a própria instância do checkpoint
   */
  function rollback(): self;
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
 * Implementação da interface IDatabaseConnection, gerenciando a conexão com o banco de dados PostgreSQL
 */
class DatabaseConnection implements IDatabaseConnection {

  /**
   * Conexão global com o banco de dados PostgreSQL, utilizada para evitar múltiplas instâncias de conexão
   */
  private static ?PostgresConnection $globalConnection = null;

  /**
   * @var string URL de conexão com o banco de dados
   */
  private readonly string $databaseUrl;

  /**
   * Conexão com o banco de dados PostgreSQL
   */
  protected ?PostgresConnection $connection = null;

  /**
   * @param PostgresConnection|string|null $connection Instância de conexão PostgreSQL ou a String da URL de Conexão com o banco. Caso não informado, será considerado a URL de conexão definida na variável de ambiente `DATABASE_URL`
   */
  function __construct(PostgresConnection|string|null $connection = null);

  /**
   * Returna uma instância da própria classe usando a conexão global com o banco
   * @param ?string $databaseUrl String da URL de Conexão com o banco. Caso não informado, será considerado a URL de conexão definida na variável de ambiente `DATABASE_URL`
   * @return static Instância da classe DatabaseConnection com a conexão global
   */
  static function getGlobalConnection(?string $databaseUrl = null): static;

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