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
