# 🔧 Guia de Debug PWA - BodaPix

## ✅ Correções Implementadas

### 1. **Logs de Debug Adicionados**
Agora o console mostra informações detalhadas:
```javascript
PWA Debug: {
  hasServiceWorker: true/false,
  isSecure: true/false,
  hasManifest: true/false
}
```

### 2. **Melhor Detecção de Instalação**
- Detecta iOS Safari
- Detecta se já está instalado
- Mostra toast em vez de alert

### 3. **Manifest Atualizado**
- Adicionado campo `id: "/"`
- Todos os campos obrigatórios presentes

## 🔍 Como Debugar no Chrome Mobile

### Passo 1: Abrir DevTools no Desktop
1. No seu computador, abra Chrome
2. Conecte o telemóvel via USB
3. Ative "Depuração USB" no telemóvel
4. No Chrome desktop, vá para: `chrome://inspect`
5. Selecione o seu dispositivo
6. Clique em "Inspect" na aba do BodaPix

### Passo 2: Verificar Console
No DevTools, vá para a aba "Console" e procure por:
```
PWA Debug: { ... }
PWA: beforeinstallprompt event fired  ← IMPORTANTE!
```

Se NÃO aparecer "beforeinstallprompt event fired", significa que o Chrome não considera o site instalável ainda.

### Passo 3: Verificar Application Tab
1. Vá para aba "Application"
2. Clique em "Manifest" → Deve mostrar todos os dados
3. Clique em "Service Workers" → Deve estar "activated and running"

## 🚨 Critérios do Chrome para Mostrar o Prompt

O Chrome só dispara `beforeinstallprompt` se:

1. ✅ **HTTPS** (ou localhost)
2. ✅ **Manifest válido** com:
   - `name` ou `short_name`
   - `icons` (192px e 512px)
   - `start_url`
   - `display: standalone`
3. ✅ **Service Worker registrado**
4. ⚠️ **Engagement do usuário**:
   - Visitou o site pelo menos **2 vezes**
   - Com pelo menos **5 minutos** entre visitas
   - OU interagiu com a página (cliques, scroll)

## 🎯 Solução Imediata

### Opção 1: Forçar Instalação (Chrome Desktop)
1. Abra o site no Chrome desktop
2. Clique no ícone de instalação na barra de endereço (⊕)
3. Ou vá em Menu (⋮) → "Instalar BodaPix"

### Opção 2: Adicionar ao Ecrã (Chrome Mobile)
1. Abra o site no Chrome mobile
2. Toque no menu (⋮)
3. Procure por "Adicionar ao ecrã principal" ou "Instalar aplicativo"
4. Se não aparecer, é porque falta engagement

### Opção 3: Aumentar Engagement
Para fazer o prompt aparecer mais rápido:
1. Visite o site
2. Navegue por 2-3 páginas
3. Espere 30 segundos
4. Feche e reabra o site
5. O prompt deve aparecer

## 📱 Teste Rápido

Execute este código no console do Chrome:

```javascript
// Verificar se PWA é instalável
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.getRegistrations().then(regs => {
    console.log('Service Workers:', regs.length);
  });
}

// Verificar manifest
fetch('/manifest.json')
  .then(r => r.json())
  .then(m => console.log('Manifest:', m));

// Verificar se já instalado
console.log('Installed:', window.matchMedia('(display-mode: standalone)').matches);
```

## 🔄 Resetar Estado PWA (Se Necessário)

No Chrome mobile:
1. Configurações → Privacidade e segurança
2. Limpar dados de navegação
3. Selecione "Dados de sites" e "Imagens e arquivos em cache"
4. Limpar dados
5. Reabra o site

## ✅ Checklist de Verificação

- [ ] Site está em HTTPS (ou localhost)
- [ ] Console mostra "SW registered"
- [ ] Console mostra "PWA Debug: { ... }"
- [ ] Manifest carrega sem erros
- [ ] Ícones 192px e 512px existem
- [ ] Service Worker está ativo
- [ ] Visitou o site 2+ vezes
- [ ] Esperou 5+ minutos entre visitas

## 🎯 Resultado Esperado

Após seguir os passos:

**Se o prompt aparecer:**
```
Console: "PWA: beforeinstallprompt event fired"
Botão: "Baixar BodaPix" (clicável)
Ao clicar: Prompt nativo do Chrome
```

**Se o prompt NÃO aparecer:**
```
Console: "PWA: No install prompt available"
Botão: "Baixar BodaPix" (clicável)
Ao clicar: Toast com instruções manuais
```

## 📞 Próximos Passos

1. Abra o site no Chrome mobile
2. Abra o console (via chrome://inspect)
3. Copie e cole aqui os logs que aparecem
4. Isso vai mostrar exatamente qual é o problema

---

**Build**: ✅ Concluído (13.21s)
**Logs**: ✅ Adicionados
**Toast**: ✅ Implementado
**Manifest**: ✅ Atualizado
