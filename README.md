# Plataforma de Gestão de Pedidos e Estoque (SaaS)

## Visão Geral

Este projeto é uma aplicação fullstack desenvolvida com Laravel no
backend e React + Vite no frontend, simulando um sistema SaaS real de
gestão de pedidos e estoque.

O objetivo do projeto é demonstrar domínio prático de conceitos e
tecnologias amplamente exigidos em vagas Full Stack e Backend, como APIs
REST, autenticação, filas, cache, testes automatizados, documentação de
API, Docker e Kubernetes.

O sistema foi projetado com foco em boas práticas, arquitetura limpa e
preparação para ambiente de produção.

## Funcionalidades Principais

### Autenticação e Autorização

-   Autenticação via API usando Laravel Sanctum
-   Controle de acesso baseado em roles (Admin, Manager, User)
-   Proteção de rotas com middleware
-   Sessão stateless

### Gestão de Usuários

-   Criação e gerenciamento de usuários
-   Atribuição de permissões
-   Ativação e desativação de contas

### Gestão de Produtos

-   CRUD completo de produtos
-   Controle de estoque
-   Cache da listagem de produtos utilizando Redis
-   Invalidação automática de cache em alterações

### Gestão de Pedidos

-   Criação de pedidos com múltiplos itens
-   Cálculo automático do valor total
-   Validação de estoque disponível
-   Controle de status (pending, processing, completed, cancelled)

### Processamento Assíncrono

-   Processamento de pedidos em background utilizando filas
-   Atualização de estoque via jobs
-   Retentativas automáticas em caso de falha
-   Separação entre API e worker

### Dashboard

-   Indicadores principais (KPIs):
    -   Total de pedidos
    -   Faturamento
    -   Produtos com estoque baixo
-   Dados cacheados para melhor performance

### Documentação da API

-   Documentação automática baseada em OpenAPI
-   Endpoints versionados
-   Exemplos de requests e responses
-   Fluxo de autenticação documentado

## Stack Tecnológica

### Backend

-   PHP 8.2
-   Laravel 11
-   Laravel Sanctum
-   PostgreSQL
-   Redis (cache e filas)
-   Pest / PHPUnit
-   Scribe ou Swagger (OpenAPI)

### Frontend

-   React 18
-   Vite
-   TypeScript
-   React Query (TanStack Query)
-   Axios
-   Tailwind CSS
-   Zod para validações

### Infraestrutura

-   Docker
-   Docker Compose
-   Nginx
-   Kubernetes (minikube ou k3s)

## Testes Automatizados

O projeto inclui testes automatizados cobrindo os fluxos críticos, como
autenticação, autorização, criação e processamento de pedidos e execução
de jobs em fila.

## Como Executar o Projeto

1.  Clone o repositório
2.  Copie o arquivo .env.example para .env
3.  Execute docker-compose up -d
4.  Rode as migrations
5.  Inicie o worker de filas

## Licença

Projeto de uso educacional e demonstração técnica.
