import { useState, useEffect } from 'react';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Save, X } from 'lucide-react';
import { useApi } from '@/hooks/useApi';
import ActionButton from '@/components/ui/ActionButton';
import DemoDialog from '@/components/ui/DemoDialog';

export default function FormEditor({ formId, onClose }) {
    const { apiCall } = useApi();
    const [form, setForm] = useState(null);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    // Form data state
    const [formData, setFormData] = useState({
        name: '',
        description: '',
        is_active: true,
        locales: ['en'],
    });

    const [demoDialog, setDemoDialog] = useState(false);

    useEffect(() => {
        if (formId) {
            fetchForm();
        }
    }, [formId]);

    const fetchForm = async () => {
        try {
            setLoading(true);
            const data = await apiCall(`/forms/${formId}`);
            setForm(data.data);
            setFormData({
                name: data.data.name?.en || '',
                description: data.data.description?.en || '',
                is_active: data.data.is_active ?? true,
                locales: data.data.configuration?.locales || ['en'],
            });
        } catch (error) {
            setError(error.message);
        } finally {
            setLoading(false);
        }
    };

    const handleSave = async () => {
        try {
            setSaving(true);
            setError('');
            setSuccess('');

            await apiCall(`/forms/${formId}`, {
                method: 'PUT',
                body: JSON.stringify({
                    name: { en: formData.name },
                    description: { en: formData.description },
                    is_active: formData.is_active,
                    configuration: {
                        locales: formData.locales,
                    },
                }),
            });

            setSuccess('Form updated successfully!');
            // Update the form state
            setForm(prev => ({
                ...prev,
                name: { en: formData.name },
                description: { en: formData.description },
                is_active: formData.is_active,
                configuration: {
                    ...prev.configuration,
                    locales: formData.locales,
                },
            }));
        } catch (error) {
            setError(error.message);
        } finally {
            setSaving(false);
        }
    };

    if (loading) {
        return (
            <div className="space-y-6">
                <div className="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <div className="animate-pulse">
                        <div className="h-6 bg-slate-200 rounded w-1/3 mb-4"></div>
                        <div className="h-4 bg-slate-200 rounded w-1/2 mb-2"></div>
                        <div className="h-4 bg-slate-200 rounded w-2/3"></div>
                    </div>
                </div>
            </div>
        );
    }

    if (!form) {
        return (
            <div className="space-y-6">
                <div className="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <p className="text-red-600">Form not found</p>
                </div>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex justify-between items-center">
                <div>
                    <h2 className="text-xl font-semibold text-slate-900">
                        Edit Form:{' '}
                        {form.name?.en || form.name?.de || `Form ${form.id}`}
                    </h2>
                    <p className="text-slate-600">
                        Update form details and configuration
                    </p>
                </div>
                <div className="flex items-center gap-2">
                    <ActionButton
                        onClick={() => setDemoDialog(true)}
                        variant="outline"
                        className="border-slate-300 text-slate-700 hover:bg-slate-50 hover:border-slate-400"
                    >
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
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                            />
                        </svg>
                        Open Form
                    </ActionButton>
                    <ActionButton onClick={onClose} variant="outline">
                        <X className="h-4 w-4 mr-2" />
                        Close
                    </ActionButton>
                </div>
            </div>

            {/* Form Details */}
            <Card>
                <CardHeader>
                    <CardTitle>Basic Information</CardTitle>
                </CardHeader>
                <CardContent className="space-y-6">
                    {/* Name Field */}
                    <div className="space-y-4">
                        <h3 className="text-lg font-medium text-slate-900">
                            Form Name
                        </h3>
                        <div className="w-full">
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Form Name *
                            </label>
                            <Input
                                value={formData.name || ''}
                                onChange={e =>
                                    setFormData(prev => ({
                                        ...prev,
                                        name: e.target.value,
                                    }))
                                }
                                placeholder="Enter form name"
                                className="w-full"
                                disabled
                            />
                        </div>
                    </div>

                    {/* Description Field */}
                    <div className="space-y-4">
                        <h3 className="text-lg font-medium text-slate-900">
                            Form Description
                        </h3>
                        <div className="w-full">
                            <label className="block text-sm font-medium text-slate-700 mb-2">
                                Form Description
                            </label>
                            <Textarea
                                value={formData.description || ''}
                                onChange={e =>
                                    setFormData(prev => ({
                                        ...prev,
                                        description: e.target.value,
                                    }))
                                }
                                placeholder="Enter form description"
                                rows={3}
                                className="w-full"
                                disabled
                            />
                        </div>
                    </div>

                    {/* Active Status */}
                    <div className="flex items-center space-x-2">
                        <Checkbox
                            id="is_active"
                            checked={formData.is_active}
                            onCheckedChange={checked =>
                                setFormData(prev => ({
                                    ...prev,
                                    is_active: checked,
                                }))
                            }
                            disabled
                        />
                        <label
                            htmlFor="is_active"
                            className="text-sm font-medium text-slate-700"
                        >
                            Form is active
                        </label>
                    </div>

                    {/* Demo Information for Form */}
                    <div className="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                        <p className="text-xs text-amber-800 leading-relaxed">
                            <strong>Demo Note:</strong> This is a demo - full
                            implementation would enable you to completely modify
                            forms including field management, advanced
                            validation, conditional logic, form templates, and
                            extensive customization options...
                        </p>
                    </div>
                </CardContent>
            </Card>

            {/* Language Support */}
            <Card>
                <CardHeader>
                    <CardTitle>Language Support</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    <div className="flex items-center gap-3">
                        <div className="flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-2 rounded-lg">
                            <span className="text-sm font-medium">English</span>
                        </div>
                        <p className="text-sm text-slate-600">
                            This is a demo for single language only, but the
                            backend supports dynamic language addition
                        </p>
                    </div>
                </CardContent>
            </Card>

            {/* Form Fields */}
            <Card>
                <CardHeader>
                    <CardTitle>Form Fields</CardTitle>
                </CardHeader>
                <CardContent>
                    {form.fields && form.fields.length > 0 ? (
                        <div className="space-y-4">
                            {/* Add New Field Before First Field */}
                            <div className="text-center">
                                <ActionButton
                                    variant="outline"
                                    className="border-green-300 text-green-700 hover:bg-green-50 hover:border-green-400"
                                    disabled
                                >
                                    <svg
                                        className="h-3 w-3 mr-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                        />
                                    </svg>
                                    Add New Field
                                </ActionButton>
                            </div>

                            {form.fields
                                .sort((a, b) => a.order - b.order)
                                .map((field, index) => (
                                    <div key={field.id}>
                                        <div className="border border-slate-200 rounded-lg p-4 bg-slate-50">
                                            <div className="flex items-center justify-between mb-3">
                                                <div className="flex items-center gap-3">
                                                    <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">
                                                        {field.order}
                                                    </span>
                                                    <span className="text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded-full font-medium">
                                                        {field.type}
                                                    </span>
                                                    {field.configuration
                                                        ?.required && (
                                                        <span className="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">
                                                            Required
                                                        </span>
                                                    )}
                                                </div>

                                                {/* Validation Rule Buttons */}
                                                <div className="flex items-center gap-2">
                                                    <div className="h-px w-8 bg-slate-300"></div>
                                                    {getValidationButtonsForFieldType(
                                                        field.type
                                                    )}

                                                    {/* Remove Field Button */}
                                                    <div className="h-px w-4 bg-slate-300"></div>
                                                    <ActionButton
                                                        variant="outline"
                                                        className="border-red-300 text-red-700 hover:bg-red-50 hover:border-red-400"
                                                        disabled
                                                    >
                                                        <svg
                                                            className="h-3 w-3 mr-1"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke="currentColor"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                strokeWidth={2}
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                            />
                                                        </svg>
                                                        Remove
                                                    </ActionButton>
                                                </div>
                                            </div>

                                            <div className="space-y-2">
                                                <div>
                                                    <label className="block text-sm font-medium text-slate-700 mb-1">
                                                        Field Name
                                                    </label>
                                                    <Input
                                                        value={
                                                            field.configuration
                                                                ?.name ||
                                                            'Unnamed Field'
                                                        }
                                                        className="w-full"
                                                        disabled
                                                    />
                                                </div>

                                                <div>
                                                    <label className="block text-sm font-medium text-slate-700 mb-1">
                                                        Label
                                                    </label>
                                                    <Input
                                                        value={
                                                            field.configuration
                                                                ?.label?.en ||
                                                            'No label'
                                                        }
                                                        className="w-full"
                                                        disabled
                                                    />
                                                </div>

                                                {field.configuration
                                                    ?.placeholder && (
                                                    <div>
                                                        <label className="block text-sm font-medium text-slate-700 mb-1">
                                                            Placeholder
                                                        </label>
                                                        <Input
                                                            value={
                                                                field
                                                                    .configuration
                                                                    .placeholder
                                                                    .en ||
                                                                'No placeholder'
                                                            }
                                                            className="w-full"
                                                            disabled
                                                        />
                                                    </div>
                                                )}

                                                {field.configuration?.options &&
                                                    field.configuration.options
                                                        .length > 0 && (
                                                        <div>
                                                            <label className="block text-sm font-medium text-slate-700 mb-1">
                                                                Options
                                                            </label>
                                                            <div className="space-y-1">
                                                                {field.configuration.options.map(
                                                                    (
                                                                        option,
                                                                        optIndex
                                                                    ) => (
                                                                        <Input
                                                                            key={
                                                                                optIndex
                                                                            }
                                                                            value={`${option.value}${option.label?.en ? ` (${option.label.en})` : ''}`}
                                                                            className="w-full"
                                                                            disabled
                                                                        />
                                                                    )
                                                                )}
                                                            </div>
                                                        </div>
                                                    )}

                                                {field.validation_rules &&
                                                    Object.keys(
                                                        field.validation_rules
                                                    ).length > 0 && (
                                                        <div>
                                                            <label className="block text-sm font-medium text-slate-700 mb-1">
                                                                Validation Rules
                                                            </label>
                                                            <div className="space-y-1">
                                                                {Object.entries(
                                                                    field.validation_rules
                                                                ).map(
                                                                    ([
                                                                        ruleName,
                                                                        rule,
                                                                    ]) => (
                                                                        <div
                                                                            key={
                                                                                ruleName
                                                                            }
                                                                        >
                                                                            <Input
                                                                                value={`${ruleName}${rule.rule ? ` (${rule.rule})` : ''}`}
                                                                                className="w-full mb-1"
                                                                                disabled
                                                                            />
                                                                            {rule
                                                                                .error_messages
                                                                                ?.en && (
                                                                                <Input
                                                                                    value={`Error: ${rule.error_messages.en}`}
                                                                                    className="w-full text-xs"
                                                                                    disabled
                                                                                />
                                                                            )}
                                                                        </div>
                                                                    )
                                                                )}
                                                            </div>
                                                        </div>
                                                    )}
                                            </div>

                                            {/* Demo Information */}
                                            <div className="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                                <p className="text-xs text-amber-800 leading-relaxed">
                                                    <strong>Demo Note:</strong>{' '}
                                                    This is a demo - full
                                                    implementation would enable
                                                    you to completely manipulate
                                                    fields in terms of addition
                                                    of validation rules, i18n,
                                                    field order and can be
                                                    extended to introduce custom
                                                    styling, autocomplete,
                                                    custom validation...
                                                </p>
                                            </div>
                                        </div>

                                        {/* Add New Field After This Field */}
                                        <div className="text-center mt-4">
                                            <ActionButton
                                                variant="outline"
                                                className="border-green-300 text-green-700 hover:bg-green-50 hover:border-green-400"
                                                disabled
                                            >
                                                <svg
                                                    className="h-3 w-3 mr-1"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        strokeWidth={2}
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                                    />
                                                </svg>
                                                Add New Field
                                            </ActionButton>
                                        </div>
                                    </div>
                                ))}

                            {/* Add New Field After Last Field */}
                            <div className="text-center">
                                <ActionButton
                                    variant="outline"
                                    className="border-green-300 text-green-700 hover:bg-green-50 hover:border-green-400"
                                    disabled
                                >
                                    <svg
                                        className="h-3 w-3 mr-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                        />
                                    </svg>
                                    Add New Field
                                </ActionButton>
                            </div>
                        </div>
                    ) : (
                        <div className="text-center py-8 text-slate-500">
                            <div className="text-slate-400 mb-2">
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
                            <p className="text-sm">
                                No fields defined for this form
                            </p>
                        </div>
                    )}
                </CardContent>
            </Card>

            {/* Form Statistics */}
            <Card>
                <CardHeader>
                    <CardTitle>Form Statistics</CardTitle>
                </CardHeader>
                <CardContent>
                    <div className="grid gap-4 md:grid-cols-3">
                        <div className="text-center">
                            <div className="text-2xl font-bold text-blue-600">
                                {form.fields?.length || 0}
                            </div>
                            <div className="text-sm text-slate-600">
                                Total Fields
                            </div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-green-600">
                                {form.is_active ? 'Active' : 'Inactive'}
                            </div>
                            <div className="text-sm text-slate-600">Status</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-purple-600">
                                {formData.locales.length}
                            </div>
                            <div className="text-sm text-slate-600">
                                Languages
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Error and Success Messages */}
            {error && (
                <div className="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p className="text-red-700">{error}</p>
                </div>
            )}

            {success && (
                <div className="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p className="text-green-700">{success}</p>
                </div>
            )}

            {/* Action Buttons */}
            <div className="flex justify-end gap-3">
                <ActionButton onClick={onClose} variant="outline" disabled>
                    Cancel
                </ActionButton>
                <ActionButton
                    onClick={handleSave}
                    disabled={true}
                    className="bg-blue-600 hover:bg-blue-700"
                >
                    <Save className="h-4 w-4 mr-2" />
                    {saving ? 'Saving...' : 'Save Changes'}
                </ActionButton>
            </div>

            {/* Demo Dialog */}
            <DemoDialog isOpen={demoDialog} onOpenChange={setDemoDialog} />
        </div>
    );

    // Helper function to get validation buttons based on field type
    function getValidationButtonsForFieldType(fieldType) {
        const commonRules = [
            {
                label: 'Required',
                color: 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100',
            },
            {
                label: 'Min Length',
                color: 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100',
            },
            {
                label: 'Max Length',
                color: 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100',
            },
        ];

        const typeSpecificRules = {
            text: [
                {
                    label: 'Pattern',
                    color: 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100',
                },
                {
                    label: 'Custom Regex',
                    color: 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100',
                },
            ],
            email: [
                {
                    label: 'Email Format',
                    color: 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100',
                },
                {
                    label: 'Domain Check',
                    color: 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100',
                },
            ],
            number: [
                {
                    label: 'Min Value',
                    color: 'bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100',
                },
                {
                    label: 'Max Value',
                    color: 'bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100',
                },
                {
                    label: 'Integer Only',
                    color: 'bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100',
                },
                {
                    label: 'Decimal Places',
                    color: 'bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100',
                },
            ],
            tel: [
                {
                    label: 'Phone Format',
                    color: 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100',
                },
                {
                    label: 'Country Code',
                    color: 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100',
                },
            ],
            url: [
                {
                    label: 'URL Format',
                    color: 'bg-teal-50 text-teal-700 border-teal-200 hover:bg-teal-100',
                },
                {
                    label: 'Protocol Check',
                    color: 'bg-teal-50 text-teal-700 border-teal-200 hover:bg-teal-100',
                },
            ],
            date: [
                {
                    label: 'Min Date',
                    color: 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100',
                },
                {
                    label: 'Max Date',
                    color: 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100',
                },
                {
                    label: 'Date Range',
                    color: 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100',
                },
            ],
            file: [
                {
                    label: 'File Size',
                    color: 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100',
                },
                {
                    label: 'File Type',
                    color: 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100',
                },
                {
                    label: 'Max Files',
                    color: 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100',
                },
            ],
            select: [
                {
                    label: 'Min Selection',
                    color: 'bg-violet-50 text-violet-700 border-violet-200 hover:bg-violet-100',
                },
                {
                    label: 'Max Selection',
                    color: 'bg-violet-50 text-violet-700 border-violet-200 hover:bg-violet-100',
                },
            ],
            radio: [
                {
                    label: 'Single Choice',
                    color: 'bg-violet-50 text-violet-700 border-violet-200 hover:bg-violet-100',
                },
            ],
            checkbox: [
                {
                    label: 'Min Checked',
                    color: 'bg-violet-50 text-violet-700 border-violet-200 hover:bg-violet-100',
                },
                {
                    label: 'Max Checked',
                    color: 'bg-violet-50 text-violet-700 border-violet-200 hover:bg-violet-100',
                },
            ],
            textarea: [
                {
                    label: 'Word Count',
                    color: 'bg-cyan-50 text-cyan-700 border-cyan-200 hover:bg-cyan-100',
                },
                {
                    label: 'Line Count',
                    color: 'bg-cyan-50 text-cyan-700 border-cyan-200 hover:bg-cyan-100',
                },
            ],
        };

        const allRules = [
            ...commonRules,
            ...(typeSpecificRules[fieldType] || []),
        ];

        return allRules.map((rule, index) => (
            <button
                key={index}
                className={`px-2 py-1 text-xs font-medium rounded border transition-colors ${rule.color}`}
                title={`Add ${rule.label} validation rule`}
                disabled
            >
                + {rule.label}
            </button>
        ));
    }
}
