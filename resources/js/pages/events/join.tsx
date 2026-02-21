import { Head, useForm, router } from '@inertiajs/react';
import { FormEventHandler, useState, useEffect } from 'react';
import { QrCode, Hash } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import InputError from '@/components/input-error';
import { toast } from 'sonner';
import { QRScanner } from '@/components/qr-scanner';

export default function JoinEvent() {
  const { data, setData, post, processing, errors } = useForm({
    access_code: '',
  });
  const [showScanner, setShowScanner] = useState(false);

  // Processar código da URL (link compartilhado)
  useEffect(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code');
    
    if (code && code.length === 8) {
      const upperCode = code.toUpperCase();
      setData('access_code', upperCode);
      
      // Auto-submit após um pequeno delay para garantir que o form está pronto
      setTimeout(() => {
        post('/events/join', {
          onSuccess: () => {
            toast.success('Entraste no evento com sucesso!');
          },
          onError: () => {
            toast.error('Código inválido ou já és membro');
          },
        });
      }, 100);
    }
  }, []);

  const submit: FormEventHandler = (e) => {
    e.preventDefault();
    post('/events/join', {
      onSuccess: () => {
        toast.success('Entraste no evento com sucesso!');
      },
      onError: () => {
        toast.error('Código inválido ou já és membro');
      },
    });
  };

  const handleScan = (scannedData: string) => {
    setShowScanner(false);
    
    let codeToSubmit = '';
    
    try {
      const url = new URL(scannedData);
      const code = url.searchParams.get('code');
      
      if (code && code.length === 8) {
        codeToSubmit = code.toUpperCase();
      }
    } catch {
      // Se não for URL, tentar usar como código direto
      if (scannedData.length === 8) {
        codeToSubmit = scannedData.toUpperCase();
      }
    }
    
    if (codeToSubmit) {
      setData('access_code', codeToSubmit);
      
      // Submeter após atualizar o estado
      setTimeout(() => {
        post('/events/join', {
          onSuccess: () => {
            toast.success('Entraste no evento com sucesso!');
          },
          onError: () => {
            toast.error('Código inválido ou já és membro');
          },
        });
      }, 100);
    } else {
      toast.error('QR Code inválido');
    }
  };

  return (
    <AppLayout>
      <Head title="Entrar em Evento" />
      
      {showScanner && (
        <QRScanner
          onScan={handleScan}
          onClose={() => setShowScanner(false)}
        />
      )}
      
      <div className="container mx-auto max-w-2xl p-4 pb-20 md:pb-4">
        <h1 className="mb-6 text-2xl font-bold">Entrar em Evento</h1>

        <Card className="p-6">
          <Tabs defaultValue="code" className="w-full">
            <TabsList className="grid w-full grid-cols-2">
              <TabsTrigger value="code">
                <Hash className="mr-2 h-4 w-4" />
                Código
              </TabsTrigger>
              <TabsTrigger value="qr">
                <QrCode className="mr-2 h-4 w-4" />
                QR Code
              </TabsTrigger>
            </TabsList>

            <TabsContent value="code" className="space-y-4">
              <form onSubmit={submit} className="space-y-4">
                <div>
                  <Label htmlFor="access_code">Código de Acesso</Label>
                  <Input
                    id="access_code"
                    value={data.access_code}
                    onChange={(e) => setData('access_code', e.target.value.toUpperCase())}
                    placeholder="Ex: ABC12345"
                    maxLength={8}
                    className="text-center text-xl font-bold tracking-wider"
                    required
                  />
                  <InputError message={errors.access_code} />
                </div>

                <Button type="submit" disabled={processing} className="w-full">
                  Entrar no Evento
                </Button>
              </form>
            </TabsContent>

            <TabsContent value="qr" className="space-y-4">
              <div className="flex flex-col items-center justify-center space-y-4 py-8">
                <QrCode className="h-24 w-24 text-muted-foreground" />
                <p className="text-center text-muted-foreground">
                  Escaneia o código QR do evento para entrares automaticamente
                </p>
                <Button onClick={() => setShowScanner(true)} size="lg">
                  <QrCode className="mr-2 h-5 w-5" />
                  Escanear QR Code
                </Button>
              </div>
            </TabsContent>
          </Tabs>
        </Card>
      </div>
    </AppLayout>
  );
}
