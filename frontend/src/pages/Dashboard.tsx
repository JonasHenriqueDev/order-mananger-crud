import { useEffect, useState } from "react";
import api from "../services/api";

type User = {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    role: string;
    active: boolean;
};

export default function Dashboard() {
    const [user, setUser] = useState<User | null>(null);

    useEffect(() => {
        async function loadUser() {
            const { data } = await api.get<User>("/me");
            setUser(data);
        }

        loadUser();
    }, []);

    if (!user) {
        return (
            <div className="h-screen flex items-center justify-center bg-[#343541] text-white">
                Carregando...
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-[#343541] flex items-center justify-center">
            <div className="bg-[#444654] p-8 rounded-2xl shadow-xl w-full max-w-md text-white space-y-3">
                <h1 className="text-2xl font-semibold mb-4">Perfil</h1>

                <p><strong>ID:</strong> {user.id}</p>
                <p><strong>Nome:</strong> {user.first_name} {user.last_name}</p>
                <p><strong>Email:</strong> {user.email}</p>
                <p><strong>Role:</strong> {user.role}</p>
                <p>
                    <strong>Status:</strong>{" "}
                    {user.active ? "Ativo" : "Inativo"}
                </p>
            </div>
        </div>
    );
}
