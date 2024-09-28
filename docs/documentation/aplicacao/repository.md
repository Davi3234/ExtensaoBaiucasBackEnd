# Criando um Repository

O Repositório é uma camada intermediária que fornece uma abstração e desacoplamento entre a camada de aplicação (serviços) e a camada de persistência dos dados. Sua única responsabilidade é acessar e manipular os dados de uma forma clara e organizada, mantendo o código flexível e mais fácil de manter

## Da Classe `Repository`

A classe `Repository` é a classe base para a comunicação e tratamento dos dados das consultas que serão enviados para o banco. Como se trata de uma class abstrata, deve-se criar uma classe que irá extender esta, normalmente referente à um model da aplicação. Sua API:

```php
<?php

namespace App\Common;

use App\Provider\Database\IDatabase;
use App\Provider\Sql\DeleteSQLBuilder;
use App\Provider\Sql\InsertSQLBuilder;
use App\Provider\Sql\SelectSQLBuilder;
use App\Provider\Sql\UpdateSQLBuilder;

/**
 * @template TModel
 */
abstract class Repository {

  function __construct(
    protected IDatabase $database
  ) { }

  protected function __execSql(string $sql, $params = []): array|bool;

  protected function __querySql(string $sql, $params = []): array;

  protected function __exec(InsertSQLBuilder|UpdateSQLBuilder|DeleteSQLBuilder $sqlBuilder): array;

  protected function __findOne(SelectSQLBuilder $selectBuilder): ?array;

  protected function __findMany(SelectSQLBuilder $selectBuilder): array;

  /**
   * @param class-string<TModel> $modelConstructor
   * @return TModel[]
   */
  protected static function toModelList(array $rawList, string $modelConstructor): array;

  /**
   * @param class-string<TModel> $modelConstructor
   * @return ?TModel
   */
  protected static function toModel(array|null $raw, string $modelConstructor): object;
}
```

### Injeção de conexão com o banco

Todo repositório deve receber uma instância da conexão com o banco de dados ([Injeção de Dependência](../fundamentos/injecao-dependencia.md)), já que o repositório é independente da conexão com o banco de dados

### Ações de execução de SQL

Para realizar as operações de consulta e execução de SQL pode-se optar em usar dois grupos de métodos: via **String SQL** e [**SQL Builder**](../tecnicas/sql-builder.md)

- **String SQL**: `__execSql` e `__querySql`. Realiza as operação do banco passando diretamente a String do SQL, porém, sua utilização abre brexas para *Injeção de SQL*, portante, se usá-lo, deve-se montar um template de SQL e passar os parâmetros separadamente. Exemplo:
    ```php
    // Crie um template de SQL passando os parâmetros de forma separada e no lugar do valor passado na string, escreva '?' para indicar que é um parâmetro
    $this->__querySql('SELECT * FROM users WHERE login = ? LIMIT 1', [$login]);
    ```
- **SQL Builder**: `__exec`, `__findOne` e `__findMany`. Realiza as operações do banco passando um SQL Builder. Por baixo dos panos, ele realiza a mesma operação de montagem do template do SQL e a separação dos parâmetros, com a vantagem da preparação da estrutura do SQL e de fácil leitura ao programador, já que o mesmo possui a mesma semantica que o SQL. Exemplo:
    ```php
    // Crie um template de SQL passando os parâmetros de forma separada e no lugar do valor passado na string, escreva '?' para indicar que é um parâmetro
    $this->__queryOne(
      SQL::select()
        ->from('users')
        ->where(
          SQL::eq('login', $login)
        )
    );
    ```

Por debaixo dos panos, estes métodos executa as ações da própria instância do `Database` que foi injetada nela.

### Tratando o retorno

Ao usar o método `__exec` para realizar operações de inserção, atualização e exclusão de registros, seu retorno será os próprios registros (linhas/tuplas) que foram alteradas, ou seja, ao realizar uma operação de `INSERT`, o mesmo retornará o próprio registro criado com o *id* definido. Veja mais sobre `RETURNING` [aqui](https://www.postgresql.org/docs/current/dml-returning.html)