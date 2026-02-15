import { api } from "./api";
import type { PaginatedResponse, User } from "./types";

export const userService = {
    async getUsers(page = 1): Promise<PaginatedResponse<User>> {
        const response = await api.get<PaginatedResponse<User>>(
            `/users?page=${page}`
        );
        return response.data;
    },

    async getUser(id: number): Promise<User> {
        const response = await api.get<User>(`/users/${id}`);
        return response.data;
    },
};