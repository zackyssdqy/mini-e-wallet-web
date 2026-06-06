import BalanceCard from '@/Components/BalanceCard';
import PageHeader from '@/Components/PageHeader';
import TransferForm from '@/Components/TransferForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

function formatAmount(value) {
    return `Rp${new Intl.NumberFormat('id-ID').format(value)}`;
}

export default function Create({ users, wallet }) {
    return (
        <AuthenticatedLayout
            header={
                <PageHeader
                    title="Transfer dana"
                    description="Kirim dana ke pengguna lain dalam satu transaksi aman."
                />
            }
        >
            <Head title="Transfer dana" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                    <div className="grid gap-6 lg:grid-cols-[1fr_1.2fr]">
                        <BalanceCard
                            title="Saldo tersedia"
                            value={formatAmount(wallet.balance)}
                            subtitle="Transfer diverifikasi sebelum saldo berubah."
                        />

                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <h2 className="text-lg font-semibold text-slate-900">
                                Detail transfer
                            </h2>
                            <p className="mt-1 text-sm text-slate-500">
                                Pilih penerima dan masukkan nominal yang ingin Anda kirim.
                            </p>

                            <div className="mt-6">
                                <TransferForm users={users} />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
