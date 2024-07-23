# saqueSuitPay

# API Para Saque - SuitPay
Este projeto tem como objetivo criar uma página web para realizar testes de transações PIX via API. O código é um exemplo de como integrar uma API de pagamento PIX em uma página HTML personalizada com suporte para Bootstrap e formatação de campos de entrada.

# Funcionalidades

- A página web permite ao usuário escolher entre duas opções de saque: por telefone ou CPF.
- Utiliza Bootstrap para um design moderno e responsivo, garantindo uma boa experiência em dispositivos móveis e desktops.

# Formatação Dinâmica:

- Telefone: O campo de entrada para o telefone é formatado automaticamente enquanto o usuário digita, exibindo o número no formato (XX) XXXXX-XXXX e enviando o valor sem formatação para a API.-
- CPF: O campo de entrada para o CPF é formatado no formato XXX.XXX.XXX-XX e o valor enviado para a API é mantido com a formatação original.
  
# Integração com a API PIX:

Envia uma solicitação para a API de pagamento PIX para realizar uma transação de teste.
O valor do teste é gerado aleatoriamente entre R$ 0,05 e R$ 0,10.

# Feedback do Usuário:

Após o envio, o usuário recebe uma confirmação de sucesso ou erro com base na resposta da API.

# Tecnologias Utilizadas
PHP: Para processamento do backend e interação com a API.
HTML/CSS: Estrutura e estilo da página web.
Bootstrap: Para estilização e layout responsivo.
JavaScript: Para formatação dinâmica dos campos de entrada e controle da visibilidade dos formulários.

Referência: 
Este projeto é baseado na função de saque do site https://cuponospremiado.cloud/oferta/.
