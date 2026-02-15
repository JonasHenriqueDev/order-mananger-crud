import {useState} from "react";
import Card from "../shared/components/Card";
import Input from "../shared/components/Input";
import Button from "../shared/components/Button";


export default function Login() {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");

    return (
        <div className="flex items-center justify-center h-screen bg-[#181818]">
            <Card>
                <h1 className="text-2xl font-semibold text-center mb-6">
                    Login
                </h1>

                <Input
                    label="E-mail"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="your@email.com"
                />

                <Input
                    label="Password"
                    type="password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="••••••••"
                />

                <Button>Sign In</Button>
            </Card>
        </div>
    );
}
