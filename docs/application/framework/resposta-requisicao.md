# Retornando dados da requisição

Para retornar dados de uma requisição, no `handler` basta apenas retornar os dados que deseja ser enviados pelo requisição

Considerações:
- Se o `handler` retorna `void`, será tratado como `null`, pois é dessa forma que o PHP interpreta o `void`
- Quando uma rota possuir vários `handlers`, apenas será considerado o retorno do último `handler`