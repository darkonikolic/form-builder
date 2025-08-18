import { useState, useCallback, memo } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { Button } from '@/components/ui/button';
import UserForms from '@/Components/UserForms';
import FormEditor from '@/Components/FormEditor';
import { LogOut, X, Home, FileText } from 'lucide-react';
import DemoDialog from '@/components/ui/DemoDialog';
import ErrorBoundary from '@/components/ui/ErrorBoundary';
import { useTabManagement } from '@/hooks/useTabManagement';
import { BUTTON_SIZES, ICON_SIZES } from '@/constants/designTokens';

// Memoize components to prevent unnecessary re-renders
const MemoizedUserForms = memo(UserForms);
const MemoizedFormEditor = memo(FormEditor);

export default function Dashboard() {
    const { logout } = useAuth();
    const [isDemoDialogOpen, setIsDemoDialogOpen] = useState(false);

    const {
        activeTab,
        openTabs,
        openFormTab,
        closeTab,
        switchToTab,
        resetToDashboard,
    } = useTabManagement();

    const handleLogout = useCallback(() => {
        logout();
    }, [logout]);

    const handleOpenDemoDialog = useCallback(e => {
        e.stopPropagation();
        setIsDemoDialogOpen(true);
    }, []);

    const handleCloseTab = useCallback(
        (e, formId) => {
            e.stopPropagation();
            closeTab(formId);
        },
        [closeTab]
    );

    return (
        <div className="min-h-screen bg-slate-50">
            {/* Header with Logout button */}
            <header className="bg-white/80 backdrop-blur-sm border-b border-slate-200 shadow-sm">
                <div className="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center h-16">
                        <div className="flex items-center">
                            <h1 className="text-xl font-semibold text-slate-900">
                                Form Builder Dashboard
                            </h1>
                        </div>
                        <Button
                            onClick={handleLogout}
                            variant="outline"
                            className="border-red-300 text-red-700 hover:bg-red-50 hover:border-red-400"
                        >
                            <LogOut className="h-4 w-4 mr-2" />
                            Logout
                        </Button>
                    </div>
                </div>
            </header>

            {/* Navigation Tabs */}
            <div className="bg-white/60 backdrop-blur-sm border-b border-slate-200">
                <div className="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="flex items-start min-h-12 py-2 gap-2 flex-wrap">
                        {/* Dashboard Tab - Always visible */}
                        <button
                            onClick={resetToDashboard}
                            className={`flex items-center rounded-t-lg px-4 py-2 border border-slate-200 border-b-0 transition-colors cursor-pointer mb-2 ${
                                !activeTab
                                    ? 'bg-blue-50 border-blue-300 text-blue-900 border-b-2 border-b-blue-600 shadow-sm'
                                    : 'bg-slate-50 text-slate-600 hover:bg-white hover:text-slate-900 hover:border-slate-300'
                            }`}
                        >
                            <Home className="h-4 w-4 mr-2" />
                            <span className="text-sm font-medium">
                                Dashboard
                            </span>
                        </button>

                        {/* Form Tabs - All open forms */}
                        {openTabs.map(tab => (
                            <div
                                key={tab.id}
                                className={`flex items-center rounded-t-lg px-4 py-2 border border-slate-200 border-b-0 transition-colors cursor-pointer mb-2 ${
                                    activeTab && activeTab.id === tab.id
                                        ? 'bg-blue-50 border-blue-300 text-blue-900 border-b-2 border-b-blue-600 shadow-sm'
                                        : 'bg-slate-50 text-slate-600 hover:bg-white hover:text-slate-900 hover:border-slate-300'
                                }`}
                                onClick={() => switchToTab(tab)}
                            >
                                <FileText className="h-4 w-4 mr-2" />
                                <span className="text-sm font-medium text-slate-900 mr-3">
                                    Form {tab.id}
                                </span>
                                <button
                                    onClick={handleOpenDemoDialog}
                                    className={`${BUTTON_SIZES.icon.md} rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors mr-1`}
                                    title="Open Form (Demo)"
                                    aria-label="Open form in demo mode"
                                    role="button"
                                    tabIndex={0}
                                >
                                    <svg
                                        className={`${ICON_SIZES.sm} text-slate-600 hover:text-slate-800`}
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                        />
                                    </svg>
                                </button>
                                <button
                                    onClick={e => handleCloseTab(e, tab.id)}
                                    className={`${BUTTON_SIZES.icon.md} rounded-full hover:bg-slate-100 flex items-center justify-center transition-colors`}
                                    title="Close Tab"
                                >
                                    <X
                                        className={`${ICON_SIZES.sm} text-slate-500 hover:text-slate-700`}
                                    />
                                </button>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <main className="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
                {activeTab ? (
                    <ErrorBoundary
                        fallback={
                            <div className="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                                <p className="text-red-600">
                                    Error loading form editor. Please try again.
                                </p>
                            </div>
                        }
                        onRetry={() => window.location.reload()}
                    >
                        <MemoizedFormEditor
                            formId={activeTab.id}
                            onClose={() => closeTab(activeTab.id)}
                        />
                    </ErrorBoundary>
                ) : (
                    <MemoizedUserForms onOpenForm={openFormTab} />
                )}
            </main>

            {/* Demo Dialog */}
            <DemoDialog
                isOpen={isDemoDialogOpen}
                onOpenChange={setIsDemoDialogOpen}
            />
        </div>
    );
}
