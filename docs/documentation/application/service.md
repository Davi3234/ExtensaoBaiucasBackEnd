# Criando um Service

O `Service` é onde conterá todos os casos de uso junto com as regras de negócio

Exemplo:
```php
use App\Provider\Zod\Z;
use App\Exception\Http\BadRequestException;
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
        ->min(0, 'Assunto é obrigatório'),
      'body' => Z::string()
        ->trim()
        ->min(0, 'O Corpo da mensagem é obrigatório'),
    ]);

    $dto = $createSchema->parseNoSafe($args);

    $user = $this->userRepository->findById($dto->userId);

    if (!$user) {
      throw new BadRequestException(
        'Cannot create post',
        [['message' => 'User not found', 'cause' => 'userId']]
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