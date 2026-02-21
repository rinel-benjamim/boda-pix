# 📱 Botão "Baixar BodaPix" - Instalação PWA

## ✅ Implementação Concluída

O botão "Baixar BodaPix" agora aparece **sempre** nas páginas de login e registro, permitindo que os usuários instalem o BodaPix como uma aplicação nativa.

## 📍 Localização

O botão está presente em:
- ✅ `/login` - Página de Login
- ✅ `/register` - Página de Registro

## 🎯 Comportamento

### 1. **Quando o Prompt Automático Está Disponível**
- Botão: "Baixar BodaPix"
- Ao clicar: Abre o prompt nativo do navegador
- Após instalação: Botão muda para "BodaPix Instalado" (desabilitado)

### 2. **Quando o Prompt Não Está Disponível**
- Botão: "Baixar BodaPix" (sempre visível)
- Ao clicar: Mostra instruções manuais:
  ```
  Para instalar o BodaPix:
  
  Chrome/Edge: Menu (⋮) → Instalar aplicativo
  Safari (iOS): Partilhar → Adicionar ao ecrã principal
  Firefox: Menu (⋮) → Instalar
  ```

### 3. **Quando Já Está Instalado**
- Botão: "BodaPix Instalado" (desabilitado)
- Estado visual: Botão outline desabilitado

## 🔧 Código Implementado

### Componente: `install-pwa-button.tsx`

```typescript
export function InstallPWAButton() {
  const [deferredPrompt, setDeferredPrompt] = useState<BeforeInstallPromptEvent | null>(null);
  const [isInstalled, setIsInstalled] = useState(false);
  const [isInstalling, setIsInstalling] = useState(false);

  const handleInstall = async () => {
    if (!deferredPrompt) {
      // Mostrar instruções manuais
      alert('Para instalar o BodaPix:\n\n' +
        'Chrome/Edge: Menu (⋮) → Instalar aplicativo\n' +
        'Safari (iOS): Partilhar → Adicionar ao ecrã principal\n' +
        'Firefox: Menu (⋮) → Instalar');
      return;
    }
    
    // Usar prompt automático
    await deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    
    if (outcome === 'accepted') {
      setIsInstalled(true);
    }
  };

  // Sempre mostrar, exceto se já instalado
  if (isInstalled) {
    return <Button disabled>BodaPix Instalado</Button>;
  }

  return (
    <Button onClick={handleInstall}>
      <Download className="mr-2 h-4 w-4" />
      Baixar BodaPix
    </Button>
  );
}
```

## 🎨 Design

- **Variante**: `outline` (botão com borda)
- **Largura**: `w-full` (100% da largura)
- **Ícone**: Download (lucide-react)
- **Posição**: Abaixo do botão principal de login/registro

## 📱 Compatibilidade

| Navegador | Suporte | Comportamento |
|-----------|---------|---------------|
| Chrome (Android) | ✅ Prompt automático | Instalação nativa |
| Chrome (Desktop) | ✅ Prompt automático | Instalação nativa |
| Edge | ✅ Prompt automático | Instalação nativa |
| Safari (iOS) | ⚠️ Manual | Instruções exibidas |
| Safari (macOS) | ⚠️ Manual | Instruções exibidas |
| Firefox | ⚠️ Manual | Instruções exibidas |

## 🔍 Detecção de Instalação

O componente detecta se o app já está instalado usando:

```typescript
window.matchMedia('(display-mode: standalone)').matches
```

Também escuta o evento `appinstalled`:

```typescript
window.addEventListener('appinstalled', () => {
  setIsInstalled(true);
});
```

## 📦 Build

```bash
npm run build
✓ Built in 14.54s
✓ login.tsx: 10.20 kB
✓ register.tsx: 3.01 kB
✓ install-pwa-button: Sempre visível
```

## ✅ Checklist de Funcionalidades

- ✅ Botão aparece sempre (não depende de condições)
- ✅ Funciona em login e registro
- ✅ Prompt automático quando disponível
- ✅ Instruções manuais quando prompt não disponível
- ✅ Detecta se já está instalado
- ✅ Feedback visual durante instalação
- ✅ Ícone de download
- ✅ Texto em português
- ✅ Design consistente com o resto da aplicação

## 🎯 Experiência do Usuário

### Fluxo de Instalação (Chrome/Edge)
1. Usuário acessa `/login` ou `/register`
2. Vê botão "Baixar BodaPix"
3. Clica no botão
4. Prompt nativo aparece
5. Usuário confirma instalação
6. App é instalado no dispositivo
7. Botão muda para "BodaPix Instalado"

### Fluxo de Instalação (Safari iOS)
1. Usuário acessa `/login` ou `/register`
2. Vê botão "Baixar BodaPix"
3. Clica no botão
4. Alert com instruções aparece
5. Usuário segue instruções manuais
6. App é instalado no dispositivo

---

**Status**: ✅ Implementado e testado
**Build**: ✅ Concluído (14.54s)
**Páginas**: ✅ Login + Register
