# Requisitos a serem atendidos do backend:

## RF
- RF1: O sistema deve permitir que o usuário mantenha os usuários
- RF2: O sistema deve permitir que o usuário se autentique no sistema
- RF3: O sistema deve permitir que o usuário mantenha os endereços
- RF4: O sistema deve permitir que o usuário mantenha os cardápios
- RF5: O sistema deve permitir que o usuário mantenha os menus
- RF6: O sistema deve permitir que o usuário mantenha os itens

## RNF
- Será desenvolvido em aplicação WEB

## RN 
- Para RF1:
  - O cadastro de usuário deve incluir: nome, email, senha, cpf/cnpj, nº de celular, tipo de usuário
  - Não é possível cadastrar mais de um usuário com o mesmo email
  - Os tipos de pessoa são: "Física" e "Jurídica"
  - Os Tipos de usuários são: "Cliente" (consumidor), "Proprietário" (atendente/gerente/administrador), "Entregador" (entrega delivery)
  - O cadastro de um usuário do tipo "Proprietário" e "Entregador" só pode ser feito por outro usuário do tipo "Proprietário"
- Para RF2:
  - Na autenticação do usuário deve ser informado email e senha
- Para RF3:
  - O cadastro de endereço deve conter: estado, cidade, bairro, rua, nº da casa, cliente
- Para RF4:
  - O cadastro de cardápio deve conter: se está ativo ou não, título
  - O cadastro de cardápio de fer feito por um usuário do tipo "Proprietário"
- Para RF5:
  - O cadastro de menu deve conter: se está ativo ou não, título, menu pai (opcional)
  - O cadastro de menu de fer feito por um usuário do tipo "Proprietário"
- Para RF6:
  - O cadastro de menu deve conter: se está ativo ou não, título, descrição, menu, tipo de preço, valor único, valores flexíveis
  - O tipo de preço do item pode ser: "Único" ou "Flexível"
  - No campo de valores flexíveis, deve ser informado o tamanho e preco de cada porção
  - O cadastro de item deve fer feito por um usuário do tipo "Proprietário"