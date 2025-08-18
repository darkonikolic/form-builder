import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useDialogStyles } from '@/hooks/useDialogStyles';

export default function BaseDialog({
    isOpen,
    onOpenChange,
    title,
    description,
    children,
    size = 'md',
}) {
    const styles = useDialogStyles();

    return (
        <Dialog open={isOpen} onOpenChange={onOpenChange}>
            <DialogContent
                className={
                    size === 'large'
                        ? styles.dialogContentLarge
                        : styles.dialogContent
                }
            >
                <DialogHeader>
                    <DialogTitle className={styles.dialogTitle}>
                        {title}
                    </DialogTitle>
                    {description && (
                        <DialogDescription className="text-slate-600 mt-2">
                            {description}
                        </DialogDescription>
                    )}
                </DialogHeader>
                {children}
            </DialogContent>
        </Dialog>
    );
}
