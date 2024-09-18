# Instruções para Rodar o Projeto

## Etapas para Rodar o Sistema:

1. **Crie um diretório onde será clonado todo o sistema:**
   - Escolha um local apropriado e crie um diretório para o projeto.

2. **Clone o projeto para o diretório criado:**
   - Utilize o comando abaixo e insira sua senha do GitHub se solicitado:
     ```bash
     git clone https://github.com/maxuelassis95/teste-proficiencia-laravel.git
     ```

3. **Copie o arquivo `.env.example` para `.env`:**
   - No terminal, dentro do diretório clonado, execute os seguintes comandos:
     ```bash
     cd TesteProficienciaLaravel_app
     sudo cp .env.example .env
     cd ../
     ```

4. **No diretório raiz clonado, configure as permissões:**
   - Execute os comandos:
     ```bash
     sudo chmod 777 -R TesteProficienciaLaravel_app
     sudo chown www-data:www-data TesteProficienciaLaravel_app
     ```

5. **Dentro do diretório clonado, construa as imagens Docker:**
   - Execute:
     ```bash
     sudo docker compose build
     ```
     > Aguarde enquanto as imagens são construídas.

6. **Suba os containers:**
   - Execute:
     ```bash
     sudo docker compose up -d
     ```
     > Esse comando irá iniciar os containers para PHP, Nginx, Redis e MySQL.

7. **Liste todos os containers para encontrar o ID ou nome do container PHP:**
   - Execute:
     ```bash
     sudo docker ps -a
     ```

8. **Acesse o container PHP:**
   - Execute:
     ```bash
     sudo docker exec -it <id_ou_nome_container_php> /bin/bash
     ```

9. **Dentro do container, execute os seguintes comandos:**
   - Instale as dependências do Composer:
     ```bash
     composer install
     ```
   - Gere a chave de aplicação:
     ```bash
     php artisan key:generate
     ```

10. **Importante:**
    - O arquivo `.env.example` já possui as configurações do banco de dados conforme definido no `docker-compose.yml`. Se você alterar essas configurações no Docker, atualize o arquivo `.env` de acordo.

11. **Ainda dentro do container, configure o banco de dados e popule as tabelas:**
    - Execute:
      ```bash
      php artisan migrate
      php artisan db:seed
      ```

12. **Ajuste o arquivo `.env` com suas configurações de Mailtrap e Slack:**

    - **Slack Webhook URL:** Adicione a URL do webhook do Slack para receber notificações:
      ```bash
      SLACK_WEBHOOK_URL=<sua_url_de_webhook_do_slack>
      ```

    - **Mailtrap:** Configure o Mailtrap para capturar e-mails durante o desenvolvimento:
      ```bash
      MAIL_MAILER=smtp
      MAIL_HOST=sandbox.smtp.mailtrap.io
      MAIL_PORT=2525
      MAIL_USERNAME=<seu_username_do_mailtrap>
      MAIL_PASSWORD=<sua_senha_do_mailtrap>
      MAIL_ENCRYPTION=null
      MAIL_FROM_ADDRESS="teste@adgency.com"
      MAIL_FROM_NAME="${APP_NAME}"
      ```

**Importante:**
- **Sincronização com o Docker:** Se você alterar as configurações do banco de dados ou outros serviços no Docker (por exemplo, no arquivo `docker-compose.yml`), certifique-se de atualizar o arquivo `.env` para refletir essas alterações. Manter essas configurações sincronizadas é essencial para que a aplicação funcione corretamente.

Após seguir essas etapas e garantir que as configurações no `.env` estejam atualizadas conforme as definições no Docker, você poderá acessar o sistema pelo seguinte endereço:

[http://localhost:8282/pedidos](http://localhost:8282/pedidos)

Além disso, você já pode começar a gerar pedidos de acordo com a documentação abaixo. **Note que será solicitado um e-mail e senha ao acessar o sistema.** Detalhes sobre como criar pedidos e outras informações necessárias estão documentados a seguir.

 - **Email**: admin@teste.com
 - **Senha**: senha123

# Documentação do Sistema de Pedidos

Este projeto foi desenvolvido para testar a proeficiência em Laravel.

## 1. Processamento de Pedidos com Filas

### Como criar um pedido

Para criar um pedido no sistema, você precisará utilizar uma ferramenta como Postman para enviar uma requisição POST para a API.

**Endpoint**

```http
POST http://localhost:8282/api/pedidos
``` 

### Headers:

    Accept: application/json
    Content-Type: application/json

### Exemplo de corpo da requisição (JSON):

```json
{
    "cliente_id" : 1,
    "produtos": [
        {"id":28, "quantidade":1},
        {"id":8, "quantidade":1}
    ]
}

````

#### O que acontece ao criar um pedido:
- Quando o pedido é criado, ele passa por várias **Jobs**:
  1. **Verificação de Disponibilidade dos Produtos**: Verifica se todos os produtos do pedido estão disponíveis no sistema.
  2. **Verificação de Estoque**: Confirma se há quantidade suficiente de cada produto no estoque para atender ao pedido.
  3. **Processamento do Estoque**: Atualiza o estoque, decrementando a quantidade de produtos conforme o pedido.
  4. **Geração de Fatura**: Calcula o valor total do pedido e gera a fatura associada.

- Caso todas as jobs sejam concluídas com sucesso:
  - Um **e-mail de confirmação** é enviado ao cliente em uma fila de prioridade menor.
  - Um **e-mail promocional** é enfileirado com um **delay de 5 minutos** (esse delay baixo é apenas para fins de teste).

  **Observação**: Foram adicionados pequenos **delays (sleep)** entre os jobs para testar a performance do sistema.
  
  - Logs: O sistema registra logs detalhados de erros e falhas, permitindo uma melhor análise do processo.
  
  ### Acesso ao Horizon:

  Você pode monitorar as filas, jobs e workers pelo Laravel Horizon, acessando a URL:
  
  ```bash
  http://localhost:8282/horizon
  ``` 
  
  **Observação**: A rota do Horizon está protegida por login, então você precisará estar autenticado para acessá-la. Veja as credenciais de login na seção a seguir.
  
  ## 2. Otimização de Consultas e Performance

### Visualizando os Pedidos

Foi implementada uma página simples onde o administrador pode visualizar e gerenciar os pedidos.

#### Acesso

```bash
http://localhost:8282/pedidos
```

### Login

Para acessar a lista de pedidos e outras funcionalidades do sistema, o usuário precisará fazer login. Um usuário admin fictício foi criado com as seguintes credenciais:

- **Email:** admin@teste.com
- **Senha:** senha123

**Observação:** As rotas do Laravel Horizon e do Laravel Telescope também estão protegidas e só podem ser acessadas após o login.

### Otimizações Implementadas

- **Eager Loading** foi utilizado para evitar o problema de N+1 queries, garantindo um carregamento eficiente dos relacionamentos, como cliente e produtos do pedido.
- **Caching** foi implementado para armazenar os pedidos dos últimos 7 dias, garantindo uma recuperação rápida de dados sem precisar consultar o banco de dados repetidamente.

### Filtros Disponíveis

Você pode filtrar os pedidos por:

- **Data:** Filtrar por data de criação do pedido (com data inicial e final).
- **Status:** Filtrar pedidos por seu status atual (ex: Aguardando pagamento, Concluído, etc.).
- **Cliente:** Filtrar pedidos por cliente, exibindo nome, total do pedido, e data de criação.

**Observação:** O cache é invalidado automaticamente sempre que um novo pedido é criado, garantindo que as informações estejam sempre atualizadas.

## 3. Tratamento de Erros e Resiliência

### Integração com API Externa de Pagamento

O sistema faz integração com uma API de pagamento externa, que foi configurada para simular falhas aleatórias.

**Endpoint:**

```http
GET http://localhost:8282/pagamento
```

### Como funciona:

- A API simula falhas com uma percentagem de erro aleatória. Quando ocorre uma falha, o sistema tenta realizar a operação novamente.
- Foi implementado um mecanismo de retry com backoff exponencial, que tenta a chamada até 5 vezes antes de considerar a operação como falha definitiva.
- Em caso de falhas repetidas, uma notificação via Slack é enviada ao canal configurado no `.env`.

### Logs e Alertas

- Todas as falhas são registradas no sistema usando o Monolog, permitindo uma análise detalhada de cada erro. Os logs podem ser visualizados em: `storage/logs/laravel.log`.
- Além disso, os logs também podem ser visualizados no Laravel Telescope.
- Notificações via Slack são configuradas para falhas críticas, ajudando a monitorar o sistema em tempo real.


## 4. Monitoramento e Métricas

### Laravel Telescope

Foi implementado o Laravel Telescope para monitoramento de jobs, filas, consultas e execuções de comandos em tempo real.

**Acesso ao Telescope:**

```bash
http://localhost:8282/telescope
```

**Observação:** Assim como o Horizon, o acesso ao Telescope está protegido por login, utilizando as mesmas credenciais de administrador mencionadas anteriormente.

### Navegação

- Links para acessar o Telescope e o Horizon foram adicionados ao cabeçalho da página de Pedidos, facilitando o acesso direto às ferramentas de monitoramento.

### Logs e Métricas

- O Telescope permite visualizar logs de exceções, comandos Artisan executados, queries, requests e muito mais, ajudando no acompanhamento do sistema em tempo real.
