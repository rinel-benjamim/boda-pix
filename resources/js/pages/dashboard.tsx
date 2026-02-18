import { Head } from '@inertiajs/react';
import { useEffect } from 'react';
import { router } from '@inertiajs/react';

export default function Dashboard() {
    useEffect(() => {
        router.visit('/events');
    }, []);

    return <Head title="Dashboard" />;
}
