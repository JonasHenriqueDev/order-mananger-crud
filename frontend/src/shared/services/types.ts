export interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    email_verified_at: boolean,
    role: 'admin' | 'manager' | 'user';
    active: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string;
}
export interface LoginResponse {
    token: string;
    user: User;
}

export interface Product {
    id: number;
    name: string;
    slug: string;
    sku: string;
    description: string;
    price: string;
    stock: number;
    status: string;
    is_featured: boolean;
    metadata: Record<string, any>;
    created_at: string;
    updated_at: string;
    deleted_at?: string;
}

export interface Order {
    id: number;
    order_number: string;
    status: string;
    status_label: string;
    status_color: string;
    subtotal: string;
    tax: string;
    discount: string;
    total: string;
    notes?: string;
    processed_at?: string;
    completed_at?: string;
    cancelled_at?: string;
    created_at: string;
    updated_at: string;
    user: {
        id: number;
        first_name: string;
        last_name: string;
        email: string;
    };
    items?: OrderItem[];
}

export interface OrderItem {
    id: number;
    order_id: number;
    product_id: number;
    product_name: string;
    product_sku: string;
    price: string;
    quantity: number;
    subtotal: string;
}

export interface PaginatedResponse<T> {
    data: T[];
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        links: Array<{
            url: string | null;
            label: string;
            page: number | null;
            active: boolean;
        }>;
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}