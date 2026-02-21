import { Download } from 'lucide-react';
import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';

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
    if (window.matchMedia('(display-mode: standalone)').matches) {
      setIsInstalled(true);
      return;
    }

    const handler = (e: Event) => {
      e.preventDefault();
      setDeferredPrompt(e as BeforeInstallPromptEvent);
    };

    window.addEventListener('beforeinstallprompt', handler);
    
    window.addEventListener('appinstalled', () => {
      setIsInstalled(true);
      setDeferredPrompt(null);
    });

    return () => {
      window.removeEventListener('beforeinstallprompt', handler);
    };
  }, []);

  const handleInstall = async () => {
    if (!deferredPrompt) {
      // Se não houver prompt, mostrar instruções
      alert('Para instalar o BodaPix:\n\n' +
        'Chrome/Edge: Menu (⋮) → Instalar aplicativo\n' +
        'Safari (iOS): Partilhar → Adicionar ao ecrã principal\n' +
        'Firefox: Menu (⋮) → Instalar');
      return;
    }
    
    setIsInstalling(true);
    
    try {
      await deferredPrompt.prompt();
      const { outcome } = await deferredPrompt.userChoice;
      
      if (outcome === 'accepted') {
        setIsInstalled(true);
      }
      
      setDeferredPrompt(null);
    } catch (error) {
      console.error('Install error:', error);
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
