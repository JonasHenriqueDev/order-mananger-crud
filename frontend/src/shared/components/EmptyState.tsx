interface EmptyStateProps {
    message: string;
}

export default function EmptyState({ message }: EmptyStateProps) {
    return (
        <div className="text-center py-8 text-gray-500">
            {message}
        </div>
    );
}