import '../css/app.css';
import './bootstrap';
import { createRoot } from 'react-dom/client';
import FormBuilderApp from './Components/FormBuilderApp';
import { AuthProvider } from './contexts/AuthContext';

// Initialize React app
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
document.title = `Form Builder - ${appName}`;

const container = document.getElementById('app');
const root = createRoot(container);
root.render(
    <AuthProvider>
        <FormBuilderApp />
    </AuthProvider>
);
