import { BrowserRouter, Route, Routes, Navigate } from "react-router-dom";
import Login from "./pages/Login";
import Home from "./pages/Home.tsx";
import { useIsAuthenticated } from "./shared/contexts/AuthContext";
import AppLayout from "./shared/layout/AppLayout";

export const AppRoutes = () => {
    const isAuthenticated = useIsAuthenticated();

    return (
        <BrowserRouter>
            <Routes>

                {/* Public Route */}
                {!isAuthenticated && (
                    <Route path="*" element={<Login />} />
                )}

                {/* Protected Routes */}
                {isAuthenticated && (
                    <Route element={<AppLayout />}>
                        <Route path="/" element={<Home />} />
                    </Route>
                )}

                {/* Optional redirect fallback */}
                <Route
                    path="*"
                    element={<Navigate to={isAuthenticated ? "/" : "/login"} />}
                />

            </Routes>
        </BrowserRouter>
    );
};
