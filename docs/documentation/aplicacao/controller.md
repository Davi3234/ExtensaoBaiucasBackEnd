# Criando o Controller

O `Controller` terá a responsabilidade de receber as requisições da API por meio dos métodos, conforme mapeados em [`Router`](rotas.md), e repassar para o [`Service`](service.md) realizar o caso de uso junto com as regras de negócio

Exemplo:
```php
// Em Router/PostRouter.php
use App\Core\Components\Router;
use App\Controller\PostController;
use App\Middleware\AuthenticationMiddleware;

Router::post('/create', AuthenticationMiddleware::class, [PostController::class, 'create']);

// Em Controller/PostController.php
use App\Core\Components\Request;
use App\Provider\Database\Database;
use App\Repository\PostRepository;
use App\Service\PostService;

class PostController {

  private readonly PostService $postService;

  function __construct() {
    $databaseConnection = Database::getGlobalConnection();

    $this->userService = new UserService(
      new PostRepository($databaseConnection),
      new UserRepository($databaseConnection),
    );
  }

  function create(Request $request) {
    $result = $this->postService->create([
      'userId' => $request->getAttribute('userId'),
      'subject' => $request->getBody('subject'),
      'body' => $request->getBody('body'),
    ]);

    return $result;
  }
}
```

Em seu método construtor, é realizado a instância dos serviços, bem como a preparação do estado da classe para assim receber a requisição