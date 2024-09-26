# Criando um Service

O `Service` é onde conterá todos os casos de uso junto com as regras de negócio

Exemplo:
```php
use App\Repository\IPostRepository;
use App\Repository\IUserRepository;
use App\Exception\Http\BadRequestException;

class PostService {

  function __construct(
    private IPostRepository $postRepository,
    private IUserRepository $userRepository,
  ) {
  }

  function create(array $args) {
    $post = new Post;

    $post->setUserId($args['userId']);
    $post->setSubject($args['subject']);
    $post->setBody($args['body']);

    $errors = [];
    if (!$post->getSubject()) {
      $errors[] = ['message' => 'Subject cannot be empty', 'cause' => 'subject'];
    }
    if (!$post->getBody()) {
      $errors[] = ['message' => 'Body cannot be empty', 'cause' => 'body'];
    }

    if ($errors) {
      throw new BadRequestException('Cannot create post', $errors);
    }

    $user = $this->userRepository->findById($post->getUserId());

    if (!$user) {
      throw new BadRequestException(
        'Cannot create post',
        [['message' => 'User not found', 'cause' => 'userId']]
      );
    }

    $this->postRepository->create($post);

    return ['message' => 'Post created with successfully'];
  }
}

```