import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../shared/hooks/useAuth";
import { orderService } from "../shared/services/orderService";
import type { Order } from "../shared/services/types";
import Container from "../shared/components/Container";
import Loading from "../shared/components/Loading";
import ErrorMessage from "../shared/components/ErrorMessage";
import Badge from "../shared/components/Badge";
import Button from "../shared/components/Button";
import Pagination from "../shared/components/Pagination";
import { Table, TableBody, TableCell, TableHeader, TableHead, TableRow } from "../shared/components/Table";

export default function MyOrders() {
    const navigate = useNavigate();
    const { user, loading: authLoading } = useAuth();

    const [orders, setOrders] = useState<Order[]>([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!authLoading && user) {
            fetchMyOrders(1);
        }
    }, [authLoading, user]);

    const fetchMyOrders = async (page: number) => {
        setLoading(true);
        setError(null);

        try {
            const ordersData = await orderService.getMyOrders(page);

            setOrders(ordersData.data);
            setCurrentPage(ordersData.meta.current_page);
            setLastPage(ordersData.meta.last_page);
            setTotal(ordersData.meta.total);
        } catch (err: any) {
            setError(err.response?.data?.message || "Error loading your orders.");
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const handlePageChange = (page: number) => {
        fetchMyOrders(page);
    };

    const handleViewOrder = (orderId: number) => {
        navigate(`/orders/${orderId}`);
    };

    const formatDate = (dateString: string): string => {
        return new Date(dateString).toLocaleDateString("en-US", {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
        });
    };

    const formatCurrency = (value: string): string => {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "USD",
        }).format(parseFloat(value));
    };

    if (authLoading || loading) {
        return <Loading message="Loading your orders..." />;
    }

    if (!user) {
        return (
            <div className="flex items-center justify-center min-h-screen bg-[#212121]">
                <div className="text-center">
                    <h2 className="text-2xl font-bold text-red-400 mb-2">
                        Authentication Required
                    </h2>
                    <p className="text-gray-400 mb-4">
                        Please log in to view your orders.
                    </p>
                    <Button onClick={() => navigate("/login")}>Go to Login</Button>
                </div>
            </div>
        );
    }

    return (
        <Container>
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold text-white">
                            My Orders
                        </h1>
                        <p className="text-gray-400 mt-1">
                            {user.first_name}, here are all your orders
                        </p>
                    </div>
                </div>

                {error && <ErrorMessage message={error} />}

                {orders.length === 0 ? (
                    <div className="text-center py-12">
                        <h2 className="text-2xl font-semibold text-gray-300 mb-2">
                            No Orders Yet
                        </h2>
                        <p className="text-gray-400 mb-6">
                            You haven't placed any orders yet. Start shopping to create your first order.
                        </p>
                        <Button onClick={() => navigate("/orders/create")}>
                            Create Order
                        </Button>
                    </div>
                ) : (
                    <>
                        <div className="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableHead>Order #</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Date</TableHead>
                                    <TableHead>Items</TableHead>
                                    <TableHead>Total</TableHead>
                                    <TableHead>Actions</TableHead>
                                </TableHeader>
                                <TableBody>
                                    {orders.map((order) => (
                                        <TableRow key={order.id}>
                                            <TableCell className="font-semibold text-white">
                                                {order.order_number}
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant={getStatusVariant(order.status)}>
                                                    {order.status_label}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="text-gray-300">
                                                {formatDate(order.created_at)}
                                            </TableCell>
                                            <TableCell className="text-gray-300">
                                                {order.items?.length || 0} item{(order.items?.length || 0) !== 1 ? 's' : ''}
                                            </TableCell>
                                            <TableCell className="text-white font-semibold">
                                                {formatCurrency(order.total)}
                                            </TableCell>
                                            <TableCell>
                                                <button
                                                    onClick={() => handleViewOrder(order.id)}
                                                    className="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition duration-200"
                                                >
                                                    View
                                                </button>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </div>

                        {lastPage > 1 && (
                            <Pagination
                                currentPage={currentPage}
                                lastPage={lastPage}
                                onPageChange={handlePageChange}
                            />
                        )}

                        <div className="text-right text-sm text-gray-400">
                            Showing {orders.length} of {total} orders
                        </div>
                    </>
                )}
            </div>
        </Container>
    );
}

function getStatusVariant(
    status: string
): "success" | "warning" | "danger" | "default" {
    switch (status?.toLowerCase()) {
        case "completed":
            return "success";
        case "processing":
        case "pending":
            return "warning";
        case "cancelled":
            return "danger";
        default:
            return "default";
    }
}






