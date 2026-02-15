import { api } from "./api";
import type { LoginResponse } from "./types";

export interface RegisterPayload {
    first_name: string;
    last_name: string;
    email: string;
    password: string;
    password_confirmation: string;
    role?: "user" | "manager" | "admin";
}

export const authService = {
    async login(email: string, password: string): Promise<LoginResponse> {
        const response = await api.post<LoginResponse>("/login", {
            email,
            password,
        });

        return response.data;
    },

    async register(data: RegisterPayload): Promise<LoginResponse> {
        const response = await api.post<LoginResponse>("/register", data);
        return response.data;
    },
};
