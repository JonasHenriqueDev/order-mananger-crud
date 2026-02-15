import { api } from "./api";
import type {LoginResponse} from "./types";

export const authService = {
    async login(email: string, password: string): Promise<LoginResponse> {
        const response = await api.post<LoginResponse>("/login", {
            email,
            password,
        });

        return response.data;
    },
};
