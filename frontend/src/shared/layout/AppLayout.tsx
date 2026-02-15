import { Outlet, useNavigate, useLocation } from "react-router-dom";
import {useAuthContext} from "../contexts/AuthContext.tsx";

export default function AppLayout() {
    const { logout } = useAuthContext();
    const navigate = useNavigate();
    const location = useLocation();

    const handleLogout = () => {
        logout();
    };

    const isActive = (path: string): boolean => {
        return location.pathname === path;
    };

    const navLinkClasses = (path: string): string => {
        const base = "px-4 py-2 rounded-lg text-sm transition";
        return isActive(path)
            ? `${base} bg-blue-600 text-white`
            : `${base} text-gray-300 hover:bg-[#2a2a2a]`;
    };

    return (
        <div className="min-h-screen bg-[#181818] text-white">

            <header className="bg-[#212121] p-4 shadow flex justify-between items-center">
                <h1 className="font-semibold text-lg">
                    Order Manager
                </h1>

                <nav className="flex items-center gap-2">
                    <button
                        onClick={() => navigate("/")}
                        className={navLinkClasses("/")}
                    >
                        Home
                    </button>
                    <button
                        onClick={() => navigate("/my-orders")}
                        className={navLinkClasses("/my-orders")}
                    >
                        My Orders
                    </button>
                    <button
                        onClick={handleLogout}
                        className="bg-red-600 hover:bg-red-900 px-4 py-2 rounded-lg text-sm transition"
                    >
                        Logout
                    </button>
                </nav>
            </header>

            <main className="p-6">
                <Outlet />
            </main>

        </div>
    );
}
