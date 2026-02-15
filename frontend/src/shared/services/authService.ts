import { api } from "./api.ts";
import type {LoginResponse, User} from "./types.ts";

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

    async me(): Promise<User> {
        const response = await api.get<User>("/me");
        return response.data;
    }
};
