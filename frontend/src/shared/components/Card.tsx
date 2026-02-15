import type {ReactNode} from "react";

interface CardProps {
    children: ReactNode;
}

export default function Card({ children }: CardProps) {
    return (
        <div className="bg-[#212121] w-full max-w-md p-8 rounded-2xl shadow-lg text-white">
            {children}
        </div>
    );
}
