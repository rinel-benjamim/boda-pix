import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler, useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/input-error';
import { toast } from 'sonner';

export default function CreateEvent() {
  const { data, setData, post, processing, errors, recentlySuccessful } = useForm({
    name: '',
    description: '',
    event_date: '',
    cover_image: null as File | null,
    is_private: true,
  });

  const submit: FormEventHandler = (e) => {
    e.preventDefault();
    post('/events', {
      forceFormData: true,
      onSuccess: () => {
        toast.success('Evento criado com sucesso!');
      },
      onError: (errors) => {
        console.error('Error creating event:', errors);
        toast.error('Erro ao criar evento');
      },
    });
  };

  return (
    <AppLayout>
      <Head title="Criar Evento" />
      
      <div className="container mx-auto max-w-2xl p-4">
        <h1 className="mb-6 text-2xl font-bold">Criar Novo Evento</h1>

        <Card className="p-6">
          <form onSubmit={submit} className="space-y-4">
            <div>
              <Label htmlFor="name">Nome do Evento</Label>
              <Input
                id="name"
                value={data.name}
                onChange={(e) => setData('name', e.target.value)}
                required
              />
              <InputError message={errors.name} />
            </div>

            <div>
              <Label htmlFor="description">Descrição</Label>
              <textarea
                id="description"
                value={data.description}
                onChange={(e) => setData('description', e.target.value)}
                className="min-h-24 w-full rounded-md border px-3 py-2"
              />
              <InputError message={errors.description} />
            </div>

            <div>
              <Label htmlFor="event_date">Data do Evento</Label>
              <Input
                id="event_date"
                type="date"
                value={data.event_date}
                onChange={(e) => setData('event_date', e.target.value)}
                required
              />
              <InputError message={errors.event_date} />
            </div>

            <div>
              <Label htmlFor="cover_image">Imagem de Capa (opcional, máx 10MB)</Label>
              <Input
                id="cover_image"
                type="file"
                accept="image/*"
                onChange={(e) => {
                  const file = e.target.files?.[0];
                  if (file) {
                    if (file.size > 10 * 1024 * 1024) {
                      toast.error('Imagem muito grande. Máximo: 10MB');
                      e.target.value = '';
                      return;
                    }
                    setData('cover_image', file);
                  }
                }}
              />
              <InputError message={errors.cover_image} />
            </div>

            <Button type="submit" disabled={processing} className="w-full">
              Criar Evento
            </Button>
          </form>
        </Card>
      </div>
    </AppLayout>
  );
}
