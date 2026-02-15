import type { ReactNode } from "react";

interface TableProps {
    children: ReactNode;
}

export function Table({ children }: TableProps) {
    return (
        <div className="overflow-x-auto rounded-lg">
            <table className="min-w-full divide-y divide-[#3d3d3d]">
                {children}
            </table>
        </div>
    );
}

interface TableHeaderProps {
    children: ReactNode;
}

export function TableHeader({ children }: TableHeaderProps) {
    return (
        <thead className="bg-[#1a1a1a]">
        <tr>{children}</tr>
        </thead>
    );
}

interface TableHeadProps {
    children: ReactNode;
}

export function TableHead({ children }: TableHeadProps) {
    return (
        <th className="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
            {children}
        </th>
    );
}

interface TableBodyProps {
    children: ReactNode;
}

export function TableBody({ children }: TableBodyProps) {
    return (
        <tbody className="bg-[#2a2a2a] divide-y divide-[#3d3d3d]">
        {children}
        </tbody>
    );
}

interface TableRowProps {
    children: ReactNode;
}

export function TableRow({ children }: TableRowProps) {
    return (
        <tr className="hover:bg-[#383838] transition-colors">
            {children}
        </tr>
    );
}

interface TableCellProps {
    children: ReactNode;
    className?: string;
}

export function TableCell({ children, className = "" }: TableCellProps) {
    return (
        <td className={`px-6 py-4 whitespace-nowrap text-sm text-gray-300 ${className}`}>
            {children}
        </td>
    );
}