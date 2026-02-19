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

    setUploading(true);
    const formData = new FormData();
    Array.from(files).forEach((file) => formData.append('files[]', file));

    try {
      await fetch(`/events/${event.id}/media`, {
        method: 'POST',
        body: formData,
        headers: { 
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json'
        },
      });
      toast.success('Upload concluído!');
      router.reload();
    } catch (error) {
      console.error('Upload error:', error);
      toast.error('Erro no upload');
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
