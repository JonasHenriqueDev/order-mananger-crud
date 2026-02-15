import { useState } from "react";
import * as React from "react";
import { useNavigate } from "react-router-dom";
import Card from "../shared/components/Card";
import Input from "../shared/components/Input";
import Button from "../shared/components/Button";
import { useAuthContext } from "../shared/contexts/AuthContext";

export default function Register() {
    const { register } = useAuthContext();
    const navigate = useNavigate();

    const [firstName, setFirstName] = useState("");
    const [lastName, setLastName] = useState("");
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [passwordConfirmation, setPasswordConfirmation] = useState("");

    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.SyntheticEvent) => {
        e.preventDefault();

        try {
            setLoading(true);
            setError(null);

            await register({
                first_name: firstName,
                last_name: lastName,
                email,
                password,
                password_confirmation: passwordConfirmation,
            });

            navigate("/dashboard");
        } catch (err) {
            setError("Registration failed");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="flex items-center justify-center h-screen bg-[#181818]">
            <Card>
                <form onSubmit={handleSubmit}>
                    <h1 className="text-2xl font-semibold text-center mb-6">
                        Create Account
                    </h1>

                    <Input
                        label="First Name"
                        value={firstName}
                        onChange={(e) => setFirstName(e.target.value)}
                        required
                    />

                    <Input
                        label="Last Name"
                        value={lastName}
                        onChange={(e) => setLastName(e.target.value)}
                        required
                    />

                    <Input
                        label="E-mail"
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                    />

                    <Input
                        label="Password"
                        type="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                    />

                    <Input
                        label="Confirm Password"
                        type="password"
                        value={passwordConfirmation}
                        onChange={(e) => setPasswordConfirmation(e.target.value)}
                        required
                    />

                    {error && (
                        <p className="text-red-500 text-sm mb-4">{error}</p>
                    )}

                    <Button type="submit" disabled={loading}>
                        {loading ? "Creating..." : "Register"}
                    </Button>
                </form>
            </Card>
        </div>
    );
}
