import type {ReactNode, ButtonHTMLAttributes} from "react";

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
    children: ReactNode;
}

export default function Button({ children, ...props }: ButtonProps) {
    return (
        <button
            {...props}
            className="w-full bg-blue-600 hover:bg-blue-700 transition rounded-lg py-3 font-medium"
        >
            {children}
        </button>
    );
}
