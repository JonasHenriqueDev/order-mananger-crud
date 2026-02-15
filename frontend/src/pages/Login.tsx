import { useState } from "react";
import Card from "../shared/components/Card";
import Input from "../shared/components/Input";
import Button from "../shared/components/Button";
import * as React from "react";
import {useAuthContext} from "../shared/contexts/AuthContext.tsx";

export default function Login() {
    const { login } = useAuthContext();

    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.SyntheticEvent) => {
        e.preventDefault();

        try {
            setLoading(true);
            setError(null);

            await login(email, password);

            // optional: navigate to dashboard
        } catch (err) {
            setError("Invalid credentials");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="flex items-center justify-center h-screen bg-[#181818]">
            <Card>
                <form onSubmit={handleSubmit}>
                    <h1 className="text-2xl font-semibold text-center mb-6">
                        Login
                    </h1>

                    <Input
                        label="E-mail"
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="your@email.com"
                        required
                    />

                    <Input
                        label="Password"
                        type="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        placeholder="••••••••"
                        required
                    />

                    {error && (
                        <p className="text-red-500 text-sm mb-4">{error}</p>
                    )}

                    <Button type="submit" disabled={loading}>
                        {loading ? "Signing in..." : "Sign In"}
                    </Button>
                </form>
            </Card>
        </div>
    );
}
