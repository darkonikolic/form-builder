import { useState, useEffect } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import Welcome from '@/Pages/Welcome';
import LoginDialog from '@/Components/LoginDialog';
import LogoutDialog from '@/Components/LogoutDialog';
import Dashboard from '@/Components/Dashboard';
import { Button } from '@/components/ui/button';

// Main App Component
export default function FormBuilderApp() {
    const { user, loading } = useAuth();
    const [showLoginDialog, setShowLoginDialog] = useState(false);

    if (loading) {
        return (
            <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center">
                <div className="text-center">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-slate-900 mx-auto mb-4"></div>
                    <p className="text-slate-600">Loading...</p>
                </div>
            </div>
        );
    }

    // If user is logged in, show Dashboard
    if (user) {
        return <Dashboard />;
    }

    // If user is not logged in, show Welcome page
    return (
        <div>
            <Welcome
                onLoginClick={() => setShowLoginDialog(true)}
                onRegisterClick={() => setShowLoginDialog(true)}
            />
            {/* Login Dialog */}
            <LoginDialog
                isOpen={showLoginDialog}
                onOpenChange={setShowLoginDialog}
                onSuccess={() => {
                    // User successfully logged in, dialog will close automatically
                    // and Dashboard will be shown because user state is updated
                }}
            />
        </div>
    );
}
