import type {ChangeEvent} from "react";

interface TextareaProps {
    label: string;
    value: string;
    onChange: (e: ChangeEvent<HTMLTextAreaElement>) => void;
    placeholder?: string;
    required?: boolean;
    rows?: number;
}

export default function Textarea({label, value, onChange, placeholder, required, rows = 4,}: TextareaProps) {
    return (<div className="mb-4">
            <label className="block text-sm mb-2 text-gray-300">{label}</label>
            <textarea
                value={value}
                onChange={onChange}
                placeholder={placeholder}
                required={required}
                rows={rows}
                className="w-full p-3 rounded-lg bg-[#2a2a2a] border border-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
            />
        </div>);
}