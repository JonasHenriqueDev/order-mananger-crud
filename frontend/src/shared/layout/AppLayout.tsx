import { Outlet, useNavigate } from "react-router-dom";
import {useAuthContext} from "../contexts/AuthContext.tsx";

export default function AppLayout() {
    const navigate = useNavigate();
    const { logout } = useAuthContext();

    const handleLogout = () => {
        logout();
        navigate("/login");
    };

    return (
        <div className="min-h-screen bg-[#181818] text-white">

            <header className="bg-[#212121] p-4 shadow flex justify-between items-center">
                <h1 className="font-semibold text-lg">
                    Order Manager
                </h1>

                <button
                    onClick={handleLogout}
                    className="bg-red-600 hover:bg-red-900 px-4 py-2 rounded-lg text-sm transition"
                >
                    Logout
                </button>
            </header>

            <main className="p-6">
                <Outlet />
            </main>

        </div>
    );
}
