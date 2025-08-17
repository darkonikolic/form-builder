import { useState, useEffect } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { Button } from '@/components/ui/button';
import UserForms from '@/Components/UserForms';
import FormEditor from '@/Components/FormEditor';
import { LogOut, X, Home, FileText } from 'lucide-react';

export default function Dashboard() {
    const { logout } = useAuth();
    const [activeTab, setActiveTab] = useState(null);
    const [openTabs, setOpenTabs] = useState([]);

    // Load open tabs from localStorage on component mount
    useEffect(() => {
        const savedTabs = localStorage.getItem('openTabs');
        const savedActiveTab = localStorage.getItem('activeTab');

        if (savedTabs) {
            try {
                const parsedTabs = JSON.parse(savedTabs);
                setOpenTabs(parsedTabs);

                // Restore active tab if it exists in open tabs
                if (savedActiveTab) {
                    const parsedActiveTab = JSON.parse(savedActiveTab);
                    if (parsedTabs.find(tab => tab.id === parsedActiveTab.id)) {
                        setActiveTab(parsedActiveTab);
                    }
                }
            } catch (error) {
                console.error('Error loading tabs from localStorage:', error);
            }
        }
    }, []);

    const handleLogout = () => {
        logout();
    };

    const openFormTab = formId => {
        // Check if form is already open
        const existingTab = openTabs.find(tab => tab.id === formId);

        if (existingTab) {
            // If form is already open, just switch to it
            setActiveTab(existingTab);
            localStorage.setItem('activeTab', JSON.stringify(existingTab));
            return;
        }

        // Add form to open tabs if not already open
        const newTabs = [...openTabs, { id: formId, type: 'form' }];
        setOpenTabs(newTabs);
        localStorage.setItem('openTabs', JSON.stringify(newTabs));

        const newActiveTab = { id: formId, type: 'form' };
        setActiveTab(newActiveTab);
        localStorage.setItem('activeTab', JSON.stringify(newActiveTab));
    };

    const closeTab = formId => {
        // Remove specific form tab
        const newTabs = openTabs.filter(tab => tab.id !== formId);
        setOpenTabs(newTabs);
        localStorage.setItem('openTabs', JSON.stringify(newTabs));

        // If closing active tab, switch to dashboard
        if (activeTab && activeTab.id === formId) {
            setActiveTab(null);
            localStorage.removeItem('activeTab');
        }
    };

    const switchToTab = tab => {
        setActiveTab(tab);
        localStorage.setItem('activeTab', JSON.stringify(tab));
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
            {/* Header with Logout button */}
            <header className="bg-white/80 backdrop-blur-sm border-b border-slate-200 shadow-sm">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-start min-h-12 py-2 gap-2 flex-wrap">
                        {/* Dashboard Tab - Always visible */}
                        <button
                            onClick={() => {
                                setActiveTab(null);
                                localStorage.removeItem('activeTab');
                            }}
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
                                    onClick={e => {
                                        e.stopPropagation();
                                        // Open demo dialog here
                                        alert(
                                            "This is a demo project. In the full version, you would be able to see the generated form layout and test data entry here. However, since you haven't paid for the premium version yet, this feature is not available."
                                        );
                                    }}
                                    className="w-5 h-5 rounded-full hover:bg-slate-100 flex items-center justify-center transition-colors mr-1"
                                    title="Open Form (Demo)"
                                >
                                    <svg
                                        className="h-3 w-3 text-slate-500 hover:text-slate-700"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
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
                                    onClick={e => {
                                        e.stopPropagation();
                                        closeTab(tab.id);
                                    }}
                                    className="w-5 h-5 rounded-full hover:bg-slate-100 flex items-center justify-center transition-colors"
                                    title="Close Tab"
                                >
                                    <X className="h-4 w-4 text-slate-500 hover:text-slate-700" />
                                </button>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {activeTab ? (
                    <FormEditor
                        formId={activeTab.id}
                        onClose={() => closeTab(activeTab.id)}
                    />
                ) : (
                    <UserForms onOpenForm={openFormTab} />
                )}
            </main>
        </div>
    );
}
