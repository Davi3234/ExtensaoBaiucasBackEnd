# Projeto Extensão Baiuxas - UDESC

Ao clonar o projeto, siga os seguintes passos para fazer o setup do ambiente:

- Para rodar o BackEnd juntamente com o FrontEnd é necessário ter o docker instalado, copiar o arquivo do docker-compose dentro da pasta `docker` no projeto, onde a estrutura de pastas ficará parecida com essa:
  ```
  📦 Meu-projeto
  ├── ExtensaoBaiucasBackEnd 📂 (Link: https://github.com/Davi3234/ExtensaoBaiucasBackEnd)
  ├── ExtensaoBaiucas 📂 (Link: https://github.com/Davi3234/ExtensaoBaiucas)
  └── docker-compose.yaml 📄 (Link: https://github.com/Davi3234/ExtensaoBaiucasBackEnd/blob/main/docker/docker-compose.yaml)
  ```
- Duplique o arquivo `.env.example` e renomeie para `.env`. Dentro dele, substitua os valores das variáveis de ambiente para que seja possível realizar a execução local na sua máquina;
- As configurações do banco estão no docker-compose.
- Depois de realizar esses passos, entre na pasta do seu projeto e rode o comando `docker composer up --build`

- RF01- O sistema deve permitir que o usuário registre e gerencie perfis de clientes, incluindo informações como cpf, nome, e-mail e senha e endereço. 
- RF02 - O sistema deve permitir que o usuário se autentique por meio de login e senha, garantindo segurança e integridade dos dados. 
- RF03 - O sistema deve permitir o cadastro de produto, incluindo informações do produto: nome, descrição, valor, categoria (Bebida ou Comida) e se está ativo ou não. 
- RF04 - O sistema deve permitir que o usuário visualize e mantenha os pedidos, com informações detalhadas sobre os itens, datas e status de cada pedido. 
- RF05 - O sistema deve permitir que o usuário adicione e gerencie métodos de pagamento, como cartões de crédito, pix
- RF06 - O sistema deve oferecer ao usuário a possibilidade de personalizar pedidos, como a remoção ou adição de ingredientes. 
