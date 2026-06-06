import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import LoadingSpinner from '@/Components/LoadingSpinner';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { useForm } from '@inertiajs/react';

function formatAmountInput(value) {
    if (!value) {
        return '';
    }

    const numericValue = value.toString().replace(/\D/g, '');

    if (!numericValue) {
        return '';
    }

    return new Intl.NumberFormat('id-ID').format(Number(numericValue));
}

export default function TransferForm({ users, className = '' }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        receiver_id: '',
        amount: '',
    });

    const submit = (e) => {
        e.preventDefault();

        post(route('transfers.store'), {
            preserveScroll: true,
            onSuccess: () => reset('amount'),
        });
    };

    return (
        <form onSubmit={submit} className={`space-y-6 ${className}`}>
            <div>
                <InputLabel value="Penerima" htmlFor="receiver_id" />
                <select
                    id="receiver_id"
                    name="receiver_id"
                    className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    value={data.receiver_id}
                    onChange={(e) => setData('receiver_id', e.target.value)}
                >
                    <option value="">Pilih penerima</option>
                    {users.map((user) => (
                        <option key={user.id} value={user.id}>
                            {user.name} ({user.email})
                        </option>
                    ))}
                </select>
                <InputError message={errors.receiver_id} className="mt-2" />
            </div>

            <div>
                <InputLabel value="Nominal" htmlFor="amount" />
                <TextInput
                    id="amount"
                    type="text"
                    inputMode="numeric"
                    autoComplete="off"
                    placeholder="10.000"
                    className="mt-1 block w-full"
                    value={formatAmountInput(data.amount)}
                    onChange={(e) => {
                        const numericValue = e.target.value.replace(/\D/g, '');
                        setData('amount', numericValue);
                    }}
                />
                <InputError message={errors.amount} className="mt-2" />
            </div>

            <div className="flex items-center gap-3">
                <PrimaryButton disabled={processing}>
                    {processing ? <LoadingSpinner label="Mengirim..." /> : 'Kirim'}
                </PrimaryButton>
            </div>
        </form>
    );
}
