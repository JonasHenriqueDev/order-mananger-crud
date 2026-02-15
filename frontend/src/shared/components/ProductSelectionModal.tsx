import { useState, useEffect } from "react";
import type {Product} from "../services/types.ts";
import {productService} from "../services/productService.ts";
import Badge from "./Badge.tsx";


interface ProductSelectionModalProps {
    onSelect: (product: Product) => void;
    onClose: () => void;
    selectedProductIds: number[];
}

export default function ProductSelectionModal({onSelect, onClose, selectedProductIds,}: ProductSelectionModalProps) {
    const [products, setProducts] = useState<Product[]>([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState("");

    useEffect(() => {
        fetchProducts();
    }, []);

    const fetchProducts = async () => {
        try {
            const data = await productService.getProducts(1, 100);
            setProducts(data.data.filter((p) => p.status === "active"));
        } catch (error) {
            console.error("Error fetching products:", error);
        } finally {
            setLoading(false);
        }
    };

    const filteredProducts = products.filter(
        (product) =>
            product.stock > 0 &&
            !selectedProductIds.includes(product.id) &&
            (product.name.toLowerCase().includes(search.toLowerCase()) ||
                product.sku.toLowerCase().includes(search.toLowerCase()))
    );

    return (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div className="bg-[#2a2a2a] rounded-lg max-w-3xl w-full max-h-[80vh] flex flex-col border border-[#3d3d3d]">
                <div className="p-6 border-b border-[#3d3d3d]">
                    <div className="flex items-center justify-between mb-4">
                        <h2 className="text-2xl font-bold text-white">Select Product</h2>
                        <button
                            onClick={onClose}
                            className="text-gray-400 hover:text-white transition"
                        >
                            <svg
                                className="w-6 h-6"
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

                    <input
                        type="text"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Search by name or SKU..."
                        className="w-full p-3 rounded-lg bg-[#1a1a1a] border border-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div className="flex-1 overflow-y-auto p-6">
                    {loading ? (
                        <div className="text-center py-8 text-gray-400">
                            Loading products...
                        </div>
                    ) : filteredProducts.length === 0 ? (
                        <div className="text-center py-8 text-gray-500">
                            No products available
                        </div>
                    ) : (
                        <div className="space-y-3">
                            {filteredProducts.map((product) => (
                                <button
                                    key={product.id}
                                    onClick={() => {
                                        onSelect(product);
                                        onClose();
                                    }}
                                    className="w-full p-4 bg-[#1a1a1a] hover:bg-[#383838] rounded-lg border border-[#3d3d3d] transition text-left"
                                >
                                    <div className="flex items-center justify-between">
                                        <div className="flex-1">
                                            <h3 className="text-white font-medium">
                                                {product.name}
                                            </h3>
                                            <p className="text-sm text-gray-400 mt-1">
                                                SKU: {product.sku}
                                            </p>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-white font-semibold">
                                                $ {parseFloat(product.price).toFixed(2)}
                                            </p>
                                            <Badge
                                                variant={
                                                    product.stock > 10
                                                        ? "success"
                                                        : product.stock > 0
                                                            ? "warning"
                                                            : "danger"
                                                }
                                                className="mt-1"
                                            >
                                                Stock: {product.stock}
                                            </Badge>
                                        </div>
                                    </div>
                                </button>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}