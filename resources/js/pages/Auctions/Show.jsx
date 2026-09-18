import { Link, router, usePage } from '@inertiajs/react';

export default function Show({ auction }) {
    const { auth } = usePage().props;
    const isOwner = auth.user && auth.user.id === auction.user_id;

    function cancelAuction() {
        if (confirm('Are you sure you want to cancel this auction?')) {
            router.post(`/auctions/${auction.id}/cancel`);
        }
    }

    return (
        <div className="max-w-2xl mx-auto p-6">
            <h1 className="text-3xl font-bold">{auction.title}</h1>
            <p className="text-gray-600 mt-2">{auction.description}</p>

            {isOwner && auction.status === 'active' && (
                <div className="flex gap-3 mt-4">
                    {auction.bids && auction.bids.length === 0 && (
                        <Link
                            href={`/auctions/${auction.id}/edit`}
                            className="border rounded px-3 py-1 text-sm hover:bg-gray-100"
                        >
                            Edit
                        </Link>
                    )}
                    <button
                        onClick={cancelAuction}
                        className="border border-red-600 text-red-600 rounded px-3 py-1 text-sm hover:bg-red-50"
                    >
                        Cancel Auction
                    </button>
                </div>
            )}

            {auction.status === 'cancelled' && (
                <p className="text-red-600 font-medium mt-4">This auction has been cancelled.</p>
            )}

            {/* existing BidForm etc below */}
        </div>
    );
}