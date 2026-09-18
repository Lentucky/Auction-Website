import { useForm } from '@inertiajs/react';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        type: 'ych',
        content_rating: 'safe',
        starting_price: '',
        min_increment: '5.00',
        ends_at: '',
    });

    function submit(e) {
        e.preventDefault();
        post('/auctions');
    }

    return (
        <div className="max-w-xl mx-auto p-6">
            <h1 className="text-2xl font-bold mb-6">Create Auction</h1>

            <form onSubmit={submit} className="flex flex-col gap-4">
                <div>
                    <label className="block text-sm font-medium mb-1">Title</label>
                    <input
                        type="text"
                        value={data.title}
                        onChange={(e) => setData('title', e.target.value)}
                        className="border rounded px-3 py-2 w-full"
                    />
                    {errors.title && <p className="text-red-600 text-sm">{errors.title}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium mb-1">Description</label>
                    <textarea
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        className="border rounded px-3 py-2 w-full"
                        rows={4}
                    />
                    {errors.description && <p className="text-red-600 text-sm">{errors.description}</p>}
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <label className="block text-sm font-medium mb-1">Type</label>
                        <select
                            value={data.type}
                            onChange={(e) => setData('type', e.target.value)}
                            className="border rounded px-3 py-2 w-full"
                        >
                            <option value="ych">YCH</option>
                            <option value="commission_slot">Commission Slot</option>
                            <option value="adoptable">Adoptable</option>
                            <option value="base_recolor">Base/Recolor</option>
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm font-medium mb-1">Content Rating</label>
                        <select
                            value={data.content_rating}
                            onChange={(e) => setData('content_rating', e.target.value)}
                            className="border rounded px-3 py-2 w-full"
                        >
                            <option value="safe">Safe</option>
                            <option value="questionable">Questionable</option>
                            <option value="explicit">Explicit</option>
                        </select>
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <label className="block text-sm font-medium mb-1">Starting Price ($)</label>
                        <input
                            type="number"
                            step="0.01"
                            value={data.starting_price}
                            onChange={(e) => setData('starting_price', e.target.value)}
                            className="border rounded px-3 py-2 w-full"
                        />
                        {errors.starting_price && <p className="text-red-600 text-sm">{errors.starting_price}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium mb-1">Min Increment ($)</label>
                        <input
                            type="number"
                            step="0.01"
                            value={data.min_increment}
                            onChange={(e) => setData('min_increment', e.target.value)}
                            className="border rounded px-3 py-2 w-full"
                        />
                    </div>
                </div>

                <div>
                    <label className="block text-sm font-medium mb-1">Ends At</label>
                    <input
                        type="datetime-local"
                        value={data.ends_at}
                        onChange={(e) => setData('ends_at', e.target.value)}
                        className="border rounded px-3 py-2 w-full"
                    />
                    {errors.ends_at && <p className="text-red-600 text-sm">{errors.ends_at}</p>}
                </div>

                <button
                    type="submit"
                    disabled={processing}
                    className="bg-black text-white rounded px-4 py-2 disabled:opacity-50"
                >
                    {processing ? 'Creating…' : 'Create Auction'}
                </button>
            </form>
        </div>
    );
}