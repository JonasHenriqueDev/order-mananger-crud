import {BrowserRouter, Navigate, Route, Routes} from "react-router-dom";
import Login from "./pages/Login";
import Register from "./pages/Register";
import Home from "./pages/Home";
import {useIsAuthenticated} from "./shared/contexts/AuthContext";
import AppLayout from "./shared/layout/AppLayout";
import CreateProduct from "./pages/CreateProduct.tsx";
import CreateOrder from "./shared/components/CreateOrder.tsx";
import ViewProduct from "./pages/ViewProduct.tsx";
import ViewOrder from "./pages/ViewOrder.tsx";
import MyOrders from "./pages/MyOrders.tsx";

export const AppRoutes = () => {
    const isAuthenticated = useIsAuthenticated();

    return (
        <BrowserRouter>
            <Routes>

                {/* Public Routes */}
                <Route
                    path="/login"
                    element={
                        isAuthenticated ? <Navigate to="/"/> : <Login/>
                    }
                />

                <Route
                    path="/register"
                    element={
                        isAuthenticated ? <Navigate to="/"/> : <Register/>
                    }
                />

                {/* Protected Routes */}
                <Route
                    element={
                        isAuthenticated ? <AppLayout/> : <Navigate to="/login"/>
                    }
                >
                    <Route path="/" element={<Home />} />
                    <Route path="/my-orders" element={<MyOrders />} />
                    <Route path="/products/create" element={<CreateProduct />} />
                    <Route path="/products/:id" element={<ViewProduct />} />
                    <Route path="/orders/create" element={<CreateOrder />} />
                    <Route path="/orders/:id" element={<ViewOrder />} />
                </Route>

                {/* Fallback */}
                <Route
                    path="*"
                    element={
                        <Navigate to={isAuthenticated ? "/" : "/login"}/>
                    }
                />

            </Routes>
        </BrowserRouter>
    );
};
