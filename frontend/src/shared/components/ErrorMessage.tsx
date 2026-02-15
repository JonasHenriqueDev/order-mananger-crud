interface ErrorMessageProps {
    message: string;
}

export default function ErrorMessage({ message }: ErrorMessageProps) {
    return (
        <div className="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded">
            {message}
        </div>
    );
}