# Criando um model

Em `src/Model` será declarado as entidades da aplicação. Todo o model deve extender a classe `Model` como no seguinte exemplo:

```php
use App\Common\Model;

class ModelExample extends Model {

  protected function __load(array $raw) {
    // ...
  }
}
```

- Ao extender a class `Model`, deve se implementar o método `__load`, que recebe um array com os dados carregados e este deve carregar nas propriedades da própria classe, como no exemplo a seguir:

```php
use App\Common\Model;

class User extends Model {

  private int $id;
  private string $name;
  private string $login;

  #[\Override] // Anotação pra indicar que este método sobrescreve um método definido na sua super classe ou na interface que a classe implementa
  protected function __load(array $raw) {
    $this->id = $raw['id'];
    $this->name = $raw['name'];
    $this->login = $raw['login'];
  }

  // gets, sets ...
}
```

Isso serve para posteriormente ser usada pelo [`Repository`](7.1-repository.md) que irá carregar as instâncias do seu respectivo `Model` usando o método `__load` para carregá-lo