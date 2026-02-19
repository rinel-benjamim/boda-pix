import { Head, router } from '@inertiajs/react';
import { useState, useRef } from 'react';
import { Upload, Share2, Settings, Copy, Link as LinkIcon, Download } from 'lucide-react';
import { Event, Media, PaginatedMedia } from '@/types/event';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { toast } from 'sonner';
import { QRCodeSVG } from 'qrcode.react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import html2canvas from 'html2canvas';

interface Props {
  event: Event;
  media: PaginatedMedia;
}

export default function ShowEvent({ event, media }: Props) {
  const [uploading, setUploading] = useState(false);
  const qrRef = useRef<HTMLDivElement>(null);

  const handleUpload = (files: FileList | null) => {
    if (!files || files.length === 0) return;

    const maxSize = 20 * 1024 * 1024;
    for (const file of Array.from(files)) {
      if (file.size > maxSize) {
        toast.error(`Ficheiro "${file.name}" é muito grande. Máximo: 20MB`);
        return;
      }
    }

    setUploading(true);
    
    router.post(`/events/${event.id}/media`, 
      { files: Array.from(files) },
      {
        forceFormData: true,
        onSuccess: () => {
          toast.success(`${files.length} ficheiro(s) carregado(s) com sucesso!`);
          setUploading(false);
        },
        onError: (errors) => {
          console.error('Upload error:', errors);
          toast.error('Erro no upload');
          setUploading(false);
        },
      }
    );
  };

  const copyAccessCode = () => {
    navigator.clipboard.writeText(event.access_code);
    toast.success('Código copiado!');
  };

  const inviteLink = `${window.location.origin}/events/join?code=${event.access_code}`;

  const copyInviteLink = () => {
    navigator.clipboard.writeText(inviteLink);
    toast.success('Link copiado!');
  };

  const shareEvent = async () => {
    if (navigator.share) {
      try {
        await navigator.share({
          title: event.name,
          text: `Junta-te ao evento ${event.name}!`,
          url: inviteLink,
        });
      } catch (err) {
        if ((err as Error).name !== 'AbortError') {
          copyInviteLink();
        }
      }
    } else {
      copyInviteLink();
    }
  };

  const downloadQRCode = async () => {
    if (!qrRef.current) return;
    
    try {
      const canvas = await html2canvas(qrRef.current, {
        backgroundColor: '#ffffff',
        scale: 2,
      });
      
      const link = document.createElement('a');
      link.download = `${event.name}-qrcode.png`;
      link.href = canvas.toDataURL('image/png');
      link.click();
      
      toast.success('QR Code baixado!');
    } catch (error) {
      toast.error('Erro ao baixar QR Code');
    }
  };

  return (
    <AppLayout>
      <Head title={event.name} />
      
      <div className="container mx-auto p-4 pb-20 md:pb-4">
        {event.cover_image && (
          <div className="mb-6 h-48 overflow-hidden rounded-lg">
            <img src={event.cover_image} alt={event.name} className="h-full w-full object-cover" />
          </div>
        )}

        <div className="mb-6 flex items-start justify-between">
          <div>
            <h1 className="text-3xl font-bold">{event.name}</h1>
            <p className="text-muted-foreground">{new Date(event.event_date).toLocaleDateString('pt-AO')}</p>
          </div>
          <div className="flex gap-2">
            <Dialog>
              <DialogTrigger asChild>
                <Button variant="outline" size="icon">
                  <Share2 className="h-4 w-4" />
                </Button>
              </DialogTrigger>
              <DialogContent className="max-w-md">
                <DialogHeader>
                  <DialogTitle>Convidar Participantes</DialogTitle>
                </DialogHeader>
                <Tabs defaultValue="qr" className="w-full">
                  <TabsList className="grid w-full grid-cols-3">
                    <TabsTrigger value="qr">QR Code</TabsTrigger>
                    <TabsTrigger value="code">Código</TabsTrigger>
                    <TabsTrigger value="link">Link</TabsTrigger>
                  </TabsList>
                  <TabsContent value="qr" className="space-y-4">
                    <div ref={qrRef} className="flex flex-col items-center justify-center bg-white p-6 rounded-lg">
                      <QRCodeSVG value={inviteLink} size={200} level="H" />
                      <p className="mt-4 text-sm font-medium text-gray-900">{event.name}</p>
                    </div>
                    <p className="text-center text-sm text-muted-foreground">
                      Escaneia este código para entrar no evento
                    </p>
                    <Button onClick={downloadQRCode} className="w-full" variant="outline">
                      <Download className="mr-2 h-4 w-4" />
                      Baixar QR Code
                    </Button>
                  </TabsContent>
                  <TabsContent value="code" className="space-y-4">
                    <div className="flex gap-2">
                      <Input value={event.access_code} readOnly className="text-center text-2xl font-bold tracking-wider" />
                      <Button onClick={copyAccessCode} size="icon">
                        <Copy className="h-4 w-4" />
                      </Button>
                    </div>
                    <p className="text-center text-sm text-muted-foreground">
                      Partilha este código para convidar participantes
                    </p>
                  </TabsContent>
                  <TabsContent value="link" className="space-y-4">
                    <div className="flex gap-2">
                      <Input value={inviteLink} readOnly className="text-sm" />
                      <Button onClick={copyInviteLink} size="icon">
                        <Copy className="h-4 w-4" />
                      </Button>
                    </div>
                    <Button onClick={shareEvent} className="w-full">
                      <LinkIcon className="mr-2 h-4 w-4" />
                      Partilhar Link
                    </Button>
                  </TabsContent>
                </Tabs>
              </DialogContent>
            </Dialog>
            {event.is_admin && (
              <Button variant="outline" size="icon" onClick={() => router.visit(`/events/${event.id}/edit`)}>
                <Settings className="h-4 w-4" />
              </Button>
            )}
          </div>
        </div>

        <div className="mb-6">
          <label htmlFor="upload" className="cursor-pointer">
            <Card className="flex items-center justify-center p-8 transition-colors hover:bg-accent">
              <Upload className="mr-2 h-5 w-5" />
              <span>{uploading ? 'A carregar...' : 'Carregar Fotos/Vídeos'}</span>
            </Card>
            <input
              id="upload"
              type="file"
              multiple
              accept="image/*,video/*"
              className="hidden"
              onChange={(e) => handleUpload(e.target.files)}
              disabled={uploading}
            />
          </label>
        </div>

        <div className="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
          {media.data.map((item) => (
            <div key={item.id} className="aspect-square overflow-hidden rounded-lg">
              {item.type === 'image' ? (
                <img src={item.thumbnail_url || item.url} alt="" className="h-full w-full object-cover" />
              ) : (
                <video src={item.url} className="h-full w-full object-cover" />
              )}
            </div>
          ))}
        </div>
      </div>
    </AppLayout>
  );
}
