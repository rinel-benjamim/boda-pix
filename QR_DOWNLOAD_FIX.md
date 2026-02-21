# 🔧 Correção do Download do QR Code

## 🐛 Problema Identificado

Ao clicar em "Baixar QR Code":
- ❌ Erro: "Erro ao baixar QR Code"
- ❌ Download não funcionava

### Causa Raiz
O `html2canvas` tem problemas ao renderizar elementos dentro de Dialogs/Modals do Radix UI, especialmente com SVGs. A biblioteca tenta capturar o DOM mas falha com elementos posicionados de forma absoluta ou com z-index alto.

## ✅ Solução Implementada

Substituir `html2canvas` por conversão **nativa** de SVG para Canvas usando APIs do navegador:

### Antes (❌ com html2canvas)
```typescript
const canvas = await html2canvas(qrRef.current, {
  backgroundColor: '#ffffff',
  scale: 2,
});
```

### Agora (✅ nativo)
```typescript
// 1. Encontrar o SVG
const svg = qrRef.current.querySelector('svg');

// 2. Serializar SVG
const svgData = new XMLSerializer().serializeToString(svg);
const svgBlob = new Blob([svgData], { type: 'image/svg+xml' });
const url = URL.createObjectURL(svgBlob);

// 3. Converter para imagem
const img = new Image();
img.onload = () => {
  ctx.drawImage(img, 0, 0, size, size);
  // Adicionar texto
  ctx.fillText(event.name, size / 2, size + 35);
  // Download
  link.href = canvas.toDataURL('image/png');
  link.click();
};
img.src = url;
```

## 📝 Vantagens da Nova Abordagem

1. ✅ **Sem dependências externas** - Não precisa de html2canvas
2. ✅ **Mais rápido** - Conversão direta SVG → Canvas
3. ✅ **Mais confiável** - Funciona em Dialogs/Modals
4. ✅ **Melhor qualidade** - Controle total sobre o canvas
5. ✅ **Menor bundle** - Removemos ~200KB do html2canvas

## 🎨 Resultado do Download

O QR Code baixado contém:
- ✅ QR Code em alta qualidade (400x400px)
- ✅ Nome do evento abaixo do QR
- ✅ Fundo branco
- ✅ Formato PNG

## 📦 Build

```bash
npm run build
✓ Built in 13.95s
✓ Bundle reduzido (sem html2canvas)
✓ show.tsx: 26.20 kB (antes: 226.70 kB)
```

## 🧪 Como Testar

1. Login no sistema
2. Abrir um evento
3. Clicar no botão de compartilhar (Share2)
4. Ir para aba "QR Code"
5. Clicar em "Baixar QR Code"
6. ✅ Arquivo PNG deve ser baixado com sucesso

## 🔍 Tratamento de Erros

A função agora tem tratamento robusto de erros:

```typescript
if (!qrRef.current) {
  toast.error('Erro ao baixar QR Code');
  return;
}

const svg = qrRef.current.querySelector('svg');
if (!svg) {
  toast.error('QR Code não encontrado');
  return;
}

const ctx = canvas.getContext('2d');
if (!ctx) {
  toast.error('Erro ao criar canvas');
  return;
}

img.onerror = () => {
  toast.error('Erro ao processar QR Code');
};
```

## 📊 Comparação

| Aspecto | html2canvas | Nativo |
|---------|-------------|--------|
| Tamanho | ~200KB | 0KB |
| Velocidade | Lento | Rápido |
| Confiabilidade | Problemas com Modals | ✅ Funciona |
| Qualidade | Boa | Excelente |
| Controle | Limitado | Total |

---

**Status**: ✅ Corrigido e testado
**Build**: ✅ Concluído (13.95s)
**Bundle**: ✅ Reduzido (-200KB)
