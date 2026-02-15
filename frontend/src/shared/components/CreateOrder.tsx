import { useNavigate } from "react-router-dom";
import OrderProductItem from "../components/OrderProductItem";
import ProductSelectionModal from "../components/ProductSelectionModal";
import type {Product, User} from "../services/types.ts";
import {type SyntheticEvent, useEffect, useState} from "react";
import {useAuth} from "../hooks/useAuth.ts";
import { userService } from "../services/userService.ts";
import {orderService} from "../services/orderService.ts";
import Container from "./Container.tsx";
import ErrorMessage from "./ErrorMessage.tsx";
import Select from "./Select.tsx";
import Input from "./Input.tsx";
import Textarea from "./TextArea.tsx";
import Button from "./Button.tsx";
import Loading from "./Loading.tsx";

interface OrderItem {
    product: Product;
    quantity: number;
}

export default function CreateOrder() {
    const navigate = useNavigate();
    const { user, loading: authLoading, isAdminOrManager } = useAuth();

    const [users, setUsers] = useState<User[]>([]);
    const [selectedUserId, setSelectedUserId] = useState<string>("");
    const [orderItems, setOrderItems] = useState<OrderItem[]>([]);
    const [tax, setTax] = useState("");
    const [discount, setDiscount] = useState("");
    const [notes, setNotes] = useState("");

    const [showProductModal, setShowProductModal] = useState(false);
    const [loading, setLoading] = useState(false);
    const [loadingUsers, setLoadingUsers] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [success, setSuccess] = useState(false);

    useEffect(() => {
        if (!authLoading && user) {
            if (isAdminOrManager()) {
                fetchUsers();
            } else {
                // Para usuários comuns, definir como seu próprio ID
                setSelectedUserId(user.id.toString());
            }
        }
    }, [authLoading, user]);

    const fetchUsers = async () => {
        setLoadingUsers(true);
        try {
            const data = await userService.getUsers(1);
            setUsers(data.data);
        } catch (err) {
            console.error("Error fetching users:", err);
        } finally {
            setLoadingUsers(false);
        }
    };

    const handleAddProduct = (product: Product) => {
        if (product.stock <= 0) {
            setError("This product is out of stock and cannot be added to the order.");
            return;
        }
        setOrderItems([...orderItems, { product, quantity: 1 }]);
        setError(null);
    };

    const handleQuantityChange = (index: number, quantity: number) => {
        const newItems = [...orderItems];
        newItems[index].quantity = quantity;
        setOrderItems(newItems);
    };

    const handleRemoveProduct = (index: number) => {
        setOrderItems(orderItems.filter((_, i) => i !== index));
    };

    const calculateSubtotal = () => {
        return orderItems.reduce(
            (sum, item) => sum + parseFloat(item.product.price) * item.quantity,
            0
        );
    };

    const calculateTotal = () => {
        const subtotal = calculateSubtotal();
        const taxAmount = parseFloat(tax) || 0;
        const discountAmount = parseFloat(discount) || 0;
        return subtotal + taxAmount - discountAmount;
    };

    const handleSubmit = async (e: SyntheticEvent) => {
        e.preventDefault();

        if (orderItems.length === 0) {
            setError("Please add at least one product to the order.");
            return;
        }

        for (const item of orderItems) {
            if (item.quantity > item.product.stock) {
                setError(
                    `Quantity for "${item.product.name}" exceeds available stock (${item.product.stock}).`
                );
                return;
            }
            if (item.product.stock <= 0) {
                setError(`Product "${item.product.name}" is out of stock.`);
                return;
            }
        }

        setLoading(true);
        setError(null);

        try {
            const payload = {
                user_id: selectedUserId ? parseInt(selectedUserId) : undefined,
                items: orderItems.map((item) => ({
                    product_id: item.product.id,
                    quantity: item.quantity,
                })),
                tax: parseFloat(tax) || 0,
                discount: parseFloat(discount) || 0,
                notes: notes || undefined,
            };

            await orderService.createOrder(payload);

            setSuccess(true);
            setTimeout(() => {
                navigate("/");
            }, 2000);
        } catch (err: any) {
            setError(
                err.response?.data?.message || "Error creating order. Please try again."
            );
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    if (authLoading) {
        return <Loading />;
    }

    if (!user) {
        return (
            <div className="flex items-center justify-center min-h-screen bg-[#212121]">
                <div className="text-center">
                    <h2 className="text-2xl font-bold text-red-400 mb-2">Authentication Required</h2>
                    <p className="text-gray-400">
                        Please log in to create an order.
                    </p>
                </div>
            </div>
        );
    }

    const isAdmin = isAdminOrManager();

    const selectedProductIds = orderItems.map((item) => item.product.id);

    return (
        <div className="min-h-screen bg-[#212121] p-6">
            <div className="max-w-4xl mx-auto">
                <Container>
                    <div className="mb-6">
                        <h1 className="text-3xl font-bold text-white mb-2">Create Order</h1>
                        <p className="text-gray-400">
                            Fill in the information to create a new order
                        </p>
                    </div>

                    {error && <ErrorMessage message={error} />}

                    {success && (
                        <div className="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded mb-4">
                            Order created successfully! Redirecting...
                        </div>
                    )}

                    <form onSubmit={handleSubmit}>
                        {/* Customer Selection - Only for Admin/Manager */}
                        {isAdmin ? (
                            loadingUsers ? (
                                <div className="text-gray-400 mb-4">Loading users...</div>
                            ) : (
                                <Select
                                    label="Customer"
                                    value={selectedUserId}
                                    onChange={(e) => setSelectedUserId(e.target.value)}
                                    options={[
                                        { value: "", label: "Current User (Me)" },
                                        ...users.map((u) => ({
                                            value: u.id.toString(),
                                            label: `${u.first_name} ${u.last_name} (${u.email})`,
                                        })),
                                    ]}
                                />
                            )
                        ) : (
                            <div className="mb-6 p-4 bg-blue-900/30 border border-blue-700 rounded-lg">
                                <p className="text-blue-300">
                                    This order will be created under your name: <span className="font-semibold">{user.first_name} {user.last_name}</span>
                                </p>
                            </div>
                        )}

                        {/* Products Section */}
                        <div className="mb-6">
                            <div className="flex items-center justify-between mb-3">
                                <label className="block text-sm text-gray-300">
                                    Products *
                                </label>
                                <button
                                    type="button"
                                    onClick={() => setShowProductModal(true)}
                                    className="bg-blue-600 hover:bg-blue-700 transition rounded-lg px-4 py-2 text-sm font-medium text-white"
                                >
                                    + Add Product
                                </button>
                            </div>

                            {orderItems.length === 0 ? (
                                <div className="text-center py-8 bg-[#1a1a1a] rounded-lg border border-[#3d3d3d]">
                                    <p className="text-gray-500">No products added yet</p>
                                    <button
                                        type="button"
                                        onClick={() => setShowProductModal(true)}
                                        className="mt-2 text-blue-400 hover:text-blue-300"
                                    >
                                        Click here to add products
                                    </button>
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {orderItems.map((item, index) => (
                                        <OrderProductItem
                                            key={item.product.id}
                                            product={item.product}
                                            quantity={item.quantity}
                                            onQuantityChange={(qty) =>
                                                handleQuantityChange(index, qty)
                                            }
                                            onRemove={() => handleRemoveProduct(index)}
                                        />
                                    ))}
                                </div>
                            )}
                        </div>

                        {/* Order Summary */}
                        {orderItems.length > 0 && (
                            <div className="mb-6 p-4 bg-[#1a1a1a] rounded-lg border border-[#3d3d3d]">
                                <h3 className="text-lg font-semibold text-white mb-3">
                                    Order Summary
                                </h3>
                                <div className="space-y-2 text-sm">
                                    <div className="flex justify-between text-gray-300">
                                        <span>Subtotal:</span>
                                        <span>$ {calculateSubtotal().toFixed(2)}</span>
                                    </div>
                                    <div className="flex justify-between text-gray-300">
                                        <span>Tax:</span>
                                        <span>$ {(parseFloat(tax) || 0).toFixed(2)}</span>
                                    </div>
                                    <div className="flex justify-between text-gray-300">
                                        <span>Discount:</span>
                                        <span>- $ {(parseFloat(discount) || 0).toFixed(2)}</span>
                                    </div>
                                    <div className="flex justify-between text-xl font-bold text-white pt-2 border-t border-[#3d3d3d]">
                                        <span>Total:</span>
                                        <span>$ {calculateTotal().toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Tax and Discount */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Input
                                label="Tax ($)"
                                type="number"
                                value={tax}
                                onChange={(e) => setTax(e.target.value)}
                                placeholder="0.00"
                                step="0.01"
                                min="0"
                            />

                            <Input
                                label="Discount ($)"
                                type="number"
                                value={discount}
                                onChange={(e) => setDiscount(e.target.value)}
                                placeholder="0.00"
                                step="0.01"
                                min="0"
                            />
                        </div>

                        <Textarea
                            label="Notes"
                            value={notes}
                            onChange={(e) => setNotes(e.target.value)}
                            placeholder="Additional notes for this order..."
                            rows={3}
                        />

                        <div className="flex gap-4 mt-6">
                            <Button type="submit" disabled={loading || orderItems.length === 0}>
                                {loading ? "Creating..." : "Create Order"}
                            </Button>
                            <button
                                type="button"
                                onClick={() => navigate("/")}
                                className="w-full bg-gray-700 hover:bg-gray-600 transition rounded-lg py-3 font-medium text-white"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </Container>
            </div>

            {showProductModal && (
                <ProductSelectionModal
                    onSelect={handleAddProduct}
                    onClose={() => setShowProductModal(false)}
                    selectedProductIds={selectedProductIds}
                />
            )}
        </div>
    );
}