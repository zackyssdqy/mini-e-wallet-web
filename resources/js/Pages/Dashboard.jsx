import BalanceCard from '@/Components/BalanceCard';
import EmptyState from '@/Components/EmptyState';
import PageHeader from '@/Components/PageHeader';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

function formatAmount(value) {
    return `Rp${new Intl.NumberFormat('id-ID').format(value)}`;
}

function formatDate(value) {
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}

function formatSignedAmount(transaction) {
    const formatted = `Rp${new Intl.NumberFormat('id-ID').format(transaction.amount)}`;

    if (transaction.type_label === 'Transfer masuk') {
        return {
            label: `+${formatted}`,
            className: 'text-emerald-600',
        };
    }

    return {
        label: `-${formatted}`,
        className: 'text-slate-900',
    };
}

export default function Dashboard({ userName, wallet, recentTransactions }) {
    return (
        <AuthenticatedLayout
            header={
                    <PageHeader
                    title="Beranda"
                    description={`Selamat datang kembali, ${userName}. Berikut saldo dan aktivitas terbaru Anda.`}
                    actions={
                        <>
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
            <Head title="Beranda" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                    <div className="grid gap-6 lg:grid-cols-[1fr_1.1fr]">
                        <BalanceCard
                            title="Saldo saat ini"
                            value={formatAmount(wallet.balance)}
                            subtitle="Saldo Anda langsung diperbarui setelah setiap transfer."
                        />

                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <h2 className="text-lg font-semibold text-slate-900">
                                Ringkasan cepat
                            </h2>
                            <div className="mt-4 grid gap-4 sm:grid-cols-2">
                                <div className="rounded-xl bg-slate-50 p-4">
                                    <p className="text-xs uppercase tracking-wide text-slate-500">
                                        Nama akun
                                    </p>
                                    <p className="mt-1 text-lg font-semibold text-slate-900">
                                        {userName}
                                    </p>
                                </div>
                                <div className="rounded-xl bg-slate-50 p-4">
                                    <p className="text-xs uppercase tracking-wide text-slate-500">
                                        Transaksi terbaru
                                    </p>
                                    <p className="mt-1 text-lg font-semibold text-slate-900">
                                        {recentTransactions.length}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="rounded-2xl bg-white shadow-sm">
                        <div className="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                            <div>
                                <h2 className="text-lg font-semibold text-slate-900">
                                    Transaksi terbaru
                                </h2>
                                <p className="text-sm text-slate-500">
                                    Lima mutasi terakhir pada akun Anda.
                                </p>
                            </div>
                            <Link
                                href={route('transactions.index')}
                                className="text-sm font-medium text-slate-700 hover:text-slate-900"
                            >
                                Lihat semua
                            </Link>
                        </div>

                        {recentTransactions.length > 0 ? (
                            <div className="divide-y divide-slate-100">
                                {recentTransactions.map((transaction) => (
                                    <div
                                        key={transaction.id}
                                        className="flex flex-wrap items-center justify-between gap-4 px-6 py-4"
                                    >
                                        <div>
                                            <p className="text-sm font-medium text-slate-900">
                                                {transaction.type_label} dengan{' '}
                                                {transaction.counterpart_name}
                                            </p>
                                            <p className="text-sm text-slate-500">
                                                {formatDate(transaction.created_at)}
                                            </p>
                                        </div>
                                        <div className="text-right">
                                            <p
                                                className={`text-sm font-semibold ${formatSignedAmount(transaction).className}`}
                                            >
                                                {formatSignedAmount(transaction).label}
                                            </p>
                                            <p className="text-xs text-slate-500">
                                                {transaction.transaction_code}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="p-6">
                                <EmptyState
                                    title="Belum ada transaksi"
                                    description="Riwayat transfer Anda akan muncul di sini setelah transfer pertama."
                                />
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
