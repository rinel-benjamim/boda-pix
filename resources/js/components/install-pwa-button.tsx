import { Download } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { usePWA } from '@/hooks/use-pwa';

export function InstallPWAButton() {
  const { installPWA } = usePWA();
  const [isInstalling, setIsInstalling] = useState(false);

  const handleInstall = async () => {
    setIsInstalling(true);
    try {
      await installPWA();
    } finally {
      setIsInstalling(false);
    }
  };

  return (
    <Button
      type="button"
      variant="outline"
      className="w-full"
      onClick={handleInstall}
      disabled={isInstalling}
    >
      <Download className="mr-2 h-4 w-4" />
      {isInstalling ? 'A instalar...' : 'Instalar BodaPix'}
    </Button>
  );
}
