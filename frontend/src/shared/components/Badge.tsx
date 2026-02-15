import type { ReactNode } from "react";

type BadgeVariant = "success" | "warning" | "danger" | "info" | "default";

interface BadgeProps {
    children: ReactNode;
    variant?: BadgeVariant;
    className?: string;
}

const variantStyles: Record<BadgeVariant, string> = {
    success: "bg-green-900/50 text-green-300 border-green-700",
    warning: "bg-yellow-900/50 text-yellow-300 border-yellow-700",
    danger: "bg-red-900/50 text-red-300 border-red-700",
    info: "bg-blue-900/50 text-blue-300 border-blue-700",
    default: "bg-[#383838] text-gray-400 border-[#4d4d4d]",
};

export default function Badge({ children, variant = "default", className = "" }: BadgeProps) {
    return (
        <span className={`px-2 py-1 rounded border ${variantStyles[variant]} ${className}`}>
            {children}
        </span>
    );
}