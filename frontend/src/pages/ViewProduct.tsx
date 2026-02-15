import {useEffect, useState} from "react";
import {useNavigate, useParams} from "react-router-dom";
import {useAuth} from "../shared/hooks/useAuth";
import {productService} from "../shared/services/productService";
import type {Product} from "../shared/services/types";
import Container from "../shared/components/Container";
import Badge from "../shared/components/Badge";
import Loading from "../shared/components/Loading";
import ErrorMessage from "../shared/components/ErrorMessage";

export default function ViewProduct() {
    const {id} = useParams<{ id: string }>();
    const navigate = useNavigate();
    const {user, loading: authLoading, isAdminOrManager} = useAuth();

    const [product, setProduct] = useState<Product | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!authLoading && isAdminOrManager() && id) {
            fetchProduct();
        }
    }, [authLoading, id, user]);

    const fetchProduct = async () => {
        if (!id) return;

        setLoading(true);
        try {
            const data = await productService.getProduct(parseInt(id));
            console.log('Product data:', data); // DEBUG
            setProduct(data);
            setError(null);
        } catch (err: any) {
            setError(err.response?.data?.message || "Error loading product.");
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const getStockVariant = (stock: number): "success" | "warning" | "danger" => {
        if (stock > 10) return "success";
        if (stock > 0) return "warning";
        return "danger";
    };

    if (authLoading || loading) {
        return <Loading message="Loading product..."/>;
    }

    if (!user || !isAdminOrManager()) {
        return (<div className="flex items-center justify-center min-h-screen bg-[#212121]">
                <div className="text-center">
                    <h2 className="text-2xl font-bold text-red-400 mb-2">Access Denied</h2>
                    <p className="text-gray-400">
                        You need to be Admin or Manager to access this page.
                    </p>
                </div>
            </div>);
    }

    if (error || !product) {
        return (<div className="min-h-screen bg-[#212121] p-6">
                <div className="max-w-4xl mx-auto">
                    <Container>
                        <ErrorMessage message={error || "Product not found"}/>
                        <button
                            onClick={() => navigate("/")}
                            className="mt-4 bg-blue-600 hover:bg-blue-700 transition rounded-lg px-4 py-2 text-white"
                        >
                            Back to Dashboard
                        </button>
                    </Container>
                </div>
            </div>);
    }

    return (<div className="min-h-screen bg-[#212121] p-6">
            <div className="max-w-4xl mx-auto space-y-6">
                {/* Header */}
                <Container>
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold text-white mb-2">
                                {product.name}
                            </h1>
                            <p className="text-gray-400">Product Details</p>
                        </div>
                        <button
                            onClick={() => navigate("/")}
                            className="bg-gray-700 hover:bg-gray-600 transition rounded-lg px-4 py-2 text-white"
                        >
                            Back
                        </button>
                    </div>
                </Container>

                {/* Product Information */}
                <Container>
                    <h2 className="text-xl font-bold text-white mb-4">Information</h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label className="block text-sm text-gray-400 mb-1">SKU</label>
                            <p className="text-white font-medium">{product.sku}</p>
                        </div>

                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Slug</label>
                            <p className="text-white font-medium">{product.slug}</p>
                        </div>

                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Price</label>
                            <p className="text-white font-medium text-lg">
                                $ {parseFloat(product.price).toFixed(2)}
                            </p>
                        </div>

                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Stock</label>
                            <Badge variant={getStockVariant(product.stock)}>
                                {product.stock} {product.stock === 1 ? 'unit' : 'units'}
                            </Badge>
                        </div>

                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Status</label>
                            <Badge
                                variant={product.status === "active" ? "success" : "default"}
                            >
                                {product.status.charAt(0).toUpperCase() + product.status.slice(1)}
                            </Badge>
                        </div>

                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Featured</label>
                            <Badge variant={product.is_featured ? "info" : "default"}>
                                {product.is_featured ? "Yes" : "No"}
                            </Badge>
                        </div>
                    </div>

                    <div className="mt-6">
                        <label className="block text-sm text-gray-400 mb-2">Description</label>
                        <div className="text-white bg-[#1a1a1a] p-4 rounded-lg border border-[#3d3d3d]">
                            {product.description || "No description available"}
                        </div>
                    </div>

                    {product.metadata && Object.keys(product.metadata).length > 0 && (<div className="mt-6">
                            <label className="block text-sm text-gray-400 mb-2">Metadata</label>
                            <pre
                                className="text-white bg-[#1a1a1a] p-4 rounded-lg border border-[#3d3d3d] overflow-x-auto text-sm">
                                {JSON.stringify(product.metadata, null, 2)}
                            </pre>
                        </div>)}
                </Container>

                {/* Timestamps */}
                <Container>
                    <h2 className="text-xl font-bold text-white mb-4">Timestamps</h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Created At</label>
                            <p className="text-white">
                                {product.created_at ? new Date(product.created_at).toLocaleString("en-US", {
                                    year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                                }) : "N/A"}
                            </p>
                        </div>

                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Updated At</label>
                            <p className="text-white">
                                {product.updated_at ? new Date(product.updated_at).toLocaleString("en-US", {
                                    year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                                }) : "N/A"}
                            </p>
                        </div>

                        {product.deleted_at && (<div>
                                <label className="block text-sm text-gray-400 mb-1">
                                    Deleted At
                                </label>
                                <p className="text-red-400">
                                    {new Date(product.deleted_at).toLocaleString("en-US", {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })}
                                </p>
                            </div>)}
                    </div>
                </Container>
            </div>
        </div>);
}