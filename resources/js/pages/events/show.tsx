import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { Upload, Share2, Settings } from 'lucide-react';
import { Event, Media, PaginatedMedia } from '@/types/event';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { toast } from 'sonner';

interface Props {
  event: Event;
  media: PaginatedMedia;
}

export default function ShowEvent({ event, media }: Props) {
  const [uploading, setUploading] = useState(false);

  const handleUpload = async (files: FileList | null) => {
    if (!files || files.length === 0) return;

    console.log('Event ID:', event.id);
    console.log('Upload URL:', `/events/${event.id}/media`);

    // Validar tamanho dos arquivos
    const maxSize = 20 * 1024 * 1024; // 20MB
    for (const file of Array.from(files)) {
      if (file.size > maxSize) {
        toast.error(`Ficheiro "${file.name}" é muito grande. Máximo: 20MB`);
        return;
      }
    }

    setUploading(true);
    const formData = new FormData();
    Array.from(files).forEach((file) => formData.append('files[]', file));

    try {
      const url = `/events/${event.id}/media`;
      console.log('Sending POST to:', url);
      
      const response = await fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json'
        },
      });
      
      console.log('Response status:', response.status);
      
      if (!response.ok) {
        const data = await response.json().catch(() => ({}));
        console.error('Response error:', data);
        throw new Error(data.message || 'Erro no upload');
      }
      
      toast.success(`${files.length} ficheiro(s) carregado(s) com sucesso!`);
      router.reload();
    } catch (error) {
      console.error('Upload error:', error);
      toast.error(error instanceof Error ? error.message : 'Erro no upload');
    } finally {
      setUploading(false);
    }
  };

  const copyAccessCode = () => {
    navigator.clipboard.writeText(event.access_code);
    toast.success('Código copiado!');
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
              <DialogContent>
                <DialogHeader>
                  <DialogTitle>Código de Acesso</DialogTitle>
                </DialogHeader>
                <div className="flex gap-2">
                  <Input value={event.access_code} readOnly />
                  <Button onClick={copyAccessCode}>Copiar</Button>
                </div>
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
