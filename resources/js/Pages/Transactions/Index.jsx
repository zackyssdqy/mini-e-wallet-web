import PageHeader from '@/Components/PageHeader';
import TransactionTable from '@/Components/TransactionTable';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({ transactions, sort }) {
    const nextSort = sort === 'asc' ? 'desc' : 'asc';
    const sortLabel = nextSort === 'asc' ? 'terlama' : 'terbaru';

    return (
        <AuthenticatedLayout
            header={
                <PageHeader
                    title="Riwayat transaksi"
                    description="Pantau transfer masuk dan keluar di satu tempat."
                    actions={
                        <>
                            <Link
                                href={route('transactions.index', { sort: nextSort })}
                                className="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                            >
                                Urutkan {sortLabel}
                            </Link>
                            <Link
                                href={route('transfers.create')}
                                className="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800"
                            >
                                Transfer baru
                            </Link>
                            <button
                                type="button"
                                onClick={() => router.reload({ preserveScroll: true })}
                                className="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                            >
                                Muat ulang
                            </button>
                        </>
                    }
                />
            }
        >
            <Head title="Riwayat transaksi" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <TransactionTable transactions={transactions} sort={sort} />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
