# 🔧 Solução: PWA Precisa de HTTPS

## ❌ Problema Identificado

```
isSecure: false  ← ESTE É O PROBLEMA!
```

**PWA só funciona em HTTPS** (ou localhost no desktop). Você está usando HTTP no telemóvel.

## ✅ Soluções

### **Opção 1: Usar Ngrok (Mais Fácil)**

1. **Instalar Ngrok**
```bash
# No seu computador
npm install -g ngrok
```

2. **Iniciar o servidor Laravel**
```bash
php artisan serve
```

3. **Criar túnel HTTPS**
```bash
ngrok http 8000
```

4. **Usar a URL HTTPS no telemóvel**
```
Ngrok vai mostrar algo como:
https://abc123.ngrok.io → http://localhost:8000

Use https://abc123.ngrok.io no telemóvel!
```

### **Opção 2: Usar Valet (macOS/Linux)**

```bash
# Instalar Valet
composer global require laravel/valet
valet install

# No diretório do projeto
valet link bodapix
valet secure bodapix

# Acesse: https://bodapix.test
```

### **Opção 3: Usar Codespaces/GitHub (Você está usando!)**

Se está no GitHub Codespaces:

1. **Tornar a porta pública**
   - Vá para aba "PORTS"
   - Clique com botão direito na porta 8000
   - Selecione "Port Visibility" → "Public"

2. **Usar a URL HTTPS**
   - Copie a URL que aparece (já é HTTPS!)
   - Exemplo: `https://abc-8000.preview.app.github.dev`

3. **Atualizar .env**
```bash
APP_URL=https://sua-url-codespaces.github.dev
```

4. **Reiniciar servidor**
```bash
php artisan config:clear
php artisan serve
```

### **Opção 4: Deploy em Produção**

Deploy no Vercel, Netlify, ou qualquer host com HTTPS automático.

## 🚀 Teste Rápido (Ngrok)

```bash
# Terminal 1: Servidor Laravel
composer run dev

# Terminal 2: Ngrok
ngrok http 8000

# Copie a URL HTTPS que aparece
# Exemplo: https://abc123.ngrok-free.app

# Abra no telemóvel
# Agora o botão "Baixar BodaPix" vai funcionar!
```

## ✅ Após Configurar HTTPS

Você verá no console:
```javascript
PWA Debug: {
  hasServiceWorker: true,
  isSecure: true,  ← AGORA SIM!
  hasManifest: true
}
```

E após visitar 2x com 5min de intervalo:
```javascript
PWA: beforeinstallprompt event fired  ← SUCESSO!
```

## 📱 Instalação Manual (Enquanto Isso)

Mesmo sem o prompt automático, você pode instalar manualmente:

**Chrome Mobile:**
1. Menu (⋮)
2. "Adicionar ao ecrã principal"
3. Confirmar

**Isso funciona mesmo em HTTP!**

## 🔄 Limpar Cache do Service Worker

Se ainda tiver erros após corrigir:

1. Chrome → `chrome://serviceworker-internals`
2. Encontre "bodapix"
3. Clique em "Unregister"
4. Recarregue a página

---

**Resumo:**
- ❌ HTTP não funciona para PWA prompt automático
- ✅ HTTPS é obrigatório
- ✅ Use Ngrok para desenvolvimento
- ✅ Instalação manual funciona em HTTP
