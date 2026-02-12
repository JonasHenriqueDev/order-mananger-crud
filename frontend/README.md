# React + TypeScript + Vite

A aplicação estará disponível em: `http://localhost:5173`

## Build para Produção

```bash
npm run build
```

## Estrutura

```
src/
├── pages/          # Páginas da aplicação
│   └── Login.tsx   # Tela de login
├── services/       # Configuração de APIs
│   └── api.ts      # Cliente Axios
├── types/          # Tipos TypeScript
│   └── auth.ts     # Tipos de autenticação
├── App.tsx         # Componente principal e rotas
└── main.tsx        # Entry point
```

## Funcionalidades Implementadas

- [x] Tela de login com email e senha
- [x] Validação de formulário
- [x] Tratamento de erros
- [x] Loading states
- [x] Integração com API Laravel
- [x] Armazenamento de token
- [x] Roteamento com React Router

## Credenciais de Teste

Após executar o seeder do backend, você pode usar:

```
Email: admin@example.com
Senha: password
```

## Configuração da API

A URL da API está configurada em `src/services/api.ts`:

```typescript
baseURL: "http://localhost:8000/api"
```

Certifique-se de que o backend está rodando na porta 8000.
import reactDom from 'eslint-plugin-react-dom'

export default defineConfig([
  globalIgnores(['dist']),
  {
    files: ['**/*.{ts,tsx}'],
    extends: [
      // Other configs...
      // Enable lint rules for React
      reactX.configs['recommended-typescript'],
      // Enable lint rules for React DOM
      reactDom.configs.recommended,
    ],
    languageOptions: {
      parserOptions: {
        project: ['./tsconfig.node.json', './tsconfig.app.json'],
        tsconfigRootDir: import.meta.dirname,
      },
      // other options...
    },
  },
])
```
