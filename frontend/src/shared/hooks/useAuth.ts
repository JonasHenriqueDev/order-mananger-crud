import { useState, useEffect } from 'react';
import { authService } from '../services/authService';
import type {User} from "../services/types.ts";

export const useAuth = () => {
    const [user, setUser] = useState<User | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchUser = async () => {
            const token = localStorage.getItem('token');

            if (!token) {
                setLoading(false);
                return;
            }

            try {
                const userData = await authService.me();
                setUser(userData);
            } catch (error) {
                console.error('Error fetching user:', error);
                localStorage.removeItem('token');
                setUser(null);
            } finally {
                setLoading(false);
            }
        };

        fetchUser().catch((error) => {
            console.error('Unexpected error in fetchUser:', error)
        });

    }, []);

    const isAdmin = () => user?.role === 'admin';
    const isManager = () => user?.role === 'manager';
    const isAdminOrManager = () => user?.role === 'admin' || user?.role === 'manager';

    return { user, loading, isAdmin, isManager, isAdminOrManager };
};