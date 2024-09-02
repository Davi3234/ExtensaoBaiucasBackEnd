# Requisição

A classe `Request` é uma classe que contém todos os dados da requisição. Ela é carregada em toda requisição e pode ser acessada a partir de um handler definido no `Router`

## Parâmetros

Parâmetros são definidos no próprio *path* da rota definida. Eles podem ser definidos de duas maneiras:
- **Parâmetro**: no `path`, declare o nome do parâmetro com o `:` no início. Exemplo: `/users/:id`
- **Query-String**: Ao chamar uma rota da aplicação, é possível passar as *query-strings* depois da chamada da rota. Exemplo: `/users?id=1`
  - Nota: O parâmetro definido direto na rota sobrescreve a *query-string* que possuir o mesmo nome

Exemplo:

```php
namespace App\Core\Components\Request;

Router::put('/users/:id', function(Request $request, Response $response) {
  // # Forma 1
  $params = $request->getParams();
  $id = $params['id'];

  // # Forma 2
  $id = $request->getParam('id');
});

// Ao ser requisitado a rota PUT '/users/6', o valor da variável $id será 6
```

## Headers

É possível obter os dados do cabeçalho da requisição acessando os métodos `getHeaders` e `getHeader`. Os dados definidos nele são os dados da variável super global `$_SERVER`

```php
Router::put('/users/:id', function(Request $request, Response $response) {
  $httpMethod = $request->getHeader('REQUEST_METHOD'); // PUT
});
```

# Obtendo os dados do corpo

Para se obter os dados do corpo da requisição, utiliza-se o método `getAllBody` ou `getBody`. Exemplo:

```php
Router::post('/users/create', function(Request $request, Response $response) {
  // # Forma 1
  $body = $request->getAllBody();
  $name = $body['name'];
  $login = $body['login'];

  // # Forma 2
  $name = $request->getBody('name');
  $login = $request->getBody('login');
});

// Ao ser requisitado a rota PUT '/users/6', o valor da variável $id será 6
```

## Atributos

É possível colocar dados na instância de `Request` por meio de atributos. Isso é util quando há vários `handlers` para a mesma rota onde é necessário repassar informações para os `handlers` seguintes. Exemplo:

```php

class AuthenticationMiddleware implements Middleware {

  function perform(Request $request) {
    $authentication = request->getHeader('authentication');

    $userId = null; // Lógica de autenticação do usuário...

    // Setando o atributo userId
    $request->setAttribute('userId', $userId);
  }
}

class UserController {

  function update(Request $request) {
    // Obtendo o valor do atributo userId
    $userId = $request->getAttribute('userId');
    $name = $request->getBody('name');
    $login = $request->getBody('login');
  }
}

Router::put('/users', AuthenticationMiddleware::class, [UserController::class, 'update']);
```