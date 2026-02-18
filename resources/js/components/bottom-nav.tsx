import { Link } from '@inertiajs/react';
import { Home, Calendar, User } from 'lucide-react';
import { cn } from '@/lib/utils';

export function BottomNav() {
  const isActive = (path: string) => window.location.pathname === path;

  return (
    <nav className="fixed bottom-0 left-0 right-0 z-50 border-t bg-background md:hidden">
      <div className="flex items-center justify-around py-2">
        <Link
          href="/events"
          className={cn(
            'flex flex-col items-center gap-1 px-4 py-2 text-xs transition-colors',
            isActive('/events') ? 'text-primary' : 'text-muted-foreground'
          )}
        >
          <Home className="h-5 w-5" />
          <span>Eventos</span>
        </Link>
        
        <Link
          href="/events/create"
          className={cn(
            'flex flex-col items-center gap-1 px-4 py-2 text-xs transition-colors',
            isActive('/events/create') ? 'text-primary' : 'text-muted-foreground'
          )}
        >
          <Calendar className="h-5 w-5" />
          <span>Criar</span>
        </Link>
        
        <Link
          href="/settings/profile"
          className={cn(
            'flex flex-col items-center gap-1 px-4 py-2 text-xs transition-colors',
            isActive('/settings/profile') ? 'text-primary' : 'text-muted-foreground'
          )}
        >
          <User className="h-5 w-5" />
          <span>Perfil</span>
        </Link>
      </div>
    </nav>
  );
}
