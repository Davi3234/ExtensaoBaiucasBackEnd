______
# RF
- O sistema deve permitir que o usuário mantenha os usuários
- O sistema deve permitir que o usuário se autentique no sistema
- O sistema deve permitir que o usuário consulte o histórico de pedidos feito
- O sistema deve permitir que o usuário mantenha os pedidos
- O sistema deve permitir que o usuário mantenha os endereços
- O sistema deve permitir que o usuário mantenha os pagamentos
- O sistema deve permitir que o usuário mantenha as entregas dos pedidos deliveries
- O sistema deve manter o fluxo da situação do pedido
- O sistema deve manter o fluxo da situação do pagamento
- O sistema deve permitir que o usuário mantenha os cardápios
- O sistema deve permitir que o usuário mantenha os menus
- O sistema deve permitir que o usuário mantenha os itens
- O sistema deve realziar envio de notificações
______
# RNF
- Será desenvolvido em aplicação mobile e deverá estar disponível para todas as plataformas IOS/Android
______
# RN
## O sistema deve permitir que o usuário mantenha os usuários
- O cadastro de usuário deve incluir: nome, email, senha, cpf/cnpj, nº de celular, tipo de usuário

- Não é possível cadastrar mais de um usuário com o mesmo email

- Os tipos de pessoa são: "Física" e "Jurídica"

- Os Tipos de usuários são: "Cliente" (consumidor), "Proprietário" (atendente/gerente/administrador), "Entregador" (entrega delivery)

- O cadastro de um usuário do tipo "Proprietário" e "Entregador" só pode ser feito por outro usuário do tipo "Proprietário"

## O sistema deve permitir que o usuário se autentique no sistema
- Na autenticação do usuário deve ser informado email e senha

## O sistema deve permitir que o usuário mantenha os endereços
- O cadastro de endereço deve conter: estado, cidade, bairro, rua, nº da casa, cliente

## O sistema deve permitir que o usuário mantenha os pedidos
- O cadastro de pedido deve conter: Número do pedido, data e hora do pedido realizado, tempo estimado para finaliza-lo, cliente, identificador do cliente (opcional), situação, itens do pedido, valor total, código de promoção, tipo de entrega, nº da casa, rua, bairro, cidade e estado

- As situações do pedido são: "Na Fila", "Em Andamento", "Pronto", "Em Rota de Entrega", "Entregue" e "Cancelado"

- Os tipos de entrega são: "Delivery" ou "Estabelecimento"

## O sistema deve permitir que o usuário consulte o histórico de pedidos feito
Todo pedido realizado que esteja vinculado a um cliente, o mesmo ficará salva em seu histórico

- O usuário do tipo "Cliente" pode consultar todos os pedidos feito pelo mesmo

- O usuário do tipo "Proprietário" pode consultar todos os pedidos feito pelos clientes

## O sistema deve permitir que o usuário mantenha os pagamentos
- A inclusão de um pagamento deve conter: o pedido, tipo de pagamento, local do pagamento, situação do pagamento

- Os tipos de pagamento são: "Cartão", "Pix", "Dinheiro"

- Os locais possíveis para pagamento são via: "Aplicativo", "Balcão" ou "Na Entrega"

- As situações de pagamento são: "Pago", "Pendente", "Devolvido" ou "Cancelado"

## O sistema deve manter o fluxo da situação do pedido
- Após realizar o pedido a situação do mesmo passa para "Na Fila"

- Usuários do tipo "Proprietário" podem receber os pedidos que estão "Na Fila" e passar para "Em Andamento" quando o mesmo está sendo feito

- Quando o pedido foi finalizado e o mesmo estiver em "Em Andamento", o usuário do tipo "Proprietário" passa para "Pronto"

## O sistema deve manter o fluxo da situação do pagamento
- A inclusão de um pagamento deve estar vinvulado a um pedido

- Não pode incluir um novo pagamento para o pedido que tenha outros pagamentos com as situações: "Pago" ou "Pendente"

- Após confirmar o pagamento, a situação do mesmo passa para "Pago"

## O sistema deve permitir que o usuário mantenha as entregas dos pedidos deliveries
- Usuários do tipo "Entregador" podem receber os pedidos que estão "Prontos" e o usuário do tipo "Proprietário" pode passar para "Em Rota de Entrega"

- Após efetuar a entrega do pedido do tipo "Estabelecimento", o usuário do tipo "Proprietário" pode passar a situação do pedido para "Entregue"

- Após efetuar a entrega do pedido do tipo "Delivery", o usuário do tipo "Proprietário" e/ou "Entregador" pode passar a situação do pedido para "Entregue"

## O sistema deve permitir que o usuário mantenha os cardápios
- O cadastro de cardápio deve conter: se está ativo ou não, título

- O cadastro de cardápio de fer feito por um usuário do tipo "Proprietário"

## O sistema deve permitir que o usuário mantenha os menus
- O cadastro de menu deve conter: se está ativo ou não, título, menu pai (opcional)

- O cadastro de menu de fer feito por um usuário do tipo "Proprietário"

## O sistema deve permitir que o usuário mantenha os itens
- O cadastro de menu deve conter: se está ativo ou não, título, descrição, menu, tipo de preço, valor único, valores flexíveis

- O tipo de preço do item pode ser: "Único" ou "Flexível"

- No campo de valores flexíveis, deve ser informado o tamanho e preco de cada porção

- O cadastro de item deve fer feito por um usuário do tipo "Proprietário"

## O sistema deve realizar envio de notificações
- O envio de notificação deve ser feito após a atualização da situação do pedido
______
