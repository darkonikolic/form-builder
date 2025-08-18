import { useState, useEffect, useCallback } from 'react';

export const useTabManagement = () => {
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

    const openFormTab = useCallback(
        formId => {
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
        },
        [openTabs]
    );

    const closeTab = useCallback(
        formId => {
            // Remove specific form tab
            const newTabs = openTabs.filter(tab => tab.id !== formId);
            setOpenTabs(newTabs);
            localStorage.setItem('openTabs', JSON.stringify(newTabs));

            // If closing active tab, switch to dashboard
            if (activeTab && activeTab.id === formId) {
                setActiveTab(null);
                localStorage.removeItem('activeTab');
            }
        },
        [openTabs, activeTab]
    );

    const switchToTab = useCallback(tab => {
        setActiveTab(tab);
        localStorage.setItem('activeTab', JSON.stringify(tab));
    }, []);

    const closeActiveTab = useCallback(() => {
        if (activeTab) {
            closeTab(activeTab.id);
        }
    }, [activeTab, closeTab]);

    const resetToDashboard = useCallback(() => {
        setActiveTab(null);
        localStorage.removeItem('activeTab');
    }, []);

    return {
        activeTab,
        openTabs,
        openFormTab,
        closeTab,
        switchToTab,
        closeActiveTab,
        resetToDashboard,
    };
};
