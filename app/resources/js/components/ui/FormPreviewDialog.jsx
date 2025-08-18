import BaseDialog from '@/components/ui/BaseDialog';
import ActionButton from '@/components/ui/ActionButton';

export default function FormPreviewDialog({ isOpen, onOpenChange }) {
    return (
        <BaseDialog
            isOpen={isOpen}
            onOpenChange={onOpenChange}
            title="Form Preview"
            description="Preview your form in action"
        >
            <div className="mb-6">
                <p className="text-gray-700 mb-3">
                    This feature allows you to preview your form layout and test
                    data entry functionality.
                </p>
                <p className="text-sm text-gray-500">
                    Form preview and testing capabilities are available in the
                    full implementation.
                </p>
            </div>

            <div className="flex justify-end">
                <ActionButton
                    onClick={() => onOpenChange(false)}
                    className="bg-blue-600 hover:bg-blue-700 text-white"
                >
                    Got it
                </ActionButton>
            </div>
        </BaseDialog>
    );
}
