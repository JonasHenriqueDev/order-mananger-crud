import { api } from "./api";
import type { PaginatedResponse, Order } from "./types";

export interface CreateOrderItem {
    product_id: number;
    quantity: number;
}

export interface CreateOrderPayload {
    user_id?: number;
    items: CreateOrderItem[];
    tax?: number;
    discount?: number;
    notes?: string;
}

export const orderService = {
    async getOrders(page = 1): Promise<PaginatedResponse<Order>> {
        const response = await api.get<PaginatedResponse<Order>>(
            `/orders?page=${page}`
        );
        return response.data;
    },

    async getOrder(id: number): Promise<Order> {
        const response = await api.get<{ data: Order }>(`/orders/${id}`);
        return response.data.data;
    },

    async getMyOrders(page = 1): Promise<PaginatedResponse<Order>> {
        const response = await api.get<PaginatedResponse<Order>>(
            `/my-orders?page=${page}`
        );
        return response.data;
    },

    async createOrder(data: CreateOrderPayload): Promise<Order> {
        const response = await api.post<{ data: Order }>("/orders", data);
        return response.data.data;
    },

    async cancelOrder(id: number): Promise<Order> {
        const response = await api.post<{ data: Order }>(`/orders/${id}/cancel`);
        return response.data.data;
    },
};