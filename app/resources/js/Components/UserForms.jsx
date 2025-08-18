import { useState, useEffect } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Trash2, ExternalLink, Plus, Loader2, Edit } from 'lucide-react';
import CreateFormDialog from '@/Components/CreateFormDialog';
import FormPreviewDialog from '@/components/ui/FormPreviewDialog';

export default function UserForms({ onOpenForm }) {
    const { user, loading: authLoading } = useAuth();
    const [forms, setForms] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [showCreateDialog, setShowCreateDialog] = useState(false);
    const [deleteDialog, setDeleteDialog] = useState({
        isOpen: false,
        formId: null,
        formName: '',
    });

    const [isFormPreviewOpen, setIsFormPreviewOpen] = useState(false);

    useEffect(() => {
        // Only fetch forms if user is authenticated
        if (user && !authLoading) {
            fetchUserForms();
        }
    }, [user, authLoading]);

    const fetchUserForms = async () => {
        try {
            setLoading(true);
            const response = await fetch('/api/forms', {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
                    Accept: 'application/json',
                },
            });

            if (response.ok) {
                const data = await response.json();
                setForms(data.data || []);
            } else {
                setError('Failed to fetch forms');
            }
        } catch (error) {
            setError('Network error occurred');
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteForm = (formId, formName) => {
        setDeleteDialog({ isOpen: true, formId, formName });
    };

    const confirmDeleteForm = async () => {
        const { formId } = deleteDialog;

        try {
            const response = await fetch(`/api/forms/${formId}`, {
                method: 'DELETE',
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
                    Accept: 'application/json',
                },
            });

            if (response.ok) {
                // Remove the deleted form from the list
                setForms(prevForms =>
                    prevForms.filter(form => form.id !== formId)
                );
                setDeleteDialog({ isOpen: false, formId: null, formName: '' });
            } else {
                alert('Failed to delete form');
            }
        } catch (error) {
            alert('Network error occurred');
        }
    };

    const handleOpenForm = formId => {
        if (onOpenForm) {
            onOpenForm(formId);
        }
    };

    // Don't render anything if still loading auth
    if (authLoading) {
        return null;
    }

    // Don't render if user is not authenticated
    if (!user) {
        return null;
    }

    if (loading) {
        return (
            <div className="flex items-center justify-center p-8">
                <div className="text-center">
                    <Loader2 className="h-8 w-8 animate-spin text-slate-600 mx-auto mb-2" />
                    <p className="text-slate-600">Loading your forms...</p>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="text-center p-8">
                <p className="text-red-600 mb-4">{error}</p>
                <Button onClick={fetchUserForms} variant="outline">
                    Try Again
                </Button>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <div>
                    <h2 className="text-2xl font-bold text-slate-900">
                        My Forms
                    </h2>
                    <p className="text-slate-600">
                        Manage and edit your created forms
                    </p>
                </div>
                <div className="flex items-center gap-3">
                    <button
                        onClick={fetchUserForms}
                        className="flex items-center rounded-lg px-3 py-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                        title="Reload forms"
                    >
                        <svg
                            className="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                        </svg>
                    </button>
                    <Button
                        onClick={() => setShowCreateDialog(true)}
                        className="bg-gradient-to-r from-slate-900 to-slate-700 hover:from-slate-800 hover:to-slate-600"
                    >
                        <Plus className="h-4 w-4 mr-2" />
                        Create New Form
                    </Button>
                </div>
            </div>

            {forms.length === 0 ? (
                <Card className="text-center p-8">
                    <CardContent>
                        <div className="text-slate-400 mb-4">
                            <svg
                                className="mx-auto h-12 w-12"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                        </div>
                        <h3 className="text-lg font-medium text-slate-900 mb-2">
                            No forms yet
                        </h3>
                        <p className="text-slate-600 mb-4">
                            Create your first form to get started
                        </p>
                        <Button
                            onClick={() => setShowCreateDialog(true)}
                            className="bg-gradient-to-r from-slate-900 to-slate-700 hover:from-slate-800 hover:to-slate-600"
                        >
                            <Plus className="h-4 w-4 mr-2" />
                            Create Your First Form
                        </Button>
                    </CardContent>
                </Card>
            ) : (
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {forms.map(form => (
                        <Card
                            key={form.id}
                            className="hover:shadow-lg transition-shadow duration-200"
                        >
                            <CardHeader>
                                <div className="flex justify-between items-start">
                                    <div className="flex-1">
                                        <CardTitle className="text-lg font-semibold text-slate-900">
                                            {form.name?.en ||
                                                form.name?.de ||
                                                'Untitled Form'}
                                        </CardTitle>
                                        <CardDescription className="text-slate-600 mt-1">
                                            {form.description?.en ||
                                                form.description?.de ||
                                                'No description'}
                                        </CardDescription>
                                    </div>
                                    <div
                                        className={`px-2 py-1 rounded-full text-xs font-medium ${
                                            form.is_active
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800'
                                        }`}
                                    >
                                        {form.is_active ? 'Active' : 'Inactive'}
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3">
                                    <div className="text-sm text-slate-500">
                                        <span className="font-medium">
                                            Locales:
                                        </span>{' '}
                                        {form.configuration?.locales?.join(
                                            ', '
                                        ) || 'None'}
                                    </div>
                                    <div className="text-sm text-slate-500">
                                        <span className="font-medium">
                                            Fields:
                                        </span>{' '}
                                        {form.fields?.length || 0}
                                    </div>
                                    <div className="text-sm text-slate-500">
                                        <span className="font-medium">
                                            Created:
                                        </span>{' '}
                                        {new Date(
                                            form.created_at
                                        ).toLocaleDateString()}
                                    </div>

                                    <div className="flex gap-2 pt-2">
                                        <Button
                                            onClick={() =>
                                                setIsFormPreviewOpen(true)
                                            }
                                            className="flex-1 bg-slate-600 hover:bg-slate-700 text-white"
                                        >
                                            <ExternalLink className="h-4 w-4 mr-2" />
                                            Open
                                        </Button>
                                        <Button
                                            onClick={() =>
                                                handleOpenForm(form.id)
                                            }
                                            variant="outline"
                                            className="border-blue-300 text-blue-700 hover:bg-blue-50 hover:border-blue-400"
                                        >
                                            <Edit className="h-4 w-4 mr-2" />
                                            Edit
                                        </Button>
                                        <Button
                                            onClick={() =>
                                                handleDeleteForm(
                                                    form.id,
                                                    form.name?.en ||
                                                        form.name?.de ||
                                                        'Untitled Form'
                                                )
                                            }
                                            variant="outline"
                                            className="border-red-300 text-red-700 hover:bg-red-50 hover:border-red-400"
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            )}

            {/* Delete Confirmation Dialog */}
            {deleteDialog.isOpen && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div className="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
                        <div className="flex items-center mb-4">
                            <div className="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <Trash2 className="h-6 w-6 text-red-600" />
                            </div>
                            <div className="ml-4">
                                <h3 className="text-lg font-semibold text-gray-900">
                                    Delete Form
                                </h3>
                                <p className="text-sm text-gray-500">
                                    This action cannot be undone
                                </p>
                            </div>
                        </div>

                        <div className="mb-6">
                            <p className="text-gray-700">
                                Are you sure you want to delete{' '}
                                <span className="font-semibold">
                                    "{deleteDialog.formName}"
                                </span>
                                ?
                            </p>
                            <p className="text-sm text-gray-500 mt-2">
                                This will permanently delete the form and all
                                its fields.
                            </p>
                        </div>

                        <div className="flex gap-3 justify-end">
                            <Button
                                onClick={() =>
                                    setDeleteDialog({
                                        isOpen: false,
                                        formId: null,
                                        formName: '',
                                    })
                                }
                                variant="outline"
                                className="border-gray-300 text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </Button>
                            <Button
                                onClick={confirmDeleteForm}
                                className="bg-red-600 hover:bg-red-700 text-white"
                            >
                                Delete Form
                            </Button>
                        </div>
                    </div>
                </div>
            )}

            {/* Form Preview Dialog */}
            <FormPreviewDialog
                isOpen={isFormPreviewOpen}
                onOpenChange={setIsFormPreviewOpen}
            />

            {/* Create Form Dialog */}
            <CreateFormDialog
                isOpen={showCreateDialog}
                onOpenChange={setShowCreateDialog}
                onSuccess={newForm => {
                    // Add the new form to the list
                    setForms(prevForms => [newForm, ...prevForms]);
                }}
            />
        </div>
    );
}
