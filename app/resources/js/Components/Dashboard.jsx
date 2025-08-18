import { useState, useCallback, memo } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import DemoDialog from '@/components/ui/DemoDialog';
import { useTabManagement } from '@/hooks/useTabManagement';
import DashboardHeader from '@/Components/Dashboard/DashboardHeader';
import DashboardTabs from '@/Components/Dashboard/DashboardTabs';
import DashboardContent from '@/Components/Dashboard/DashboardContent';

function Dashboard() {
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
            <DashboardHeader onLogout={handleLogout} />

            <DashboardTabs
                activeTab={activeTab}
                openTabs={openTabs}
                onSwitchToTab={switchToTab}
                onOpenDemoDialog={handleOpenDemoDialog}
                onCloseTab={handleCloseTab}
                onResetToDashboard={resetToDashboard}
            />

            <DashboardContent
                activeTab={activeTab}
                onCloseTab={closeTab}
                onOpenForm={openFormTab}
            />

            {/* Demo Dialog */}
            <DemoDialog
                isOpen={isDemoDialogOpen}
                onOpenChange={setIsDemoDialogOpen}
            />
        </div>
    );
}

export default memo(Dashboard);
