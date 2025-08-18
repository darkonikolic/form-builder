import { Button } from '@/components/ui/button';
import { useDialogStyles } from '@/hooks/useDialogStyles';

export default function ActionButton({
    children,
    variant = 'primary',
    disabled = false,
    onClick,
    type = 'button',
    className = '',
    ...props
}) {
    const styles = useDialogStyles();

    const getButtonClasses = () => {
        switch (variant) {
            case 'primary':
                return styles.primaryButton;
            case 'outline':
                return styles.outlineButton;
            case 'danger':
                return 'bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white';
            default:
                return styles.primaryButton;
        }
    };

    return (
        <Button
            type={type}
            variant={variant === 'outline' ? 'outline' : 'default'}
            disabled={disabled}
            onClick={onClick}
            className={`${getButtonClasses()} ${className}`}
            {...props}
        >
            {children}
        </Button>
    );
}
