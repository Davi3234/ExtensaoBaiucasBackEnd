<?php

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
  function __construct(PostgresConnection|string|null $connection = null) {
    $databaseUrl = env('DATABASE_URL');
    $postgresConnection = null;

    if (is_string($connection)) {
      $databaseUrl = $connection;
    } else if ($connection instanceof PostgresConnection) {
      $postgresConnection = $connection;
    }

    $this->databaseUrl = $databaseUrl;
    $this->connection = $postgresConnection;
  }

  /**
   * Returna uma instância da própria classe usando a conexão global com o banco
   * @param PostgresConnection|string|null $connection String da URL de Conexão com o banco ou a própria conexão nativa com o banco. Caso não informado, será considerado a URL de conexão definida na variável de ambiente `DATABASE_URL` para fazer a conexão. Se a conexão global já tiver sido estabelecida, este parâmetro será ignorado
   * @return static Instância da classe DatabaseConnection com a conexão global
   */
  static function getGlobalConnection(PostgresConnection|string|null $connection = null): static {
    if (!$connection) {
      $connection = env('DATABASE_URL');
    }

    if (static::$globalConnection == null) {
      if (is_string($connection)) {
        $connection = static::newPostgresConnection($connection);
      }

      static::$globalConnection = $connection;
    }

    return static::fromConnection(static::$globalConnection);
  }

  /**
   * Retorna uma instância da própria classe e já realiza a conexão com o banco de dados
   * @param ?string $databaseUrl URL de Conexão com o banco de dados. Caso não definida, irá considerar da varável env `DATABASE_URL`
   * @return static Instância da própria classe com uma nova conexão
   */
  static function newConnection(?string $databaseUrl = null): static {
    $database = new static($databaseUrl);
    $database->connect();

    return $database;
  }

  /**
   * Cria uma instância da própria classe a partir de uma conexão PostgreSQL já existente
   * @param PostgresConnection $connection Conexão PostgreSQL a ser usada na nova instância
   * @return static Nova instância da própria classe utilizando a conexão fornecida
   */
  static function fromConnection(PostgresConnection $connection): static {
    return new static($connection);
  }

  /**
   * Função que cria uma conexão com o banco de dados
   * @param string $databaseUrl URL de conexão com o banco de dados
   * @return PostgresConnection Conexão com o banco de dados PostgreSQL
   */
  static function newPostgresConnection(string $databaseUrl): PostgresConnection {
    $connection = @pg_connect($databaseUrl);

    if ($connection === false)
      throw new DatabaseException('Failed to connect to the database. Error: "' . error_get_last()['message'] . '"');

    return $connection;
  }

  #[\Override]
  function connect() {
    $this->connection = static::newPostgresConnection($this->databaseUrl);
  }

  #[\Override]
  function close() {
    pg_close($this->connection);
  }

  #[\Override]
  function getError(): string {
    return pg_last_error($this->connection);
  }

  /**
   * Retorna a conexão atual com o banco de dados PostgreSQL
   * @return PostgresConnection Conexão PostgreSQL atual
   */
  function getConnection(): PostgresConnection {
    return $this->connection;
  }

  /**
   * Retorna o status atual da conexão banco de dados conectado
   * @return bool Status atual da conexão com o banco de dados
   */
  #[\Override]
  function status(): bool {
    return pg_ping($this->connection);
  }
}
