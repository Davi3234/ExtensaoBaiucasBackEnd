# Criando um Service

O `Service` é onde conterá todos os casos de uso junto com as regras de negócio

Exemplo:
```php
use App\Exception\Http\BadRequestException;
use App\Provider\Zod\Z;
use App\Repository\IPostRepository;
use App\Repository\IUserRepository;

class PostService {

  function __construct(
    private readonly IPostRepository $postRepository,
    private readonly IUserRepository $userRepository,
  ) {
  }

  function create(array $args) {
    $createSchema = Z::object([
      'userId' => Z::number(['required' => 'Id do Usuário é obrigatório', 'invalidType' => 'Id do Usuário inválido'])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Usuário inválido'),
      'subject' => Z::string()
        ->trim()
        ->min(0, 'Assunto é obrigatório')
        ->max(255, 'O Assunto precisa conter no máximo 255 caracteres'),
      'body' => Z::string()
        ->trim()
        ->min(0, 'O Corpo da mensagem é obrigatório'),
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $user = $this->userRepository->findById($dto->userId);

    if (!$user) {
      throw new BadRequestException(
        'Cannot create post',
        [
          ['message' => 'User not found', 'cause' => 'userId']
        ]
      );
    }

    $post = new Post;

    $post->setUserId($dto->userId);
    $post->setSubject($dto->subject);
    $post->setBody($dto->body);

    $this->postRepository->create($post);

    return ['message' => 'Post created with successfully'];
  }
}
```

Analisando o código, é possível perceber o uso de alguns conceitos, como [Injeção de Dependência](../fundamentos/injecao-dependencia.md) e [validação de dados de entrada](./validacao-dados-entrada.md) com [Zod](../tecnicas/zod.md)

## Injeção de Dependência nos Serviços

`Services` não devem depender de serviços externos, como comunicação com o banco, envio de email/whatsapp ou comunicação com outra API. Por isso, não é responsabilidade do `Service` conhecer este serviço externo, como um [repositório](./repository.md) para se comunicar com o banco, portanto, esses serviços devem ser passados (Injetados) nele de forma externo em seu construtor - normalmente através de um controller ou de outro service, em outras palavras, "quem usar este `Service` deve passar para ele as instâncias das suas dependências"

Exemplo de injeção de dependência:
```php
class PostController {

  private UserService $userService;

  function __construct() {
    $databaseConnection = Database::getGlobalConnection();

    $this->userService = new UserService(
      new PostRepository($databaseConnection), // Injetando as classes de repositório no service
      new UserRepository($databaseConnection), // Injetando as classes de repositório no service
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

Note que ambos os repositórios `PostRepository` e `UserRepository` recebem uma injeção da conexão com o banco, pois eles também tem dependência com serviços externos, como a própria conexão com o banco de dados