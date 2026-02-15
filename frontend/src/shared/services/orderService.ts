import { api } from "./api";
import type { PaginatedResponse, Order } from "./types";

export const orderService = {
    async getOrders(page = 1): Promise<PaginatedResponse<Order>> {
        const response = await api.get<PaginatedResponse<Order>>(
            `/orders?page=${page}`
        );
        return response.data;
    },

    async getOrder(id: number): Promise<Order> {
        const response = await api.get<Order>(`/orders/${id}`);
        return response.data;
    },

    async getMyOrders(page = 1): Promise<PaginatedResponse<Order>> {
        const response = await api.get<PaginatedResponse<Order>>(
            `/my-orders?page=${page}`
        );
        return response.data;
    },
};