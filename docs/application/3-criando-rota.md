# Criando Rotas da API

Para se criar rotas se utiliza a classe `Router` definido em `Core/Components/Router.php`. Nele há métodos estáticos para declaração das rotas `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD` e `OPTIONS`

## Da classe `Router`



Exemplo:
```php
use App\Core\Components\Router;

Router::get('/users');
Router::post('/users/create');
Router::put('/users/update');
Router::delete('/users/:id');
```
