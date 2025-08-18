import { useState } from 'react';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import BaseDialog from '@/components/ui/BaseDialog';
import ActionButton from '@/components/ui/ActionButton';
import FormInput from '@/components/ui/FormInput';

export default function CreateFormDialog({ isOpen, onOpenChange, onSuccess }) {
    const [formData, setFormData] = useState({
        name: {
            en: '',
            de: '',
        },
        description: {
            en: '',
            de: '',
        },
        is_active: true,
        configuration: {
            locales: ['en', 'de'],
        },
    });
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState('');

    const handleSubmit = async e => {
        e.preventDefault();
        setLoading(true);
        setMessage('');

        // Prepare data for submission - copy English to other languages if they are empty
        const submitData = {
            ...formData,
            name: Object.fromEntries(
                formData.configuration.locales.map(locale => [
                    locale,
                    formData.name[locale] || formData.name.en || '',
                ])
            ),
            description: Object.fromEntries(
                formData.configuration.locales.map(locale => [
                    locale,
                    formData.description[locale] ||
                        formData.description.en ||
                        '',
                ])
            ),
        };

        try {
            const response = await fetch('/api/forms', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
                },
                body: JSON.stringify(submitData),
            });

            if (response.ok) {
                const data = await response.json();
                setMessage('Form created successfully!');
                onSuccess?.(data.data);
                onOpenChange(false);
                // Reset form
                setFormData({
                    name: { en: '', de: '' },
                    description: { en: '', de: '' },
                    is_active: true,
                    configuration: { locales: ['en', 'de'] },
                });
            } else {
                const error = await response.json();
                setMessage(error.message || 'Failed to create form');
            }
        } catch (error) {
            setMessage('Network error occurred');
        } finally {
            setLoading(false);
        }
    };

    const handleInputChange = (field, locale, value) => {
        if (locale) {
            setFormData(prev => ({
                ...prev,
                [field]: {
                    ...prev[field],
                    [locale]: value,
                },
            }));
        } else {
            setFormData(prev => ({
                ...prev,
                [field]: value,
            }));
        }
    };

    const handleLocaleToggle = locale => {
        setFormData(prev => {
            const locales = prev.configuration.locales;
            const newLocales = locales.includes(locale)
                ? locales.filter(l => l !== locale)
                : [...locales, locale];

            return {
                ...prev,
                configuration: {
                    ...prev.configuration,
                    locales: newLocales,
                },
            };
        });
    };

    return (
        <BaseDialog
            isOpen={isOpen}
            onOpenChange={onOpenChange}
            title="Create New Form"
            description="Create a new form with multiple language support"
            size="large"
        >
            <form onSubmit={handleSubmit} className="space-y-6">
                {/* Form Name */}
                <FormInput
                    id="name_en"
                    label="Form Name *"
                    value={formData.name.en}
                    onChange={e =>
                        handleInputChange('name', 'en', e.target.value)
                    }
                    placeholder="Enter form name"
                    required
                />

                {/* Form Description */}
                <div className="space-y-2">
                    <label
                        htmlFor="desc_en"
                        className="text-slate-700 font-medium"
                    >
                        Description
                    </label>
                    <Textarea
                        id="desc_en"
                        value={formData.description.en}
                        onChange={e =>
                            handleInputChange(
                                'description',
                                'en',
                                e.target.value
                            )
                        }
                        placeholder="Enter form description"
                        className="border-slate-300 focus:border-slate-500 focus:ring-slate-500"
                        rows={3}
                    />
                </div>

                {/* Locales */}
                <div className="space-y-2">
                    <label className="text-slate-700 font-medium">
                        Language
                    </label>
                    <div className="flex items-center space-x-2">
                        <Checkbox
                            id="locale_en"
                            checked={formData.configuration.locales.includes(
                                'en'
                            )}
                            disabled
                            className="border-slate-300"
                        />
                        <label
                            htmlFor="locale_en"
                            className="text-sm text-slate-600"
                        >
                            English (Default)
                        </label>
                    </div>
                </div>

                {/* Active Status */}
                <div className="flex items-center space-x-2">
                    <Checkbox
                        id="is_active"
                        checked={formData.is_active}
                        onCheckedChange={checked =>
                            handleInputChange('is_active', null, checked)
                        }
                        className="border-slate-300"
                    />
                    <label
                        htmlFor="is_active"
                        className="text-sm text-slate-600"
                    >
                        Form is active
                    </label>
                </div>

                {/* Message */}
                {message && (
                    <div
                        className={`p-3 rounded-lg text-sm ${
                            message.includes('successfully')
                                ? 'bg-green-50 text-green-700 border border-green-200'
                                : 'bg-red-50 text-red-700 border border-red-200'
                        }`}
                    >
                        {message}
                    </div>
                )}

                {/* Buttons */}
                <div className="flex justify-end space-x-3">
                    <ActionButton
                        type="button"
                        variant="outline"
                        onClick={() => onOpenChange(false)}
                    >
                        Cancel
                    </ActionButton>
                    <ActionButton type="submit" disabled={loading}>
                        {loading ? 'Creating...' : 'Create Form'}
                    </ActionButton>
                </div>
            </form>
        </BaseDialog>
    );
}
