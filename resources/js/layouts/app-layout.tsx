import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { BottomNav } from '@/components/bottom-nav';
import type { AppLayoutProps } from '@/types';

export default ({ children, breadcrumbs, ...props }: AppLayoutProps) => (
    <>
        <AppLayoutTemplate breadcrumbs={breadcrumbs} {...props}>
            {children}
        </AppLayoutTemplate>
        <BottomNav />
    </>
);
