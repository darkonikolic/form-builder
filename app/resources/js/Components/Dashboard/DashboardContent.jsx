import { memo } from 'react';
import ErrorBoundary from '@/components/ui/ErrorBoundary';
import FormEditor from '@/Components/FormEditor';
import UserForms from '@/Components/UserForms';

function DashboardContent({ activeTab, onCloseTab, onOpenForm }) {
    return (
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
                    <FormEditor
                        formId={activeTab.id}
                        onClose={() => onCloseTab(activeTab.id)}
                    />
                </ErrorBoundary>
            ) : (
                <UserForms onOpenForm={onOpenForm} />
            )}
        </main>
    );
}

export default memo(DashboardContent);
