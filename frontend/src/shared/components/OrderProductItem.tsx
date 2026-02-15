import type {Product} from "../services/types.ts";

interface OrderProductItemProps {
    product: Product;
    quantity: number;
    onQuantityChange: (quantity: number) => void;
    onRemove: () => void;
}

export default function OrderProductItem({product, quantity, onQuantityChange, onRemove,}: OrderProductItemProps) {
    const subtotal = parseFloat(product.price) * quantity;

    const handleIncrement = () => {
        if (quantity < product.stock) {
            onQuantityChange(quantity + 1);
        }
    };

    const handleDecrement = () => {
        if (quantity > 1) {
            onQuantityChange(quantity - 1);
        }
    };

    const handleInputChange = (value: string) => {
        const num = parseInt(value);
        if (isNaN(num) || num < 1) {
            onQuantityChange(1);
        } else if (num > product.stock) {
            onQuantityChange(product.stock);
        } else {
            onQuantityChange(num);
        }
    };

    return (<div className="flex items-center justify-between p-4 bg-[#1a1a1a] rounded-lg border border-[#3d3d3d]">
            <div className="flex-1">
                <h4 className="text-white font-medium">{product.name}</h4>
                <p className="text-sm text-gray-400">
                    SKU: {product.sku} | Stock: {product.stock}
                </p>
                <p className="text-sm text-gray-300 mt-1">
                    $ {parseFloat(product.price).toFixed(2)} each
                </p>
            </div>

            <div className="flex items-center gap-4">
                <div className="flex items-center gap-2">
                    <label className="text-sm text-gray-400">Qty:</label>
                    <div className="flex items-center gap-1">
                        <button
                            type="button"
                            onClick={handleDecrement}
                            disabled={quantity <= 1}
                            className="w-8 h-8 flex items-center justify-center rounded bg-[#2a2a2a] border border-gray-700 text-white hover:bg-[#383838] disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            -
                        </button>
                        <input
                            type="number"
                            value={quantity}
                            onChange={(e) => handleInputChange(e.target.value)}
                            className="w-16 p-2 rounded-lg bg-[#2a2a2a] border border-gray-700 text-white text-center focus:outline-none focus:ring-2 focus:ring-blue-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        />
                        <button
                            type="button"
                            onClick={handleIncrement}
                            disabled={quantity >= product.stock}
                            className="w-8 h-8 flex items-center justify-center rounded bg-[#2a2a2a] border border-gray-700 text-white hover:bg-[#383838] disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            +
                        </button>
                    </div>
                </div>

                <div className="text-right min-w-25">
                    <p className="text-white font-semibold">$ {subtotal.toFixed(2)}</p>
                </div>

                <button
                    type="button"
                    onClick={onRemove}
                    className="p-2 text-red-400 hover:text-red-300 hover:bg-red-900/20 rounded transition"
                    title="Remove product"
                >
                    <svg
                        className="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>);
}