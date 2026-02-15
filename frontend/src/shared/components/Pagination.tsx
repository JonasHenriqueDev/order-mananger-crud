interface PaginationProps {
    currentPage: number;
    lastPage: number;
    onPageChange: (page: number) => void;
}

export default function Pagination({ currentPage, lastPage, onPageChange }: PaginationProps) {
    const pages = [];
    const maxVisiblePages = 5;

    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(lastPage, startPage + maxVisiblePages - 1);

    if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
    }

    if (lastPage <= 1) return null;

    return (
        <div className="flex items-center justify-center gap-2 mt-6">
            <button
                onClick={() => onPageChange(currentPage - 1)}
                disabled={currentPage === 1}
                className="px-3 py-2 rounded-lg bg-[#2a2a2a] border border-[#3d3d3d] text-gray-300 hover:bg-[#383838] disabled:opacity-50 disabled:cursor-not-allowed transition"
            >
                Previous
            </button>

            {startPage > 1 && (
                <>
                    <button
                        onClick={() => onPageChange(1)}
                        className="px-3 py-2 rounded-lg bg-[#2a2a2a] border border-[#3d3d3d] text-gray-300 hover:bg-[#383838] transition"
                    >
                        1
                    </button>
                    {startPage > 2 && (
                        <span className="px-2 text-gray-500">...</span>
                    )}
                </>
            )}

            {pages.map((page) => (
                <button
                    key={page}
                    onClick={() => onPageChange(page)}
                    className={`px-3 py-2 rounded-lg border transition ${
                        page === currentPage
                            ? "bg-blue-600 border-blue-500 text-white"
                            : "bg-[#2a2a2a] border-[#3d3d3d] text-gray-300 hover:bg-[#383838]"
                    }`}
                >
                    {page}
                </button>
            ))}

            {endPage < lastPage && (
                <>
                    {endPage < lastPage - 1 && (
                        <span className="px-2 text-gray-500">...</span>
                    )}
                    <button
                        onClick={() => onPageChange(lastPage)}
                        className="px-3 py-2 rounded-lg bg-[#2a2a2a] border border-[#3d3d3d] text-gray-300 hover:bg-[#383838] transition"
                    >
                        {lastPage}
                    </button>
                </>
            )}

            <button
                onClick={() => onPageChange(currentPage + 1)}
                disabled={currentPage === lastPage}
                className="px-3 py-2 rounded-lg bg-[#2a2a2a] border border-[#3d3d3d] text-gray-300 hover:bg-[#383838] disabled:opacity-50 disabled:cursor-not-allowed transition"
            >
                Next
            </button>
        </div>
    );
}