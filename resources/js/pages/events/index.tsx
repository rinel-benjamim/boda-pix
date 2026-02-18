import { Head, Link, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { Event } from '@/types/event';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';

interface Props {
  events: Event[];
}

export default function Events({ events }: Props) {
  return (
    <AppLayout>
      <Head title="Meus Eventos" />
      
      <div className="container mx-auto p-4 pb-20 md:pb-4">
        <div className="mb-6 flex items-center justify-between">
          <h1 className="text-2xl font-bold">Meus Eventos</h1>
          <Button onClick={() => router.visit('/events/create')}>
            <Plus className="mr-2 h-4 w-4" />
            Criar Evento
          </Button>
        </div>

        {events.length === 0 ? (
          <Card className="p-12 text-center">
            <p className="mb-4 text-muted-foreground">Ainda não tens eventos</p>
            <Button onClick={() => router.visit('/events/create')}>
              Criar Primeiro Evento
            </Button>
          </Card>
        ) : (
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {events.map((event) => (
              <Link key={event.id} href={`/events/${event.id}`}>
                <Card className="overflow-hidden transition-all hover:shadow-lg">
                  {event.cover_image && (
                    <img
                      src={event.cover_image}
                      alt={event.name}
                      className="h-48 w-full object-cover"
                    />
                  )}
                  <div className="p-4">
                    <h3 className="mb-2 font-semibold">{event.name}</h3>
                    <p className="mb-2 text-sm text-muted-foreground">
                      {new Date(event.event_date).toLocaleDateString('pt-AO')}
                    </p>
                    <div className="flex gap-4 text-sm text-muted-foreground">
                      <span>{event.participants_count} participantes</span>
                      <span>{event.media_count} fotos</span>
                    </div>
                  </div>
                </Card>
              </Link>
            ))}
          </div>
        )}
      </div>
    </AppLayout>
  );
}
