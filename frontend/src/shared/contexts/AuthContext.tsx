import type {User} from "../../services/types.ts";
import {createContext, useCallback, useContext, useEffect, useState} from "react";
import {authService, type RegisterPayload} from "../../services/authService.ts";


interface IAuthContextProps {
    user?: User;
    token?: string;

    login(email: string, password: string): Promise<void>;
    register(data: RegisterPayload): Promise<void>;
    logout(): void;
}


const AuthContext = createContext({} as IAuthContextProps);

export const AuthProvider = ({ children }: React.PropsWithChildren) => {
    const [token, setToken] = useState<string>();
    const [user, setUser] = useState<User>();

    useEffect(() => {
        const storedToken = localStorage.getItem("token");
        const storedUser = localStorage.getItem("user");

        if (storedToken && storedUser) {
            setToken(storedToken);
            setUser(JSON.parse(storedUser));
        }
    }, []);

    const login = useCallback(async (email: string, password: string) => {
        const data = await authService.login(email, password);

        setToken(data.token);
        setUser(data.user);

        localStorage.setItem("token", data.token);
        localStorage.setItem("user", JSON.stringify(data.user));
    }, []);

    const register = async (data: RegisterPayload) => {
        const response = await authService.register(data);

        localStorage.setItem("token", response.token);
        setUser(response.user);
    };

    const logout = useCallback(() => {
        setToken(undefined);
        setUser(undefined);

        localStorage.removeItem("token");
        localStorage.removeItem("user");
    }, []);

    return (
        <AuthContext.Provider value={{ login, register, logout, token, user }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuthContext = () => useContext(AuthContext);

export const useIsAuthenticated = () => {
    const { token } = useAuthContext();
    return !!token;
};
