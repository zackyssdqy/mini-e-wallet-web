import { router, usePage } from '@inertiajs/react';
import { useEffect } from 'react';

export default function Loading() {
    const user = usePage().props.auth.user;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            router.visit(user ? route('dashboard') : route('login'), {
                replace: true,
            });
        }, 700);

        return () => window.clearTimeout(timer);
    }, [user]);

    return (
        <div className="flex min-h-screen items-center justify-center bg-[#f8fafc] px-4">
            <div className="flex flex-col items-center gap-4 rounded-3xl border border-slate-200 bg-white px-8 py-10 shadow-sm">
                <div className="h-12 w-12 animate-spin rounded-full border-4 border-slate-200 border-t-slate-900" />
                <div className="text-center">
                    <p className="text-lg font-semibold text-slate-900">
                        Dompet Digital Mini
                    </p>
                    <p className="mt-1 text-sm text-slate-500">
                        Menyiapkan sesi Anda...
                    </p>
                </div>
            </div>
        </div>
    );
}
