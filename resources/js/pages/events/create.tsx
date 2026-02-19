import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/input-error';

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
        console.log('Event created successfully');
      },
      onError: (errors) => {
        console.error('Error creating event:', errors);
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
              <Label htmlFor="cover_image">Imagem de Capa</Label>
              <Input
                id="cover_image"
                type="file"
                accept="image/*"
                onChange={(e) => setData('cover_image', e.target.files?.[0] || null)}
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
