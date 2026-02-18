# BodaPix

Plataforma angolana de partilha privada de fotos e vídeos para eventos sociais (casamentos, festas, aniversários, batizados, etc.).

## 🚀 Stack Tecnológica

### Backend
- Laravel 11+
- PostgreSQL (Supabase)
- Laravel Sanctum (API Authentication)
- Clean Architecture
- Service Layer Pattern
- Form Requests
- Policies
- Jobs/Queues

### Frontend
- React 19 + TypeScript
- Vite
- Inertia.js
- shadcn/ui
- TailwindCSS 4
- Mobile-first & Responsive

### Storage
- Supabase S3 Compatible Storage

### PWA
- Service Worker
- Manifest.json
- Instalável em mobile e desktop

## 📦 Instalação

### Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL

### Passos

1. **Clone o repositório**
```bash
git clone <repo-url>
cd boda-pix
```

2. **Instale dependências**
```bash
composer install
npm install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure as variáveis de ambiente no .env**
```env
DB_CONNECTION=pgsql
DB_HOST=your-supabase-host.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=your-db-username
DB_PASSWORD=your-db-password

AWS_ACCESS_KEY_ID=your-access-key-id
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_BUCKET=your-bucket-name
AWS_ENDPOINT=https://your-project.supabase.co/storage/v1/s3

SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=your-anon-key
SUPABASE_SERVICE_ROLE_KEY=your-service-role-key

FILESYSTEM_DISK=s3
QUEUE_CONNECTION=database
```

5. **Execute as migrations**
```bash
php artisan migrate
```

6. **Compile os assets**
```bash
npm run build
```

7. **Inicie o servidor**
```bash
composer run dev
```

Acesse: http://localhost:8000

## 🎯 Funcionalidades

### Autenticação
- ✅ Login
- ✅ Registo
- ✅ Logout
- ✅ Proteção de rotas
- ✅ Tokens seguros (Sanctum)

### Eventos
- ✅ Criar evento
- ✅ Editar evento
- ✅ Deletar evento
- ✅ Gerar código de convite
- ✅ Controlar permissões (Admin/Participante)
- ✅ Entrar em evento via código

### Upload de Mídia
- ✅ Upload direto para S3 Supabase
- ✅ Suporte a imagens e vídeos
- ✅ Upload múltiplo
- ✅ Validação de tipo e tamanho
- ✅ Geração automática de thumbnails (via Job)

### Interface
- ✅ Mobile-first
- ✅ Totalmente responsiva
- ✅ Bottom navigation no mobile
- ✅ Sidebar no desktop
- ✅ Dark mode
- ✅ Toast notifications
- ✅ Skeleton loading

### PWA
- ✅ Instalável
- ✅ Service Worker
- ✅ Offline fallback
- ✅ Botão de instalação nas páginas de auth

## 📂 Estrutura do Projeto

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── EventController.php
│   │       └── MediaController.php
│   ├── Requests/
│   │   ├── StoreEventRequest.php
│   │   ├── UpdateEventRequest.php
│   │   └── UploadMediaRequest.php
│   └── Resources/
│       ├── EventResource.php
│       └── MediaResource.php
├── Jobs/
│   └── GenerateThumbnailJob.php
├── Models/
│   ├── Event.php
│   ├── Media.php
│   └── User.php
├── Policies/
│   └── EventPolicy.php
└── Services/
    ├── EventService.php
    └── MediaService.php

resources/js/
├── components/
│   ├── ui/
│   └── bottom-nav.tsx
├── hooks/
│   └── use-pwa.ts
├── pages/
│   ├── auth/
│   │   ├── login.tsx
│   │   └── register.tsx
│   └── events/
│       ├── index.tsx
│       ├── create.tsx
│       └── show.tsx
└── types/
    └── event.ts
```

## 🔐 API Endpoints

### Autenticação
```
POST   /login
POST   /register
POST   /logout
```

### Eventos
```
GET    /api/events              - Listar eventos do usuário
POST   /api/events              - Criar evento
GET    /api/events/{id}         - Ver evento
PUT    /api/events/{id}         - Atualizar evento
DELETE /api/events/{id}         - Deletar evento
POST   /api/events/join         - Entrar via código
```

### Mídia
```
GET    /api/events/{id}/media   - Listar mídia do evento
POST   /api/events/{id}/media   - Upload de mídia
DELETE /api/media/{id}          - Deletar mídia
```

## 🎨 Paleta de Cores

```css
Primary: #FF5A1F (laranja vibrante)
Secondary: #E11D48 (rosa avermelhado)
Background: #0F172A
Card: #1E293B
Muted: #64748B
Accent gradient: linear-gradient(135deg, #FF5A1F, #E11D48)
```

## 🧪 Testes

```bash
php artisan test
```

## 📱 PWA

O BodaPix é uma Progressive Web App instalável:

1. Acesse a aplicação no navegador
2. Clique no botão "Instalar BodaPix" (Login/Signup)
3. Ou use o menu do navegador para instalar

## 🚀 Deploy

### Preparação
```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Queue Worker
```bash
php artisan queue:work --tries=3
```

## 📄 Licença

MIT

## 👨‍💻 Desenvolvido com ❤️ em Angola
