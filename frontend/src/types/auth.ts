export interface User {
    id: number;
    name: string;
    email: string;
}

export interface LoginResponse {
    token: string;
    user: User;
}

export interface LoginPayload {
    email: string;
    password: string;
}
