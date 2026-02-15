import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { useAuth } from "../shared/hooks/useAuth";
import { orderService } from "../shared/services/orderService";
import type { Order } from "../shared/services/types";
import Container from "../shared/components/Container";
import Loading from "../shared/components/Loading";
import ErrorMessage from "../shared/components/ErrorMessage";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "../shared/components/Table";

export default function ViewOrder() {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const { user, loading: authLoading, isAdminOrManager } = useAuth();

    const [order, setOrder] = useState<Order | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!authLoading && isAdminOrManager() && id) {
            fetchOrder();
        }
    }, [authLoading, id]);

    const fetchOrder = async () => {
        try {
            const data = await orderService.getOrder(parseInt(id!));
            setOrder(data);
        } catch (err: any) {
            setError(err.response?.data?.message || "Error loading order.");
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    if (authLoading || loading) {
        return <Loading message="Loading order..." />;
    }

    if (!user || !isAdminOrManager()) {
        return (
            <div className="flex items-center justify-center min-h-screen bg-[#212121]">
                <div className="text-center">
                    <h2 className="text-2xl font-bold text-red-400 mb-2">Access Denied</h2>
                    <p className="text-gray-400">
                        You need to be Admin or Manager to access this page.
                    </p>
                </div>
            </div>
        );
    }

    if (error || !order) {
        return (
            <div className="min-h-screen bg-[#212121] p-6">
                <div className="max-w-4xl mx-auto">
                    <Container>
                        <ErrorMessage message={error || "Order not found"} />
                        <button
                            onClick={() => navigate("/")}
                            className="mt-4 bg-blue-600 hover:bg-blue-700 transition rounded-lg px-4 py-2 text-white"
                        >
                            Back to Dashboard
                        </button>
                    </Container>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-[#212121] p-6">
            <div className="max-w-4xl mx-auto space-y-6">
                {/* Header */}
                <Container>
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold text-white mb-2">
                                Order {order.order_number}
                            </h1>
                            <p className="text-gray-400">Order Details</p>
                        </div>
                        <button
                            onClick={() => navigate("/")}
                            className="bg-gray-700 hover:bg-gray-600 transition rounded-lg px-4 py-2 text-white"
                        >
                            Back
                        </button>
                    </div>
                </Container>

                {/* Order Information */}
                <Container>
                    <h2 className="text-xl font-bold text-white mb-4">Order Information</h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Order Number</label>
                            <p className="text-white font-medium">{order.order_number}</p>
                        </div>

                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Status</label>
                            <span
                                className="inline-block px-3 py-1 rounded border"
                                style={{
                                    backgroundColor: order.status_color + "20",
                                    color: order.status_color,
                                    borderColor: order.status_color + "40",
                                }}
                            >
                                {order.status_label}
                            </span>
                        </div>

                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Customer</label>
                            <p className="text-white">
                                {order.user.first_name} {order.user.last_name}
                            </p>
                            <p className="text-sm text-gray-400">{order.user.email}</p>
                        </div>

                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Order Date</label>
                            <p className="text-white">
                                {new Date(order.created_at).toLocaleString("en-US")}
                            </p>
                        </div>
                    </div>

                    {order.notes && (
                        <div className="mt-6">
                            <label className="block text-sm text-gray-400 mb-1">Notes</label>
                            <p className="text-white bg-[#1a1a1a] p-4 rounded-lg border border-[#3d3d3d]">
                                {order.notes}
                            </p>
                        </div>
                    )}
                </Container>

                {/* Order Items */}
                {order.items && order.items.length > 0 && (
                    <Container>
                        <h2 className="text-xl font-bold text-white mb-4">Order Items</h2>
                        <Table>
                            <TableHeader>
                                <TableHead>Product</TableHead>
                                <TableHead>SKU</TableHead>
                                <TableHead>Price</TableHead>
                                <TableHead>Quantity</TableHead>
                                <TableHead>Subtotal</TableHead>
                            </TableHeader>
                            <TableBody>
                                {order.items.map((item) => (
                                    <TableRow key={item.id}>
                                        <TableCell className="font-medium text-gray-200">
                                            {item.product_name}
                                        </TableCell>
                                        <TableCell>{item.product_sku}</TableCell>
                                        <TableCell>$ {parseFloat(item.price).toFixed(2)}</TableCell>
                                        <TableCell>{item.quantity}</TableCell>
                                        <TableCell className="font-semibold">
                                            $ {parseFloat(item.subtotal).toFixed(2)}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </Container>
                )}

                {/* Order Summary */}
                <Container>
                    <h2 className="text-xl font-bold text-white mb-4">Order Summary</h2>
                    <div className="space-y-3">
                        <div className="flex justify-between text-gray-300">
                            <span>Subtotal:</span>
                            <span className="font-medium">
                                $ {parseFloat(order.subtotal).toFixed(2)}
                            </span>
                        </div>
                        <div className="flex justify-between text-gray-300">
                            <span>Tax:</span>
                            <span className="font-medium">
                                $ {parseFloat(order.tax).toFixed(2)}
                            </span>
                        </div>
                        <div className="flex justify-between text-gray-300">
                            <span>Discount:</span>
                            <span className="font-medium">
                                - $ {parseFloat(order.discount).toFixed(2)}
                            </span>
                        </div>
                        <div className="flex justify-between text-2xl font-bold text-white pt-3 border-t border-[#3d3d3d]">
                            <span>Total:</span>
                            <span>$ {parseFloat(order.total).toFixed(2)}</span>
                        </div>
                    </div>
                </Container>

                {/* Timestamps */}
                <Container>
                    <h2 className="text-xl font-bold text-white mb-4">Timeline</h2>
                    <div className="space-y-4">
                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Created At</label>
                            <p className="text-white">
                                {new Date(order.created_at).toLocaleString("en-US")}
                            </p>
                        </div>

                        {order.processed_at && (
                            <div>
                                <label className="block text-sm text-gray-400 mb-1">
                                    Processed At
                                </label>
                                <p className="text-white">
                                    {new Date(order.processed_at).toLocaleString("en-US")}
                                </p>
                            </div>
                        )}

                        {order.completed_at && (
                            <div>
                                <label className="block text-sm text-gray-400 mb-1">
                                    Completed At
                                </label>
                                <p className="text-green-400">
                                    {new Date(order.completed_at).toLocaleString("en-US")}
                                </p>
                            </div>
                        )}

                        {order.cancelled_at && (
                            <div>
                                <label className="block text-sm text-gray-400 mb-1">
                                    Cancelled At
                                </label>
                                <p className="text-red-400">
                                    {new Date(order.cancelled_at).toLocaleString("en-US")}
                                </p>
                            </div>
                        )}

                        <div>
                            <label className="block text-sm text-gray-400 mb-1">Updated At</label>
                            <p className="text-white">
                                {new Date(order.updated_at).toLocaleString("en-US")}
                            </p>
                        </div>
                    </div>
                </Container>
            </div>
        </div>
    );
}