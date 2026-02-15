interface LoadingProps {
    message?: string;
}

export default function Loading({ message = "Carregando..." }: LoadingProps) {
    return (
        <div className="flex items-center justify-center min-h-screen bg-[#212121]">
            <div className="text-lg text-gray-200">{message}</div>
        </div>
    );
}