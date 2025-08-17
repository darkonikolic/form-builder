import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

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
        <Dialog open={isOpen} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-2xl bg-white/95 backdrop-blur-sm border-0 shadow-2xl">
                <DialogHeader>
                    <DialogTitle className="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                        Create New Form ✨
                    </DialogTitle>
                    <DialogDescription className="text-slate-600 mt-2">
                        Create a new form with multiple language support
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Form Name */}
                    <div className="space-y-2">
                        <Label
                            htmlFor="name_en"
                            className="text-slate-700 font-medium"
                        >
                            Form Name *
                        </Label>
                        <Input
                            id="name_en"
                            value={formData.name.en}
                            onChange={e =>
                                handleInputChange('name', 'en', e.target.value)
                            }
                            placeholder="Enter form name"
                            required
                            className="border-slate-300 focus:border-slate-500 focus:ring-slate-500"
                        />
                    </div>

                    {/* Form Description */}
                    <div className="space-y-2">
                        <Label
                            htmlFor="desc_en"
                            className="text-slate-700 font-medium"
                        >
                            Description
                        </Label>
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
                        <Label className="text-slate-700 font-medium">
                            Language
                        </Label>
                        <div className="flex items-center space-x-2">
                            <Checkbox
                                id="locale_en"
                                checked={formData.configuration.locales.includes(
                                    'en'
                                )}
                                disabled
                                className="border-slate-300"
                            />
                            <Label
                                htmlFor="locale_en"
                                className="text-sm text-slate-600"
                            >
                                English (Default)
                            </Label>
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
                        <Label
                            htmlFor="is_active"
                            className="text-sm text-slate-600"
                        >
                            Form is active
                        </Label>
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
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => onOpenChange(false)}
                            className="border-slate-300 text-slate-700 hover:bg-slate-50"
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            disabled={loading}
                            className="bg-gradient-to-r from-slate-900 to-slate-700 hover:from-slate-800 hover:to-slate-600 text-white font-medium py-2 px-6 rounded-lg transition-all duration-200 hover:shadow-lg"
                        >
                            {loading ? 'Creating...' : 'Create Form'}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    );
}
