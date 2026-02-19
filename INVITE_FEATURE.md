# Feature: Sistema de Convite para Eventos

## 📋 Resumo

Implementação completa do sistema de convite para eventos com suporte a:
- QR Code
- Código de acesso
- Link de convite
- Página dedicada para entrar em eventos

## ✅ Funcionalidades Implementadas

### 1. Dialog de Convite no Evento (show.tsx)
- **3 Tabs**: QR Code, Código, Link
- **QR Code**: Gerado automaticamente com o link do evento
- **Código**: Exibição do código de 8 caracteres com botão copiar
- **Link**: URL completo do convite com botões para copiar e partilhar (Web Share API)
- **Partilha Nativa**: Suporte para Web Share API em dispositivos móveis

### 2. Página de Entrar em Evento (/events/join)
- **2 Tabs**: Código e QR Code
- **Input de Código**: Campo formatado para código de 8 caracteres (uppercase automático)
- **Scanner QR**: Placeholder para futura implementação de scanner
- **Validação**: Feedback de erros em tempo real
- **Redirecionamento**: Após sucesso, redireciona para o evento

### 3. Botão "Entrar" na Lista de Eventos
- Adicionado botão "Entrar" ao lado de "Criar" na página de eventos
- Acesso rápido à página de join

## 🛠️ Componentes Criados/Modificados

### Novos Arquivos
1. `resources/js/components/ui/tabs.tsx` - Componente Tabs do shadcn/ui
2. `resources/js/pages/events/join.tsx` - Página para entrar em eventos
3. `tests/Feature/EventInviteTest.php` - Testes da funcionalidade

### Arquivos Modificados
1. `resources/js/pages/events/show.tsx` - Dialog de convite com QR code
2. `resources/js/pages/events/index.tsx` - Botão "Entrar"
3. `routes/web.php` - Rota GET e POST para /events/join
4. `routes/api.php` - Mantida rota API para compatibilidade
5. `app/Http/Controllers/Api/EventController.php` - Ajuste no redirect

## 📦 Dependências Adicionadas
- `qrcode.react` - Geração de QR codes
- `@radix-ui/react-tabs` - Componente de tabs

## 🧪 Testes

### Testes Criados
```php
✓ user can access join event page
✓ user can join event via join page  
✓ join page shows error for invalid code
✓ event show page has invite link
```

### Resultado dos Testes
- **31 testes passaram** (122 assertions)
- **1 teste falhou** (não relacionado - EmailVerification)
- Todos os testes de eventos funcionando corretamente

## 🔗 Rotas

### Web
- `GET /events/join` - Página para entrar em evento
- `POST /events/join` - Processar entrada em evento (web)

### API
- `POST /api/events/join` - Processar entrada em evento (API)

## 🎨 UX/UI

### Dialog de Convite
- Design responsivo com tabs
- QR Code centralizado (200x200px, nível H de correção)
- Botões de copiar com feedback visual (toast)
- Suporte para Web Share API em mobile

### Página de Join
- Layout limpo com tabs
- Input estilizado para código (uppercase, monospace)
- Placeholder para scanner QR (desenvolvimento futuro)
- Feedback de erros inline

## 🚀 Como Usar

### Para Convidar Participantes
1. Abrir um evento
2. Clicar no botão de partilha (ícone Share2)
3. Escolher método:
   - **QR Code**: Mostrar para escanear
   - **Código**: Copiar e enviar (8 caracteres)
   - **Link**: Partilhar via apps nativos ou copiar

### Para Entrar em Evento
1. Clicar em "Entrar" na lista de eventos
2. Escolher método:
   - **Código**: Digitar código de 8 caracteres
   - **QR Code**: Escanear (futuro)
3. Submeter e ser redirecionado para o evento

## 📝 Notas Técnicas

- QR Code contém URL completo: `{APP_URL}/events/join?code={ACCESS_CODE}`
- Código de acesso é único e gerado automaticamente (8 chars uppercase)
- Web Share API com fallback para copiar
- Validação de código no backend (size:8)
- Prevenção de entrada duplicada no mesmo evento
- Suporte para API e Web (Inertia.js)

## 🔮 Melhorias Futuras

1. **Scanner QR Code**: Implementar com biblioteca de câmera
2. **Deep Links**: Suporte para abrir app diretamente do link
3. **Notificações**: Avisar criador quando alguém entra
4. **Estatísticas**: Mostrar quantas pessoas entraram via cada método
5. **Expiração**: Códigos com validade temporal
6. **Permissões**: Diferentes níveis de acesso via convite
