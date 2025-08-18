import { memo } from 'react';
import { Button } from '@/components/ui/button';
import { LogOut } from 'lucide-react';

function DashboardHeader({ onLogout }) {
    return (
        <header className="bg-white/80 backdrop-blur-sm border-b border-slate-200 shadow-sm">
            <div className="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div className="flex justify-between items-center h-16">
                    <div className="flex items-center">
                        <h1 className="text-xl font-semibold text-slate-900">
                            Form Builder Dashboard
                        </h1>
                    </div>
                    <div className="flex items-center gap-3">
                        <Button
                            onClick={onLogout}
                            variant="outline"
                            className="border-red-300 text-red-700 hover:bg-red-50 hover:border-red-400"
                        >
                            <LogOut className="h-4 w-4 mr-2" />
                            Logout
                        </Button>
                    </div>
                </div>
            </div>
        </header>
    );
}

export default memo(DashboardHeader);
