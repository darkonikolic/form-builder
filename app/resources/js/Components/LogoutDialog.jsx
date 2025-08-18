import { useAuth } from '@/contexts/AuthContext';
import BaseDialog from '@/components/ui/BaseDialog';
import ActionButton from '@/components/ui/ActionButton';

export default function LogoutDialog({ isOpen, onOpenChange }) {
    const { logout } = useAuth();

    const handleLogout = () => {
        logout();
        onOpenChange(false);
    };

    return (
        <BaseDialog
            isOpen={isOpen}
            onOpenChange={onOpenChange}
            title="Logout Confirmation 🔒"
            description="Are you sure you want to logout? You'll need to sign in again to access your forms."
        >
            <div className="flex justify-end space-x-2">
                <ActionButton
                    variant="outline"
                    onClick={() => onOpenChange(false)}
                >
                    Cancel
                </ActionButton>
                <ActionButton variant="danger" onClick={handleLogout}>
                    Yes, Logout
                </ActionButton>
            </div>
        </BaseDialog>
    );
}
