import { useEffect, useRef, useState } from 'react';
import { BrowserMultiFormatReader } from '@zxing/library';
import { Button } from '@/components/ui/button';
import { X } from 'lucide-react';

interface QRScannerProps {
  onScan: (code: string) => void;
  onClose: () => void;
}

export function QRScanner({ onScan, onClose }: QRScannerProps) {
  const videoRef = useRef<HTMLVideoElement>(null);
  const [error, setError] = useState<string>('');
  const readerRef = useRef<BrowserMultiFormatReader | null>(null);

  useEffect(() => {
    const reader = new BrowserMultiFormatReader();
    readerRef.current = reader;

    const startScanning = async () => {
      try {
        const videoInputDevices = await reader.listVideoInputDevices();
        
        if (videoInputDevices.length === 0) {
          setError('Nenhuma câmera encontrada');
          return;
        }

        // Procurar câmera traseira (environment) ou usar a última disponível
        const backCamera = videoInputDevices.find(device => 
          device.label.toLowerCase().includes('back') || 
          device.label.toLowerCase().includes('rear') ||
          device.label.toLowerCase().includes('environment')
        );
        
        const selectedDeviceId = backCamera?.deviceId || videoInputDevices[videoInputDevices.length - 1].deviceId;

        await reader.decodeFromVideoDevice(
          selectedDeviceId,
          videoRef.current!,
          (result, err) => {
            if (result) {
              const text = result.getText();
              onScan(text);
            }
          }
        );
      } catch (err) {
        setError('Erro ao acessar câmera');
        console.error(err);
      }
    };

    startScanning();

    return () => {
      reader.reset();
    };
  }, [onScan]);

  return (
    <div className="fixed inset-0 z-50 bg-black">
      <div className="relative h-full w-full">
        <video
          ref={videoRef}
          className="h-full w-full object-cover"
          autoPlay
          playsInline
        />
        
        <div className="absolute inset-0 flex items-center justify-center">
          <div className="h-64 w-64 border-4 border-white rounded-lg" />
        </div>

        <div className="absolute top-4 left-0 right-0 flex justify-between px-4">
          <div className="text-white text-lg font-semibold">
            Escanear QR Code
          </div>
          <Button
            variant="ghost"
            size="icon"
            onClick={onClose}
            className="text-white hover:bg-white/20"
          >
            <X className="h-6 w-6" />
          </Button>
        </div>

        {error && (
          <div className="absolute bottom-4 left-0 right-0 px-4">
            <div className="bg-red-500 text-white p-4 rounded-lg text-center">
              {error}
            </div>
          </div>
        )}

        <div className="absolute bottom-4 left-0 right-0 px-4">
          <div className="bg-white/90 backdrop-blur p-4 rounded-lg text-center text-sm">
            Posicione o QR code dentro do quadrado
          </div>
        </div>
      </div>
    </div>
  );
}
