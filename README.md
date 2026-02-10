# Sistema de Gestão de Pedidos e Estoque

Sistema backend desenvolvido com Laravel para gerenciamento completo de pedidos, produtos e usuários, com processamento assíncrono via filas e autenticação baseada em tokens.

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

### Infraestrutura
- Docker & Docker Compose
- Nginx Alpine
- Pest (testes automatizados)

## Funcionalidades

### Autenticação e Autorização
- API REST stateless com Laravel Sanctum
- Sistema de roles: admin, manager e cliente
- Middleware de proteção de rotas por permissão
- Controle de usuários ativos/inativos

### Gestão de Recursos
- **Usuários**: CRUD completo com roles, telefones e endereços
- **Produtos**: controle de estoque, soft delete, preços
- **Pedidos**: criação com múltiplos itens, validação de estoque, cálculo automático de totais

### Processamento Assíncrono
- Eventos e listeners para desacoplamento
- Jobs para processamento em background (ProcessOrderJob, AdjustStockJob, CompleteOrderJob)
- Fila Redis com Laravel Horizon
- Retentativas automáticas em caso de falha

### Estados de Pedido
- `pending`: aguardando processamento
- `processing`: ajustando estoque
- `completed`: finalizado com sucesso
- `cancelled`: cancelado pelo usuário ou sistema

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

Suba todos os containers:
```bash
docker compose up -d
```

Instale as dependências do Laravel:
```bash
docker compose exec app composer install
```

Gere a chave da aplicação:
```bash
docker compose exec app php artisan key:generate
```

Execute as migrations e seeders:
```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

### Acessar a Aplicação

O sistema estará disponível em: `http://localhost:8000`

### Comandos Úteis

Ver logs dos containers:
```bash
docker compose logs -f
```

Ver logs apenas do worker:
```bash
docker compose logs -f worker
```

Acessar o container da aplicação:
```bash
docker compose exec app bash
```

Executar testes:
```bash
docker compose exec app php artisan test
```

Acessar o Horizon (gerenciamento de filas):
```bash
# Acesse no navegador: http://localhost:8000/horizon
```

Parar os containers:
```bash
docker compose down
```

Limpar volumes e recomeçar:
```bash
docker compose down -v
docker compose up -d
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
