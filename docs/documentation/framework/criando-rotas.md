# Criando as Rotas da aplicação

## Da classe `Router`

A classe `Router` provê métodos de definição das rotas que servirão como endpoints da aplicação. É possível definir os endpoints chamando os métodos estáticos equivalentes aos métodos HTTP como `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD` e `OPTIONS`.

Sintaxe:

```php
use App\Core\Components\Router;

Router::get(string $path, ...$handlers);
Router::post(string $path, ...$handlers);
Router::put(string $path, ...$handlers);
Router::patch(string $path, ...$handlers);
Router::delete(string $path, ...$handlers);
Router::head(string $path, ...$handlers);
Router::options(string $path, ...$handlers);
```

- Substitua o `$path` pelo nome do endpoint
- Os `$handlers` são as funções que serão executadas quando a rota fornecida for requisitada, podendo ser uma função anônima, uma classe `controller` ou uma classe `Middleware`. Exemplos de cada um:

```php
// # Por Função Anônima
// Passe uma função anônima no handler para executar a ação
Router::get('/users', function(Request $request, Response $response) {
  // ...
});

// # Por Controller
class ExemploController {

  function listAll(Request $request, Response $response) {
    // ...
  }
}

// No parâmetro do handler, deve ser passado um array onde a primeira posição
// é a própria classe e a segunda o nome do método que deve executar a ação
// NOTA: se passar o nome de uma função que não existe na classe, esse handler será ignorado!
Router::get('/users', [ExemploController::class, 'listAll']);

// # Por Classe Middleware
use App\Core\Components\Middleware;

class ExemploMiddleware implements Middleware {

  function perform(Request $request, Response $response) {
    // ...
  }
}

// Passe a classe que implementa o middleware que executará a ação
Router::get('/users', ExemploMiddleware::class);

// NOTA: Os seguintes exemplos são equivalentes ao exemplo anterior, portanto,
// é redundante fazer dessa forma
Router::get('/users', [ExemploMiddleware::class]);
Router::get('/users', [ExemploMiddleware::class, 'perform']);
```

## Nome do endpoint

Considerações:

- Cuidado ao declarar o mesmo nome de rota para o mesmo método HTTP. Se houver conflito, será disparado uma exceção com a mensagem: `Router "{method_name}" "{path_name}" already defined`
- Declarar uma rota vazia `''` ou apenas com _barra_ `'/'` surtirá o mesmo efeito
- Não é necessário colocar uma _barra_ `'/'` no final da rota, ela será removida ao fazer quando a aplicação carregá-la. Exemplo: Considere fazer apenas `'/users'` do que `'/users/'`

## Múltiplos handlers

É possível passar vários handlers numa mesma requisição, que assim, a aplicação irá executá-los na ordem em que são definidos:

```php
Router::get(
  '/users',
  function(Request $request, Response $response) { /* ... */},
  ExemploMiddleware::class,
  [ExemploController::class, 'listAll'],
  // Outros handlers...
);
```

É importante notar que ao disparar uma exceção em um dos handlers, os demais não serão executados.

## Router Maker

Quando precisa declarar várias rotas que possuem o mesmo prefixo, é possível trabalhar com o `RouterMaker`, assim, toda rota definida por ele receberá seu prefixo definido. Exemplo:

```php
// # Forma 1 (Sem RouterMaker)
Router::get('/users', [UserController::class, 'listAll']); // /users
Router::post('/users/create', [UserController::class, 'create']); // /users/create
Router::put('/users/update', [UserController::class, 'update']); // /users/update
Router::delete('/users', [UserController::class, 'delete']); // /users

// # Forma 2 (Com RouterMaker)
$router = Router::maker('/users'); // Prefixo '/users'

$router->get('', [UserController::class, 'listAll']); // /users
$router->post('/create', [UserController::class, 'create']); // /users/create
$router->put('/update', [UserController::class, 'update']); // /users/update
$router->delete('', [UserController::class, 'delete']); // /users
```

## Grupos de Rotas

Diferente de outras linguagens que, no momento em que é levantado o servidor é carregado todos os componentes da aplicação (como rotas, conexão com o banco e outros serviços), o PHP não faz o carregamento dos componentes no momento em que é levantado o servidor, mas sim em toda requisição feita, ou seja, toda vez que é feito uma requisição, será feito o carregamento de todos os componentes novamente

Pensando nisso, não se deve declarar todas as rotas da aplicação em uma vez só, já que apenas uma dessas rotas será executada, será um desperdício de processamento ter que declara-las toda vez que é feito uma requisição

Para solucionar isso, é possível agrupar as rotas da aplicação por prefixo usando o método `writeRouter` da classe `Router`

```php
// No arquivo `routers.php`
Router::writeRouter([
  'prefix' => '/users', // Prefixo da rota requisitada
  'filePath' => 'Router/UserRouter.php', // Arquivo contendo as rotas deste grupo
]);

// No arquivo `Router/UserRouter.php`
Router::get('/users', [UserController::class, 'listAll']); // /users
Router::post('/users/create', [UserController::class, 'create']); // /users/create
Router::put('/users/update', [UserController::class, 'update']); // /users/update
Router::delete('/users', [UserController::class, 'delete']); // /users
```

Assim, quando requisita a rota `/users/create`, será primeiramente buscado os grupos de rotas que contém aquele prefixo, para assim ler todas as rotas declaradas dentro dele

ATENÇÃO: O prefixo definido no `writeRouter` não irá ser concatenado junto aos _paths_ das rotas definidas no arquivo definido no `filePath`, portanto, este prefixo ainda deve ser declarado nas rotas daquele grupo!
