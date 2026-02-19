import { Head, Link, router } from '@inertiajs/react';
import { Plus, Search } from 'lucide-react';
import { useState, useMemo } from 'react';
import { Event } from '@/types/event';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

interface Props {
  events: Event[];
  user: { id: number };
}

export default function Events({ events, user }: Props) {
  const [search, setSearch] = useState('');

  const myEvents = useMemo(
    () => events.filter((event) => event.created_by.id === user.id),
    [events, user.id]
  );

  const joinedEvents = useMemo(
    () => events.filter((event) => event.created_by.id !== user.id),
    [events, user.id]
  );

  const filterEvents = (eventList: Event[]) => {
    if (!search) return eventList;
    return eventList.filter(
      (event) =>
        event.name.toLowerCase().includes(search.toLowerCase()) ||
        event.description?.toLowerCase().includes(search.toLowerCase())
    );
  };

  const filteredMyEvents = filterEvents(myEvents);
  const filteredJoinedEvents = filterEvents(joinedEvents);

  const EventCard = ({ event }: { event: Event }) => (
    <Link href={`/events/${event.id}`}>
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
  );

  const EmptyState = ({ message }: { message: string }) => (
    <Card className="p-12 text-center">
      <p className="text-muted-foreground">{message}</p>
    </Card>
  );

  return (
    <AppLayout>
      <Head title="Meus Eventos" />
      
      <div className="container mx-auto p-4 pb-20 md:pb-4">
        <div className="mb-6 flex items-center justify-between">
          <h1 className="text-2xl font-bold">Meus Eventos</h1>
        </div>

        <div className="mb-6">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <Input
              placeholder="Pesquisar eventos..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="pl-10"
            />
          </div>
        </div>

        <Tabs defaultValue="created" className="w-full">
          <TabsList className="grid w-full grid-cols-2">
            <TabsTrigger value="created">
              Criados ({myEvents.length})
            </TabsTrigger>
            <TabsTrigger value="joined">
              Participando ({joinedEvents.length})
            </TabsTrigger>
          </TabsList>

          <TabsContent value="created" className="mt-6">
            {filteredMyEvents.length === 0 ? (
              search ? (
                <EmptyState message="Nenhum evento encontrado" />
              ) : (
                <EmptyState message="Ainda não criaste eventos" />
              )
            ) : (
              <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {filteredMyEvents.map((event) => (
                  <EventCard key={event.id} event={event} />
                ))}
              </div>
            )}
          </TabsContent>

          <TabsContent value="joined" className="mt-6">
            {filteredJoinedEvents.length === 0 ? (
              search ? (
                <EmptyState message="Nenhum evento encontrado" />
              ) : (
                <EmptyState message="Ainda não entraste em eventos" />
              )
            ) : (
              <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {filteredJoinedEvents.map((event) => (
                  <EventCard key={event.id} event={event} />
                ))}
              </div>
            )}
          </TabsContent>
        </Tabs>
      </div>
    </AppLayout>
  );
}
