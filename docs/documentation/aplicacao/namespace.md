# Trabalhando com `namespaces`

A forma de importação das classes e outros recursos será por `namespace`. Para definir um `namespace`, no começo de cada arquivo `.php` defina seguindo este este padrão:

```php
namespace App\{folder_path};
```

- o `App` é um prefixo global definido no `composer.json` que equivale à pasta `src`
- Substitua o `folder_path` pelo caminho das pastas do arquivo separado por `\`, sem informar o nome do arquivo

## Nomenclatura de arquivos e classes

Terá que ser adotado o padrão de declarar apenas uma classe por arquivo, onde o nome do arquivo deve ser o mesmo da classe definida, para que a o `php` faça a importação correta

## Usando os componentes definidos no `namespace`

Para utilizar uma classe definida em um namespace, no começo do arquivo declare:

```php
use App\{folder_path}\{nome_class};
```

- Substitua `nome_class` pelo nome da classe ou do recursos que está importando
