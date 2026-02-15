import { api } from "./api";
import type { PaginatedResponse, Product } from "./types";

export interface CreateProductPayload {
    name: string;
    description: string;
    price: number;
    stock: number;
    status?: "active" | "inactive";
    is_featured?: boolean;
}

export const productService = {
    async getProducts(page = 1): Promise<PaginatedResponse<Product>> {
        const response = await api.get<PaginatedResponse<Product>>(
            `/products?page=${page}`
        );
        return response.data;
    },

    async getProduct(id: number): Promise<Product> {
        const response = await api.get<Product>(`/products/${id}`);
        return response.data;
    },

    async createProduct(data: CreateProductPayload): Promise<Product> {
        const response = await api.post<Product>("/products", data);
        return response.data;
    },

    async updateProduct(id: number, data: Partial<CreateProductPayload>): Promise<Product> {
        const response = await api.put<Product>(`/products/${id}`, data);
        return response.data;
    },

    async deleteProduct(id: number): Promise<void> {
        await api.delete(`/products/${id}`);
    },
};