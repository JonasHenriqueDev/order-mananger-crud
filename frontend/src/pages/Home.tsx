import {useEffect, useState} from 'react';
import {useNavigate} from 'react-router-dom';
import type {Order, Product} from "../shared/services/types.ts";
import {useAuth} from "../shared/hooks/useAuth.ts";
import {productService} from "../shared/services/productService.ts";
import {orderService} from "../shared/services/orderService.ts";
import Loading from "../shared/components/Loading.tsx";
import ErrorMessage from "../shared/components/ErrorMessage.tsx";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "../shared/components/Table.tsx";
import Badge from "../shared/components/Badge.tsx";
import EmptyState from "../shared/components/EmptyState.tsx";
import Container from "../shared/components/Container.tsx";
import Pagination from "../shared/components/Pagination.tsx";

export default function Home() {
    const navigate = useNavigate();
    const {user, loading: authLoading, isAdminOrManager} = useAuth();

    const [products, setProducts] = useState<Product[]>([]);
    const [productsCurrentPage, setProductsCurrentPage] = useState(1);
    const [productsLastPage, setProductsLastPage] = useState(1);
    const [productsTotal, setProductsTotal] = useState(0);

    const [orders, setOrders] = useState<Order[]>([]);
    const [ordersCurrentPage, setOrdersCurrentPage] = useState(1);
    const [ordersLastPage, setOrdersLastPage] = useState(1);
    const [ordersTotal, setOrdersTotal] = useState(0);

    const [loadingProducts, setLoadingProducts] = useState(false);
    const [loadingOrders, setLoadingOrders] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!authLoading && isAdminOrManager()) {
            void fetchProducts(1);
            void fetchOrders(1);
        }
    }, [authLoading, user]);

    const fetchProducts = async (page: number) => {
        setLoadingProducts(true);
        setError(null);

        try {
            const productsData = await productService.getProducts(page);

            setProducts(productsData.data);
            setProductsCurrentPage(productsData.meta.current_page);
            setProductsLastPage(productsData.meta.last_page);
            setProductsTotal(productsData.meta.total);
        } catch (err) {
            setError('Error loading products. Check your authentication.');
            console.error(err);
        } finally {
            setLoadingProducts(false);
        }
    };

    const fetchOrders = async (page: number) => {
        setLoadingOrders(true);
        setError(null);

        try {
            const ordersData = await orderService.getOrders(page);

            setOrders(ordersData.data);
            setOrdersCurrentPage(ordersData.meta.current_page);
            setOrdersLastPage(ordersData.meta.last_page);
            setOrdersTotal(ordersData.meta.total);
        } catch (err) {
            setError('Error loading orders. Check your authentication.');
            console.error(err);
        } finally {
            setLoadingOrders(false);
        }
    };

    const handleProductPageChange = (page: number) => {
        void fetchProducts(page);
    };

    const handleOrderPageChange = (page: number) => {
        void fetchOrders(page);
    };

    const getStockVariant = (stock: number): "success" | "warning" | "danger" => {
        if (stock > 10) return "success";
        if (stock > 0) return "warning";
        return "danger";
    };

    if (authLoading) {
        return <Loading message="Loading..."/>;
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

    return (<div className="min-h-screen bg-[#212121] space-y-8 p-6">
            {/* Header */}
            <Container>
                <h1 className="text-3xl font-bold text-white">Dashboard</h1>
                <p className="text-gray-400 mt-2">
                    Welcome, {user.first_name} {user.last_name}{" "}
                    <span className="text-blue-400">({user.role})</span>
                </p>
            </Container>

            {error && <ErrorMessage message={error}/>}

            {/* Products Section */}
            <Container>
                <div className="flex items-center justify-between mb-4">
                    <div>
                        <h2 className="text-2xl font-bold text-white">Products</h2>
                        <span className="text-sm text-gray-400">
                            {productsTotal} products found
                        </span>
                    </div>
                    <button
                        onClick={() => navigate('/products/create')}
                        className="bg-blue-600 hover:bg-blue-700 transition rounded-lg px-4 py-2 font-medium text-white text-sm"
                    >
                        + New Product
                    </button>
                </div>

                {loadingProducts ? (<div className="text-center py-8 text-gray-400">
                        Loading products...
                    </div>) : products.length === 0 ? (<EmptyState message="No products found"/>) : (<>
                        <Table>
                            <TableHeader>
                                <TableHead>SKU</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Price</TableHead>
                                <TableHead>Stock</TableHead>
                                <TableHead>Status</TableHead>
                            </TableHeader>
                            <TableBody>
                                {products.map((product) => (<TableRow key={product.id}>
                                        <TableCell className="font-medium text-gray-200">
                                            {product.sku}
                                        </TableCell>
                                        <TableCell>{product.name}</TableCell>
                                        <TableCell>
                                            $ {parseFloat(product.price).toFixed(2)}
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant={getStockVariant(product.stock)}>
                                                {product.stock}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                variant={product.status === "active" ? "success" : "default"}
                                            >
                                                {product.status}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>))}
                            </TableBody>
                        </Table>
                        <Pagination
                            currentPage={productsCurrentPage}
                            lastPage={productsLastPage}
                            onPageChange={handleProductPageChange}
                        />
                    </>)}
            </Container>

            {/* Orders Section */}
            <Container>
                <div className="flex items-center justify-between mb-4">
                    <div>
                        <h2 className="text-2xl font-bold text-white">Orders</h2>
                        <span className="text-sm text-gray-400">
                {ordersTotal} orders found
            </span>
                    </div>
                    <button
                        onClick={() => navigate('/orders/create')}
                        className="bg-blue-600 hover:bg-blue-700 transition rounded-lg px-4 py-2 font-medium text-white text-sm"
                    >
                        + New Order
                    </button>
                </div>

                {loadingOrders ? (<div className="text-center py-8 text-gray-400">
                        Loading orders...
                    </div>) : orders.length === 0 ? (<EmptyState message="No orders found"/>) : (<>
                        <Table>
                            <TableHeader>
                                <TableHead>Number</TableHead>
                                <TableHead>Customer</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Total</TableHead>
                                <TableHead>Date</TableHead>
                            </TableHeader>
                            <TableBody>
                                {orders.map((order) => (<TableRow key={order.id}>
                                        <TableCell className="font-medium text-gray-200">
                                            {order.order_number}
                                        </TableCell>
                                        <TableCell>
                                            {order.user.first_name} {order.user.last_name}
                                        </TableCell>
                                        <TableCell>
                                            <span
                                                className="px-2 py-1 rounded border"
                                                style={{
                                                    backgroundColor: order.status_color + "20",
                                                    color: order.status_color,
                                                    borderColor: order.status_color + "40",
                                                }}
                                            >
                                                {order.status_label}
                                            </span>
                                        </TableCell>
                                        <TableCell className="font-semibold text-gray-200">
                                            $ {parseFloat(order.total).toFixed(2)}
                                        </TableCell>
                                        <TableCell className="text-gray-400">
                                            {new Date(order.created_at).toLocaleDateString("en-US")}
                                        </TableCell>
                                    </TableRow>))}
                            </TableBody>
                        </Table>
                        <Pagination
                            currentPage={ordersCurrentPage}
                            lastPage={ordersLastPage}
                            onPageChange={handleOrderPageChange}
                        />
                    </>)}
            </Container>
        </div>);
}