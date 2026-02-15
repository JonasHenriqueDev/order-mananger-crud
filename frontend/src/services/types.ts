export interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    role: string;
    active: boolean;
    created_at: string;
    updated_at: string;
}

export interface LoginResponse {
    token: string;
    user: User;
}
