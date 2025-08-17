import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useAuth } from '@/contexts/AuthContext';

export default function LogoutDialog({ isOpen, onOpenChange }) {
    const { logout } = useAuth();

    const handleLogout = () => {
        logout();
        onOpenChange(false);
    };

    return (
        <Dialog open={isOpen} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-md bg-white/95 backdrop-blur-sm border-0 shadow-2xl">
                <DialogHeader>
                    <DialogTitle className="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                        Logout Confirmation 🔒
                    </DialogTitle>
                    <DialogDescription className="text-slate-600 mt-2">
                        Are you sure you want to logout? You'll need to sign in
                        again to access your forms.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter className="flex justify-end space-x-2">
                    <Button
                        variant="outline"
                        onClick={() => onOpenChange(false)}
                        className="border-slate-300 text-slate-700 hover:bg-slate-50"
                    >
                        Cancel
                    </Button>
                    <Button
                        onClick={handleLogout}
                        className="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white"
                    >
                        Yes, Logout
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
