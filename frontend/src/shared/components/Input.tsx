import type {ChangeEvent} from "react";

interface InputProps {
    label: string;
    type?: string;
    value: string;
    onChange: (e: ChangeEvent<HTMLInputElement>) => void;
    placeholder?: string;
}

export default function Input({label, type = "text", value, onChange, placeholder,}: InputProps) {
    return (<div className="mb-4">
        <label className="block text-sm mb-2">{label}</label>
        <input
            type={type}
            value={value}
            onChange={onChange}
            placeholder={placeholder}
            className="w-full p-3 rounded-lg bg-[#2a2a2a] border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
    </div>);
}