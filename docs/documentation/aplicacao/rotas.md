# Criando as Rotas da Aplicação

No arquivo `groups-router.php` será definido todos os [grupos de rotas](../framework/criando-rotas.md#grupos-de-rotas) da aplicação

Exemplo:

```php
use App\Core\Components\Router;

Router::writeRouter([
  'prefix' => '/users',
  'filePath' => 'Router/UserRouter.php',
]);

Router::writeRouter([
  'prefix' => '/posts',
  'filePath' => 'Router/PostRouter.php',
]);

// ...
```

Os arquivos de rotas definidos na propriedade `filePath` irá registrar todas as [rotas](../framework/criando-rotas.md#criando-as-rotas-da-aplicação) presentes daquele grupo, seguindo um modelo parecido com isso:

```php
// Em Router/PostRouter.php

use App\Core\Components\Router;
use App\Controller\PostController;
use App\Middleware\AuthenticationMiddleware;

$router = Router::maker('/posts');

$router->get('', AuthenticationMiddleware::class, [PostController::class, 'query']);
$router->get('/:id', AuthenticationMiddleware::class, [PostController::class, 'getOne']);
$router->post('/create', AuthenticationMiddleware::class, [PostController::class, 'create']);
$router->delete('/:id', AuthenticationMiddleware::class, [PostController::class, 'delete']);
```
