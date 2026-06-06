import EmptyState from '@/Components/EmptyState';
import Pagination from '@/Components/Pagination';

function formatDate(value) {
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}

function formatAmount(value) {
    return new Intl.NumberFormat('id-ID').format(value);
}

function formatSignedAmount(transaction) {
    const formatted = `Rp${formatAmount(transaction.amount)}`;

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

export default function TransactionTable({ transactions, sort = 'desc' }) {
    const sortLabel = sort === 'asc' ? 'terlama' : 'terbaru';

    if (!transactions.data.length) {
        return (
            <div className="space-y-6">
                <EmptyState
                    title="Belum ada transaksi"
                    description="Riwayat transfer akan tampil di sini setelah transaksi pertama Anda."
                />
                <Pagination links={transactions.links} />
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="overflow-hidden rounded-2xl bg-white shadow-sm">
                <div className="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <div>
                        <h3 className="text-lg font-semibold text-slate-900">
                            Riwayat transaksi
                        </h3>
                        <p className="text-sm text-slate-500">
                            Urutan saat ini: {sortLabel}.
                        </p>
                    </div>
                </div>

                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr className="text-left text-sm font-medium text-slate-500">
                                <th className="px-6 py-3">Tanggal</th>
                                <th className="px-6 py-3">Jenis</th>
                                <th className="px-6 py-3">Lawan transaksi</th>
                                <th className="px-6 py-3">Nominal</th>
                                <th className="px-6 py-3">Kode transaksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {transactions.data.map((transaction) => (
                                <tr key={transaction.id} className="text-sm text-slate-700">
                                    <td className="whitespace-nowrap px-6 py-4">
                                        {formatDate(transaction.created_at)}
                                    </td>
                                    <td className="whitespace-nowrap px-6 py-4">
                                        {transaction.type_label}
                                    </td>
                                    <td className="whitespace-nowrap px-6 py-4">
                                        {transaction.counterpart_name}
                                    </td>
                                    <td
                                        className={`whitespace-nowrap px-6 py-4 font-medium ${formatSignedAmount(transaction).className}`}
                                    >
                                        {formatSignedAmount(transaction).label}
                                    </td>
                                    <td className="whitespace-nowrap px-6 py-4">
                                        {transaction.transaction_code}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination links={transactions.links} />
        </div>
    );
}
