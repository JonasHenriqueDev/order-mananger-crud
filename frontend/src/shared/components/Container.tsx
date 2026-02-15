import type { ReactNode } from "react";

interface ContainerProps {
    children: ReactNode;
    className?: string;
}

export default function Container({ children, className = "" }: ContainerProps) {
    return (
        <div className={`bg-[#2a2a2a] shadow-lg rounded-lg p-6 border border-[#3d3d3d] ${className}`}>
            {children}
        </div>
    );
}