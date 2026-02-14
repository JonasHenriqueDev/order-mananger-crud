# Sistema de Gestão de Pedidos e Estoque

Sistema backend desenvolvido com Laravel para gerenciamento completo de pedidos, produtos e usuários, com processamento assíncrono via filas e autenticação baseada em tokens.

OBS: O frontend ainda será desenvolvido neste mesmo repositório.

## Arquitetura do Sistema

```
┌──────────────┐
│   Cliente    │
│  (Frontend)  │
└──────┬───────┘
       │ HTTP Request
       ↓
┌──────────────────────────────────────────┐
│            Nginx (Port 8000)             │
└──────────────┬───────────────────────────┘
               │
               ↓
┌──────────────────────────────────────────┐
│         Laravel Application              │
│  ┌────────────────────────────────┐     │
│  │  Controllers & Middleware      │     │
│  │  (auth:sanctum, role)          │     │
│  └──────────┬─────────────────────┘     │
│             │                            │
│  ┌──────────▼─────────────────────┐     │
│  │   Business Logic & Models      │     │
│  │   (User, Order, Product)       │     │
│  └──────┬──────────────┬──────────┘     │
│         │              │                 │
└─────────┼──────────────┼─────────────────┘
          │              │
    ┌─────▼────┐   ┌────▼──────┐
    │PostgreSQL│   │   Redis   │
    │ Database │   │  (Queue)  │
    └──────────┘   └─────┬─────┘
                         │
                   ┌─────▼─────────┐
                   │ Horizon Worker│
                   │  (Background) │
                   └───────────────┘
```

## Fluxo de Processamento de Pedidos

```
Cliente cria pedido
      │
      ↓
[OrderController::store]
      │
      ├─→ Valida estoque disponível
      ├─→ Calcula valor total
      ├─→ Salva pedido (status: pending)
      │
      ↓
[Event: OrderCreated]
      │
      ↓
[Listener: DispatchProcessOrderJob]
      │
      ↓
[Job Queue: ProcessOrderJob]
      │
      ├─→ Atualiza status para 'processing'
      ├─→ Dispara AdjustStockJob
      │
      ↓
[Job: AdjustStockJob]
      │
      ├─→ Reduz estoque dos produtos
      ├─→ Dispara OrderStatusChanged
      │
      ↓
[Job: CompleteOrderJob]
      │
      └─→ Marca pedido como 'completed'
```

## Stack Tecnológica

### Backend
- PHP 8.2
- Laravel 12
- Laravel Sanctum (autenticação stateless)
- Laravel Horizon (gerenciamento de filas)
- PostgreSQL 16
- Redis 7

### Frontend
- ReactJS
- Vite
- Tailwind CSS
- Axios

### Infraestrutura
- Docker & Docker Compose
- Nginx Alpine
- Pest (testes automatizados)

## Funcionalidades

### Backend

#### Autenticação e Autorização
- [x] API REST stateless com Laravel Sanctum
- [x] Sistema de roles (admin, manager, cliente)
- [x] Middleware de proteção de rotas por permissão
- [x] Controle de usuários ativos/inativos
- [x] Registro de novos usuários
- [x] Login com geração de token

#### Gestão de Usuários
- [x] CRUD completo de usuários
- [x] Atribuição de roles
- [x] Vinculação de telefones múltiplos
- [x] Vinculação de endereços múltiplos
- [x] Ativação e desativação de contas
- [x] Listagem com filtros

#### Gestão de Produtos
- [x] CRUD completo de produtos
- [x] Controle de estoque
- [x] Soft delete (exclusão lógica)
- [x] Restauração de produtos excluídos
- [x] Exclusão permanente
- [x] Validação de preço e quantidade

#### Gestão de Pedidos
- [x] Criação de pedidos com múltiplos itens
- [x] Validação de estoque disponível
- [x] Cálculo automático de valores
- [x] Controle de status (pending, processing, completed, cancelled)
- [x] Cancelamento de pedidos
- [x] Listagem de pedidos do usuário
- [x] Listagem completa para admin/manager

#### Processamento Assíncrono
- [x] Eventos e listeners para desacoplamento
- [x] Jobs para processamento em background
- [x] ProcessOrderJob (processamento inicial)
- [x] AdjustStockJob (ajuste de estoque)
- [x] CompleteOrderJob (finalização)
- [x] Fila Redis com Laravel Horizon
- [x] Retentativas automáticas em caso de falha
- [x] Interface web de monitoramento (Horizon)

#### Infraestrutura
- [x] Docker e Docker Compose configurados
- [x] PostgreSQL 16 como banco de dados
- [x] Redis para cache e filas
- [x] Nginx como servidor web
- [x] Testes automatizados com Pest
- [x] Migrations e seeders
- [x] Factories para testes

### Frontend

#### Autenticação
- [ ] Tela de login
- [ ] Tela de registro
- [ ] Recuperação de senha
- [ ] Proteção de rotas privadas
- [ ] Armazenamento seguro de token
- [ ] Logout

#### Dashboard
- [ ] Visão geral do sistema
- [ ] Indicadores (total de pedidos, faturamento)
- [ ] Gráficos de vendas
- [ ] Produtos com estoque baixo
- [ ] Últimos pedidos

#### Gestão de Usuários
- [ ] Listagem de usuários
- [ ] Formulário de cadastro
- [ ] Formulário de edição
- [ ] Ativação/desativação de contas
- [ ] Gerenciamento de telefones
- [ ] Gerenciamento de endereços
- [ ] Filtros e busca

#### Gestão de Produtos
- [ ] Listagem de produtos
- [ ] Formulário de cadastro
- [ ] Formulário de edição
- [ ] Controle de estoque
- [ ] Upload de imagens
- [ ] Soft delete e restauração
- [ ] Filtros e busca

#### Gestão de Pedidos
- [ ] Listagem de pedidos
- [ ] Formulário de criação de pedido
- [ ] Seleção de produtos com validação de estoque
- [ ] Cálculo automático de totais
- [ ] Visualização de detalhes do pedido
- [ ] Cancelamento de pedidos
- [ ] Filtros por status e data
- [ ] Área "Meus Pedidos" para clientes

#### Interface e Experiência
- [ ] Design responsivo
- [ ] Tema claro/escuro
- [ ] Notificações toast
- [ ] Tratamento de erros
- [ ] Validação de formulários
- [ ] Paginação

## Instalação e Execução

### Requisitos
- Docker
- Docker Compose
- Git

### Configuração Inicial

Clone o repositório:
```bash
git clone <repository-url>
cd order-mananger/backend
```

Configure as variáveis de ambiente:
```bash
cp .env.example .env
```

Edite o arquivo `.env` com as seguintes configurações:
```env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PORT=6379

CACHE_DRIVER=redis
SESSION_DRIVER=database
```

### Iniciar o Ambiente Docker

Clone o repositório e execute:

``` bash
make up
```

### Acessar a Aplicação

O sistema estará disponível em: `http://localhost:8000`

Horizon: `http://localhost:8000/horizon`

### Comandos Úteis

Subir containers

``` bash
make up
```

Parar containers

``` bash
make down
```

Reiniciar tudo (rebuild)

``` bash
make restart
```

Rebuild manual

``` bash
make build
```

Ver logs

``` bash
make logs
```

Acessar container da aplicação

``` bash
make bash
```

Executar testes

``` bash
cd backend
docker compose exec app php artisan test
```

ou dentro do container:

``` bash
php artisan test
```

Executar Horizon manualmente

``` bash
make horizon
```


## Estrutura da API

### Autenticação
- `POST /api/register` - Registrar novo usuário
- `POST /api/login` - Login e obtenção de token
- `GET /api/me` - Dados do usuário autenticado

### Usuários (Admin/Manager)
- `GET /api/users` - Listar usuários
- `POST /api/users` - Criar usuário
- `GET /api/users/{id}` - Detalhes do usuário
- `PUT /api/users/{id}` - Atualizar usuário
- `PATCH /api/users/{id}/toggle` - Ativar/desativar

### Produtos (Admin/Manager)
- `GET /api/products` - Listar produtos
- `POST /api/products` - Criar produto
- `GET /api/products/{id}` - Detalhes do produto
- `PUT /api/products/{id}` - Atualizar produto
- `DELETE /api/products/{id}` - Soft delete
- `POST /api/products/{id}/restore` - Restaurar produto
- `DELETE /api/products/{id}/force` - Excluir permanentemente

### Pedidos (Admin/Manager)
- `GET /api/orders` - Listar todos os pedidos
- `POST /api/orders` - Criar pedido
- `GET /api/orders/{id}` - Detalhes do pedido
- `PUT /api/orders/{id}` - Atualizar pedido
- `DELETE /api/orders/{id}` - Excluir pedido
- `POST /api/orders/{id}/cancel` - Cancelar pedido
- `GET /api/my-orders` - Pedidos do usuário autenticado

### Endereços e Telefones (Admin/Manager)
- `GET /api/addresses` - Listar endereços
- `POST /api/addresses` - Criar endereço
- `GET /api/users/{id}/addresses` - Endereços do usuário
- `GET /api/phones` - Listar telefones
- `POST /api/phones` - Criar telefone
- `GET /api/users/{id}/phones` - Telefones do usuário

## Testes

Execute a suite completa de testes:
```bash
docker compose exec app php artisan test
```

Executar testes com cobertura:
```bash
docker compose exec app php artisan test --coverage
```

Executar testes específicos:
```bash
docker compose exec app php artisan test --filter=OrderTest
```

## Observações Técnicas

- O sistema utiliza eventos e listeners para desacoplamento entre camadas
- Jobs são processados via Redis para melhor performance
- O Laravel Horizon oferece interface web para monitoramento de filas
- Soft deletes permitem recuperação de produtos excluídos
- Validação de estoque ocorre antes da criação do pedido
- Tokens de autenticação não expiram (ideal para desenvolvimento)

## Licença

Projeto desenvolvido para fins educacionais e demonstração técnica.
