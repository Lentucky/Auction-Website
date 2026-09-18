import { useForm } from '@inertiajs/react';

export default function BidForm({ auction }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        amount: '',
    });

    const minBid = (parseFloat(auction.current_price) + parseFloat(auction.min_increment)).toFixed(2);

    function submit(e) {
        e.preventDefault();
        post(`/auctions/${auction.id}/bids`, {
            preserveScroll: true,
            onSuccess: () => reset('amount'),
        });
    }

    return (
        <form onSubmit={submit} className="flex flex-col gap-3 p-4 border rounded-lg">
            <div>
                <p className="text-sm text-gray-500">Current price</p>
                <p className="text-2xl font-semibold">${auction.current_price}</p>
                <p className="text-xs text-gray-400">Minimum next bid: ${minBid}</p>
            </div>

            <label htmlFor="amount" className="text-sm font-medium">
                Your bid
            </label>
            <input
                id="amount"
                type="number"
                step="0.01"
                min={minBid}
                value={data.amount}
                onChange={(e) => setData('amount', e.target.value)}
                className="border rounded px-3 py-2"
                placeholder={`$${minBid} or more`}
            />

            {errors.amount && (
                <p className="text-red-600 text-sm">{errors.amount}</p>
            )}

            <button
                type="submit"
                disabled={processing}
                className="bg-black text-white rounded px-4 py-2 disabled:opacity-50"
            >
                {processing ? 'Placing bid…' : 'Place Bid'}
            </button>
        </form>
    );
}