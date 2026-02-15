import { useNavigate } from "react-router-dom";
import { useAuth } from "../shared/hooks/useAuth";
import { productService } from "../shared/services/productService";
import Container from "../shared/components/Container";
import Input from "../shared/components/Input";
import Select from "../shared/components/Select";
import Button from "../shared/components/Button";
import ErrorMessage from "../shared/components/ErrorMessage";
import Loading from "../shared/components/Loading";
import {type SyntheticEvent, useState} from "react";
import Textarea from "../shared/components/TextArea.tsx";
import Checkbox from "../shared/components/CheckBox.tsx";

export default function CreateProduct() {
    const navigate = useNavigate();
    const { user, loading: authLoading, isAdminOrManager } = useAuth();

    const [formData, setFormData] = useState({
        name: "",
        description: "",
        price: "",
        stock: "",
        status: "active",
        is_featured: false,
    });

    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [success, setSuccess] = useState(false);

    const handleSubmit = async (e: SyntheticEvent) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        try {
            await productService.createProduct({
                name: formData.name,
                description: formData.description,
                price: parseFloat(formData.price),
                stock: parseInt(formData.stock),
                status: formData.status as "active" | "inactive",
                is_featured: formData.is_featured,
            });

            setSuccess(true);
            setTimeout(() => {
                navigate("/");
            }, 2000);
        } catch (err: any) {
            setError(err.response?.data?.message || "Error creating product. Please try again.");
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    if (authLoading) {
        return <Loading />;
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

    return (
        <div className="min-h-screen bg-[#212121] p-6">
            <div className="max-w-2xl mx-auto">
                <Container>
                    <div className="mb-6">
                        <h1 className="text-3xl font-bold text-white mb-2">Create Product</h1>
                        <p className="text-gray-400">Fill in the information to add a new product</p>
                    </div>

                    {error && <ErrorMessage message={error} />}

                    {success && (
                        <div className="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded mb-4">
                            Product created successfully! Redirecting...
                        </div>
                    )}

                    <form onSubmit={handleSubmit}>
                        <Input
                            label="Product Name"
                            type="text"
                            value={formData.name}
                            onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                            placeholder="Enter product name"
                            required
                        />

                        <Textarea
                            label="Description"
                            value={formData.description}
                            onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                            placeholder="Enter product description"
                            required
                        />

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Input
                                label="Price ($)"
                                type="number"
                                value={formData.price}
                                onChange={(e) => setFormData({ ...formData, price: e.target.value })}
                                placeholder="0.00"
                                required
                            />

                            <Input
                                label="Stock Quantity"
                                type="number"
                                value={formData.stock}
                                onChange={(e) => setFormData({ ...formData, stock: e.target.value })}
                                placeholder="0"
                                required
                            />
                        </div>

                        <Select
                            label="Status"
                            value={formData.status}
                            onChange={(e) => setFormData({ ...formData, status: e.target.value })}
                            options={[
                                { value: "active", label: "Active" },
                                { value: "inactive", label: "Inactive" },
                            ]}
                        />

                        <Checkbox
                            label="Featured Product"
                            checked={formData.is_featured}
                            onChange={(e) =>
                                setFormData({ ...formData, is_featured: e.target.checked })
                            }
                        />

                        <div className="flex gap-4 mt-6">
                            <Button type="submit" disabled={loading}>
                                {loading ? "Creating..." : "Create Product"}
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
        </div>
    );
}