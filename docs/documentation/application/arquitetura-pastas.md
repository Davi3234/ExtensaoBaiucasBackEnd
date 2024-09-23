# 2 - Arquitetura de Pastas

A aplicação segue a convencional arquitetura de projeto MVC, seguindo esta estrutura de pastas:
```
src/
├── @tests/           # Testes unitários e de integração para garantir a qualidade do código
├── Common/           # Componentes e utilitários reutilizáveis em diferentes partes da aplicação
├── Controller/       # Controladores responsáveis por lidar com as requisições HTTP
├── Core/             # Framework da aplicação
|   └── Components/   # Componentes centrais e fundamentais da aplicação
├── Enum/             # Definições de enums utilizados em toda a aplicação
├── Exception/        # Classes para tratamento de exceções personalizadas
├── Middleware/       # Middlewares que interceptam requisições para aplicar lógica antes de chegar aos controladores
├── Model/            # Modelos que representam as entidades e regras de negócio
├── Provider/         # Provedores de serviços que configuram e inicializam recursos externos
├── Repository/       # Camada de abstração para acesso a dados e interações com o banco de dados
├── Router/           # Definição das rotas da aplicação
├── Service/          # Implementação dos Casos de Uso e Regras de Negócio
└── Util/             # Funções utilitárias e helpers para suporte geral à aplicação
```