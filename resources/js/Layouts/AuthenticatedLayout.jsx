import AppLayout from '@/Components/AppLayout';

export default function AuthenticatedLayout({ header, children }) {
    return <AppLayout header={header}>{children}</AppLayout>;
}
