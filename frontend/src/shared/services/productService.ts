import { api } from "./api";
import type { PaginatedResponse, Product } from "./types";

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
};