import type { ChangeEvent } from "react";

interface CheckboxProps {
    label: string;
    checked: boolean;
    onChange: (e: ChangeEvent<HTMLInputElement>) => void;
}

export default function Checkbox({ label, checked, onChange }: CheckboxProps) {
    return (
        <div className="mb-4 flex items-center">
            <input
                type="checkbox"
                checked={checked}
                onChange={onChange}
                className="w-4 h-4 bg-[#2a2a2a] border-gray-700 rounded focus:ring-2 focus:ring-blue-500"
            />
            <label className="ml-2 text-sm text-gray-300">{label}</label>
        </div>
    );
}