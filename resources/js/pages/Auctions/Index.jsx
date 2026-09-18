import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Index({ auctions, filters }) {
    const [localFilters, setLocalFilters] = useState({
        type: filters.type || '',
        rating: filters.rating || '',
        min_price: filters.min_price || '',
        max_price: filters.max_price || '',
        sort: filters.sort || 'ending_soon',
    });

    function applyFilters(e) {
        e.preventDefault();
        router.get('/auctions', localFilters, { preserveState: true });
    }

    function timeLeft(endsAt) {
        const diff = new Date(endsAt) - new Date();
        if (diff <= 0) return 'Ended';
        const hours = Math.floor(diff / 3600000);
        const mins = Math.floor((diff % 3600000) / 60000);
        return hours > 0 ? `${hours}h ${mins}m left` : `${mins}m left`;
    }

    return (
        <div className="max-w-6xl mx-auto p-6">
            <h1 className="text-3xl font-bold mb-6">Browse Auctions</h1>

            <form onSubmit={applyFilters} className="flex flex-wrap gap-3 mb-8 items-end">
                <div>
                    <label className="block text-xs text-gray-500 mb-1">Type</label>
                    <select
                        value={localFilters.type}
                        onChange={(e) => setLocalFilters({ ...localFilters, type: e.target.value })}
                        className="border rounded px-2 py-1"
                    >
                        <option value="">All</option>
                        <option value="ych">YCH</option>
                        <option value="commission_slot">Commission Slot</option>
                        <option value="adoptable">Adoptable</option>
                        <option value="base_recolor">Base/Recolor</option>
                    </select>
                </div>

                <div>
                    <label className="block text-xs text-gray-500 mb-1">Rating</label>
                    <select
                        value={localFilters.rating}
                        onChange={(e) => setLocalFilters({ ...localFilters, rating: e.target.value })}
                        className="border rounded px-2 py-1"
                    >
                        <option value="">All</option>
                        <option value="safe">Safe</option>
                        <option value="questionable">Questionable</option>
                        <option value="explicit">Explicit</option>
                    </select>
                </div>

                <div>
                    <label className="block text-xs text-gray-500 mb-1">Min Price</label>
                    <input
                        type="number"
                        value={localFilters.min_price}
                        onChange={(e) => setLocalFilters({ ...localFilters, min_price: e.target.value })}
                        className="border rounded px-2 py-1 w-24"
                    />
                </div>

                <div>
                    <label className="block text-xs text-gray-500 mb-1">Max Price</label>
                    <input
                        type="number"
                        value={localFilters.max_price}
                        onChange={(e) => setLocalFilters({ ...localFilters, max_price: e.target.value })}
                        className="border rounded px-2 py-1 w-24"
                    />
                </div>

                <div>
                    <label className="block text-xs text-gray-500 mb-1">Sort</label>
                    <select
                        value={localFilters.sort}
                        onChange={(e) => setLocalFilters({ ...localFilters, sort: e.target.value })}
                        className="border rounded px-2 py-1"
                    >
                        <option value="ending_soon">Ending Soon</option>
                        <option value="newest">Newest</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                    </select>
                </div>

                <button type="submit" className="bg-black text-white rounded px-4 py-2">
                    Apply
                </button>
            </form>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {auctions.data.map((auction) => (
                    <Link
                        key={auction.id}
                        href={`/auctions/${auction.id}`}
                        className="border rounded-lg p-4 hover:shadow-md transition"
                    >
                        <h2 className="font-semibold text-lg">{auction.title}</h2>
                        <p className="text-sm text-gray-500">by {auction.artist.name}</p>
                        <p className="text-xl font-bold mt-2">${auction.current_price}</p>
                        <p className="text-xs text-gray-400">{timeLeft(auction.ends_at)}</p>
                    </Link>
                ))}
            </div>

            {auctions.data.length === 0 && (
                <p className="text-gray-500 mt-8">No auctions match your filters.</p>
            )}

            <div className="flex gap-2 mt-8">
                {auctions.links.map((link, i) => (
                    <button
                        key={i}
                        disabled={!link.url}
                        onClick={() => link.url && router.get(link.url)}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                        className={`px-3 py-1 border rounded text-sm min-w-[36px] text-center ${
    link.active ? 'bg-black text-white border-black' : 'bg-white text-gray-700 border-gray-300'
} ${!link.url ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-100'}`}
                    />
                ))}
            </div>
        </div>
    );
}