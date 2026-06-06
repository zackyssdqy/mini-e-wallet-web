export default function BalanceCard({ title = 'Saldo saat ini', value, subtitle }) {
    return (
        <div className="rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 p-6 text-white shadow-sm">
            <p className="text-sm text-slate-300">{title}</p>
            <p className="mt-3 text-3xl font-semibold">{value}</p>
            {subtitle ? <p className="mt-2 text-sm text-slate-300">{subtitle}</p> : null}
        </div>
    );
}
