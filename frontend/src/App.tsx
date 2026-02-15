import { AuthProvider } from './shared/contexts/AuthContext';
import { AppRoutes } from './Routes';


export function App() {

    return (
        <AuthProvider>
            <AppRoutes />
        </AuthProvider>
    )
}