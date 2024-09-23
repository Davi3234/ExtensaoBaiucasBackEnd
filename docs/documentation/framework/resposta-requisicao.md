# Padrão de retorno de dados

Será adotado um padrão de estrutura de dados retornado pela API que é *Result*. Segue a estrutura de retorno:

```json
{
  "ok": true, // Se a requisição deu certo ou não
  "status": 200, // Status code da requisição HTTP
  "value": null, // Valor retornado do handler, mas apenas quando a requisição der sucesso
  "error": null // Informações do erro ocorrido quando ocorrer uma falha na requisição
}
```

## Retornando dados da requisição

Para retornar dados de uma requisição, no `handler` basta apenas retornar os dados que deseja ser enviados pelo requisição

Considerações:
- Se o `handler` retorna `void`, será tratado como `null`, pois é dessa forma que o PHP interpreta o `void`
- Quando uma rota possuir vários `handlers`, apenas será considerado o retorno do último `handler` que retornar algum valor diferente de `null | void`

Exemplo:
```php
class ExemploController {

  foo() {
    return 'Hello World'; // Será retornado no padrão 'Result' na propriedade 'value'
  }
}
```

O exemplo acima retornará:
```json
{
  "ok": true,
  "status": 200,
  "value": "Hello World",
  "error": null
}
```

## Retornando um erro

Para retornar uma falha ao usuário deve-se utilizar exceções, informando tanto a mensagem do erro quando as causas do erro (opcional) que servirá para quem solicitar saber exatamente a origem do que ocasionou o erro, desta forma:
```php
class UserController {

  create() {
    // Dispare o erro utilizando a exceção mais cabível pro contexto
    throw new BadRequestException(
      'Erro no cadastro do usuário', // Mensagem genérica do erro. Normalmente referente ao nome da operação
      [
        ['message' => 'Nome é obrigatório', 'origin' => 'name'], // Causa do erro
        ['message' => 'Login é obrigatório', 'origin' => 'login'] // Causa do erro
      ]
    );
  }
}
```

O exemplo acima retornará:
```json
{
  "ok": false,
  "status": 400,
  "value": null,
  "error": {
    "message": "Erro no cadastro do usuário",
    "causes": [
      {
        "message": "Nome é obrigatório",
        "origin": "name"
      },
      {
        "message": "Login é obrigatório",
        "origin": "login"
      }
    ]
  }
}
```

## Da classe `Result`

Por baixo dos panos, o que é retornado na API é uma instância da classe `Result`. Ela fornece dois métodos úteis: `success` e `failure`

Exemplo de sucesso:
```php
namespace App\Core\Components\Result;

Result::success($value, int $statusCode = 200);
```

- Substitua o `$value` pelo valor que posteriormente será retornado na requisição
- Substitua o `statusCode` pelo status code HTTP com base no contexto do resultado
  - Atenção: Se informar um status code maior ou igual à 400, será disparado um erro com a mensagem: `It is not possible to define a status code greater than or equal to 400 when the result is success`

Exemplo de falha:
```php
namespace App\Core\Components\Result;

Result::failure(array $error, int $statusCode = 400);
```

- Substitua o `$error` pelo erro ocorrido informando um array com os seguintes dados:
  - *message*: Mensagem do erro
  - *causes[]*:
    - *message*: Mensagem de erro da causa
    - *origin?*: Origem da causa
- Substitua o `statusCode` pelo status code HTTP com base no contexto do resultado
  - Atenção: Se informar um status code menor que 400, será disparado um erro com a mensagem: `It is not possible to set a status code lower than 400 when the result is failure`

### Retornando diretamente um *Result*

Ao invés de retornar o próprio resultado em si, é possível também retornar uma instância de `Result`, dessa forma:

```php
class UserController {

  create() {
    // # Antes
    // return ['message' => 'Usuário criado com sucesso']; // Assim, retornará com o status code padrão (200)

    // # Depois (com o Result)
    return Result::success(['message' => 'Usuário criado com sucesso'], StatusCode::CREATED->value); // É possível alterar o status code
  }
}
```

O exemplo acima retornará:
```json
{
  "ok": true,
  "status": 201,
  "value": {
    "message": "Usuário criado com sucesso"
  },
  "error": null
}
```

É possível também retornar um erro sem disparar uma exceção, desta forma:

```php
class UserController {

  create() {
    // # Antes
    // throw new BadRequestException(
    //   'Erro no cadastro do usuário', // Mensagem genérica do erro. Normalmente referente ao título da operação
    //   [
    //     ['message' => 'Nome é obrigatório', 'origin' => 'name'], // Causa do erro
    //     ['message' => 'Login é obrigatório', 'origin' => 'login'] // Causa do erro
    //   ]
    // );

    // # Depois (com o Result)
    return Result::failure(
      [
        'message' => 'Erro no cadastro do usuário',
        'causes' => [
          ['message' => 'Nome é obrigatório', 'origin' => 'name'],
          ['message' => 'Login é obrigatório', 'origin' => 'login']
        ]
      ],
      StatusCode::BAD_REQUEST->value
    ); // É possível alterar o status code
  }
}
```

O exemplo acima retornará:
```json
{
  "ok": false,
  "status": 400,
  "value": null,
  "error": {
    "message": "Erro no cadastro do usuário",
    "causes": [
      {
        "message": "Nome é obrigatório",
        "origin": "name"
      },
      {
        "message": "Login é obrigatório",
        "origin": "login"
      }
    ]
  }
}
```