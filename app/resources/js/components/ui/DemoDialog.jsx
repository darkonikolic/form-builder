import BaseDialog from '@/components/ui/BaseDialog';
import ActionButton from '@/components/ui/ActionButton';

export default function DemoDialog({ isOpen, onOpenChange }) {
    return (
        <BaseDialog
            isOpen={isOpen}
            onOpenChange={onOpenChange}
            title="Demo Project"
            description="This is a demonstration version"
        >
            <div className="mb-6">
                <p className="text-gray-700 mb-3">
                    This is a demo project. In the full version, you would be
                    able to see the generated form layout and test data entry
                    here.
                </p>
                <p className="text-sm text-gray-500">
                    However, since you haven't paid for the premium version yet,
                    this feature is not available.
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
