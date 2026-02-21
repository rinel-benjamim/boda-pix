import { Download } from 'lucide-react';
import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';

interface BeforeInstallPromptEvent extends Event {
  prompt: () => Promise<void>;
  userChoice: Promise<{ outcome: 'accepted' | 'dismissed' }>;
}

export function InstallPWAButton() {
  const [deferredPrompt, setDeferredPrompt] = useState<BeforeInstallPromptEvent | null>(null);
  const [isInstalled, setIsInstalled] = useState(false);
  const [isInstalling, setIsInstalling] = useState(false);

  useEffect(() => {
    // Check if already installed
    if (window.matchMedia('(display-mode: standalone)').matches || 
        (window.navigator as any).standalone === true) {
      console.log('PWA: Already installed');
      setIsInstalled(true);
      return;
    }

    const handler = (e: Event) => {
      console.log('PWA: beforeinstallprompt event fired');
      e.preventDefault();
      setDeferredPrompt(e as BeforeInstallPromptEvent);
    };

    window.addEventListener('beforeinstallprompt', handler);
    
    window.addEventListener('appinstalled', () => {
      console.log('PWA: App installed');
      setIsInstalled(true);
      setDeferredPrompt(null);
      toast.success('BodaPix instalado com sucesso!');
    });

    // Debug: Check PWA requirements
    console.log('PWA Debug:', {
      hasServiceWorker: 'serviceWorker' in navigator,
      isSecure: window.location.protocol === 'https:' || window.location.hostname === 'localhost',
      hasManifest: document.querySelector('link[rel="manifest"]') !== null,
    });

    return () => {
      window.removeEventListener('beforeinstallprompt', handler);
    };
  }, []);

  const handleInstall = async () => {
    if (!deferredPrompt) {
      console.log('PWA: No install prompt available');
      
      // Verificar se é iOS Safari
      const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
      const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
      
      if (isIOS || isSafari) {
        toast.info('Para instalar no Safari:\n1. Toque no botão Partilhar\n2. Selecione "Adicionar ao Ecrã Principal"', {
          duration: 8000,
        });
      } else {
        toast.info('Para instalar:\n1. Abra o menu do navegador (⋮)\n2. Selecione "Instalar aplicativo" ou "Adicionar ao ecrã"', {
          duration: 8000,
        });
      }
      return;
    }
    
    setIsInstalling(true);
    
    try {
      console.log('PWA: Showing install prompt');
      await deferredPrompt.prompt();
      const { outcome } = await deferredPrompt.userChoice;
      
      console.log('PWA: User choice:', outcome);
      
      if (outcome === 'accepted') {
        setIsInstalled(true);
        toast.success('BodaPix instalado com sucesso!');
      } else {
        toast.info('Instalação cancelada');
      }
      
      setDeferredPrompt(null);
    } catch (error) {
      console.error('PWA: Install error:', error);
      toast.error('Erro ao instalar. Tente pelo menu do navegador.');
    } finally {
      setIsInstalling(false);
    }
  };

  // Mostrar sempre, exceto se já instalado
  if (isInstalled) {
    return (
      <Button
        type="button"
        variant="outline"
        className="w-full"
        disabled
      >
        <Download className="mr-2 h-4 w-4" />
        BodaPix Instalado
      </Button>
    );
  }

  return (
    <Button
      type="button"
      variant="outline"
      className="w-full"
      onClick={handleInstall}
      disabled={isInstalling}
    >
      <Download className="mr-2 h-4 w-4" />
      {isInstalling ? 'A instalar...' : 'Baixar BodaPix'}
    </Button>
  );
}
