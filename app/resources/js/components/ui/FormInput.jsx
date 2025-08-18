import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useDialogStyles } from '@/hooks/useDialogStyles';

export default function FormInput({
    id,
    name,
    label,
    type = 'text',
    value,
    onChange,
    placeholder,
    required = false,
    className = '',
}) {
    const styles = useDialogStyles();

    return (
        <div className="space-y-2">
            <Label htmlFor={id} className="text-slate-700 font-medium">
                {label} {required && '*'}
            </Label>
            <Input
                id={id}
                name={name}
                type={type}
                value={value}
                onChange={onChange}
                placeholder={placeholder}
                required={required}
                className={`${styles.inputField} ${className}`}
            />
        </div>
    );
}
