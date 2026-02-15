import type { ChangeEvent } from "react";

interface SelectOption {
    value: string;
    label: string;
}

interface SelectProps {
    label: string;
    value: string;
    onChange: (e: ChangeEvent<HTMLSelectElement>) => void;
    options: SelectOption[];
    required?: boolean;
}

export default function Select({ label, value, onChange, options, required }: SelectProps) {
    return (
        <div className="mb-4">
            <label className="block text-sm mb-2 text-gray-300">{label}</label>
            <select
                value={value}
                onChange={onChange}
                required={required}
                className="w-full p-3 rounded-lg bg-[#2a2a2a] border border-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                {options.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
        </div>
    );
}