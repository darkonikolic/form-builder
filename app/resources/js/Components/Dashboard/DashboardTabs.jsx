import { memo } from 'react';
import { Home, FileText, X } from 'lucide-react';
import { BUTTON_SIZES, ICON_SIZES } from '@/constants/designTokens';

function DashboardTabs({
    activeTab,
    openTabs,
    onSwitchToTab,
    onOpenDemoDialog,
    onCloseTab,
    onResetToDashboard,
    isValidatingTabs,
}) {
    return (
        <div className="bg-white/60 backdrop-blur-sm border-b border-slate-200">
            <div className="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div className="flex items-start min-h-12 py-2 gap-2 flex-wrap">
                    {isValidatingTabs && (
                        <div className="flex items-center px-4 py-2 text-sm text-slate-600">
                            <div className="h-4 w-4 animate-spin rounded-full border-2 border-slate-300 border-t-slate-600 mr-2" />
                            Validating tabs...
                        </div>
                    )}

                    {/* Dashboard Tab - Always visible */}
                    <button
                        onClick={onResetToDashboard}
                        className={`flex items-center rounded-t-lg px-4 py-2 border border-slate-200 border-b-0 transition-colors cursor-pointer mb-2 ${
                            !activeTab
                                ? 'bg-blue-50 border-blue-300 text-blue-900 border-b-2 border-b-blue-600 shadow-sm'
                                : 'bg-slate-50 text-slate-600 hover:bg-white hover:text-slate-900 hover:border-slate-300'
                        }`}
                    >
                        <Home className="h-4 w-4 mr-2" />
                        <span className="text-sm font-medium">Dashboard</span>
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
                            onClick={() => onSwitchToTab(tab)}
                        >
                            <FileText className="h-4 w-4 mr-2" />
                            <span className="text-sm font-medium text-slate-900 mr-3">
                                Form {tab.id}
                            </span>
                            <button
                                onClick={onOpenDemoDialog}
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
                                onClick={e => onCloseTab(e, tab.id)}
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
    );
}

export default memo(DashboardTabs);
