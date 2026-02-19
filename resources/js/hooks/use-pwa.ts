import { useEffect, useState } from 'react';

interface BeforeInstallPromptEvent extends Event {
  prompt: () => Promise<void>;
  userChoice: Promise<{ outcome: 'accepted' | 'dismissed' }>;
}

export function usePWA() {
  const [deferredPrompt, setDeferredPrompt] = useState<BeforeInstallPromptEvent | null>(null);

  useEffect(() => {
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js').catch((error) => {
        console.error('Service Worker registration failed:', error);
      });
    }

    const handler = (e: Event) => {
      e.preventDefault();
      setDeferredPrompt(e as BeforeInstallPromptEvent);
    };

    window.addEventListener('beforeinstallprompt', handler);
    return () => window.removeEventListener('beforeinstallprompt', handler);
  }, []);

  const installPWA = async () => {
    try {
      if (deferredPrompt) {
        await deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        console.log('PWA install outcome:', outcome);
        setDeferredPrompt(null);
      } else {
        // Fallback: tentar abrir instruções ou mostrar mensagem
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches;
        
        if (isStandalone) {
          alert('A aplicação já está instalada!');
        } else if (isIOS) {
          alert('Para instalar no iOS:\n1. Toque no botão Partilhar\n2. Selecione "Adicionar ao Ecrã Principal"');
        } else {
          alert('Para instalar:\n1. Clique no menu do navegador (⋮)\n2. Selecione "Instalar aplicação" ou "Adicionar ao ecrã inicial"');
        }
      }
    } catch (error) {
      console.error('PWA install error:', error);
    }
  };

  return { installPWA };
}
