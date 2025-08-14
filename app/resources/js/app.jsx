import '../css/app.css';
import './bootstrap';
import { createRoot } from 'react-dom/client';
import React from 'react';
import Welcome from './Pages/Welcome';

// Main App Component
function FormBuilderApp() {
    return (
        <div className="min-h-screen bg-gray-100">
            <Welcome />
        </div>
    );
}

// Initialize React app
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
document.title = `Form Builder - ${appName}`;

const container = document.getElementById('app');
const root = createRoot(container);
root.render(<FormBuilderApp />);
