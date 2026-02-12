import {type SyntheticEvent, useState } from "react";
import { useNavigate } from "react-router-dom";
import api from "../services/api";
import type {LoginPayload, LoginResponse} from "../types/auth";

export default function Login() {
    const [form, setForm] = useState<LoginPayload>({
        email: "",
        password: "",
    });

    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const navigate = useNavigate();

    async function handleSubmit(e: SyntheticEvent<HTMLFormElement>) {
        e.preventDefault();
        setError(null);
        setLoading(true);

        try {
            const { data } = await api.post<LoginResponse>("/login", form);

            localStorage.setItem("token", data.token);
            localStorage.setItem("user", JSON.stringify(data.user));

            navigate("/dashboard");
        } catch (err) {
            setError("E-mail ou senha inválidos");
        } finally {
            setLoading(false);
        }
    }

    return (
        <div className="h-screen flex items-center justify-center bg-[#343541]">
            <form
                onSubmit={handleSubmit}
                className="w-full max-w-md bg-[#444654] p-8 rounded-2xl shadow-xl space-y-4"
            >
                <h1 className="text-2xl font-semibold text-center text-white">
                    Login
                </h1>

                {error && (
                    <div className="bg-red-500/20 text-red-400 text-sm p-2 rounded">
                        {error}
                    </div>
                )}

                <div>
                    <label className="block text-sm mb-1 text-gray-300">E-mail</label>
                    <input
                        type="email"
                        className="w-full bg-[#40414F] border border-[#565869] text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#19C37D]"
                        value={form.email}
                        onChange={(e) => setForm({ ...form, email: e.target.value })}
                        required
                    />
                </div>

                <div>
                    <label className="block text-sm mb-1 text-gray-300">Senha</label>
                    <input
                        type="password"
                        className="w-full bg-[#40414F] border border-[#565869] text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#19C37D]"
                        value={form.password}
                        onChange={(e) => setForm({ ...form, password: e.target.value })}
                        required
                    />
                </div>

                <button
                    type="submit"
                    disabled={loading}
                    className="w-full bg-[#19C37D] text-black font-medium py-2 rounded-lg hover:brightness-110 transition disabled:opacity-50"
                >
                    {loading ? "Entrando..." : "Entrar"}
                </button>
            </form>
        </div>

    );

}
