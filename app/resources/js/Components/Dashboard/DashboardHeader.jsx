import { memo } from 'react';
import { Button } from '@/components/ui/button';
import { LogOut } from 'lucide-react';

function DashboardHeader({ onLogout, onRefreshTabs, isValidatingTabs }) {
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
                            onClick={onRefreshTabs}
                            variant="outline"
                            className="border-slate-300 text-slate-700 hover:bg-slate-50 hover:border-slate-400"
                            disabled={isValidatingTabs}
                        >
                            {isValidatingTabs ? (
                                <div className="h-4 w-4 animate-spin rounded-full border-2 border-slate-300 border-t-slate-600 mr-2" />
                            ) : (
                                <svg
                                    className="h-4 w-4 mr-2"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                    />
                                </svg>
                            )}
                            {isValidatingTabs
                                ? 'Validating...'
                                : 'Refresh Tabs'}
                        </Button>
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
