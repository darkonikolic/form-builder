import { useState } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import BaseDialog from '@/components/ui/BaseDialog';
import ActionButton from '@/components/ui/ActionButton';
import FormInput from '@/components/ui/FormInput';

export default function LoginDialog({ isOpen, onOpenChange, onSuccess }) {
    const [isLogin, setIsLogin] = useState(true);
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState('');
    const { login, register } = useAuth();

    const handleSubmit = async e => {
        e.preventDefault();
        setLoading(true);
        setMessage('');

        // Frontend validation for registration
        if (!isLogin) {
            if (formData.password !== formData.password_confirmation) {
                setMessage('Passwords do not match');
                setLoading(false);
                return;
            }
            if (formData.password.length < 8) {
                setMessage('Password must be at least 8 characters long');
                setLoading(false);
                return;
            }
        }

        try {
            let result;
            if (isLogin) {
                result = await login(formData.email, formData.password);
            } else {
                result = await register(
                    formData.name,
                    formData.email,
                    formData.password,
                    formData.password_confirmation
                );
            }

            if (result.success) {
                setMessage(result.message || 'Success!');
                if (isLogin) {
                    onSuccess?.();
                    onOpenChange(false);
                } else {
                    // After registration, switch to login mode
                    setIsLogin(true);
                    setFormData({
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                    });
                }
            } else {
                setMessage(result.message || 'Operation failed');
            }
        } catch (error) {
            setMessage('An error occurred');
        } finally {
            setLoading(false);
        }
    };

    const handleInputChange = e => {
        setFormData(prev => ({
            ...prev,
            [e.target.name]: e.target.value,
        }));

        // Clear password confirmation when password changes
        if (e.target.name === 'password') {
            setFormData(prev => ({
                ...prev,
                password_confirmation: '',
            }));
        }
    };

    const toggleMode = () => {
        setIsLogin(!isLogin);
        setFormData({
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
        });
        setMessage('');
    };

    return (
        <BaseDialog
            isOpen={isOpen}
            onOpenChange={onOpenChange}
            title={isLogin ? 'Welcome Back! 🔐' : 'Create Account ✨'}
            description={
                isLogin
                    ? 'Sign in to your account'
                    : 'Join us and start building forms'
            }
        >
            {isLogin && (
                <div className="mt-4 p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <p className="text-sm text-slate-600 mb-3">
                        Feel free to use existing users:
                    </p>
                    <div className="space-y-2">
                        <button
                            type="button"
                            onClick={() => {
                                setFormData({
                                    ...formData,
                                    email: 'admin@example.com',
                                    password: 'password',
                                });
                            }}
                            className="w-full text-left p-2 text-xs bg-white border border-slate-200 rounded hover:bg-slate-50 transition-colors"
                        >
                            <span className="font-medium">Admin User:</span>{' '}
                            admin@example.com / password
                        </button>
                        <button
                            type="button"
                            onClick={() => {
                                setFormData({
                                    ...formData,
                                    email: 'test@example.com',
                                    password: 'password',
                                });
                            }}
                            className="w-full text-left p-2 text-xs bg-white border border-slate-200 rounded hover:bg-slate-50 transition-colors"
                        >
                            <span className="font-medium">Test User:</span>{' '}
                            test@example.com / password
                        </button>
                        <button
                            type="button"
                            onClick={() => {
                                setFormData({
                                    ...formData,
                                    email: 'demo@example.com',
                                    password: 'password',
                                });
                            }}
                            className="w-full text-left p-2 text-xs bg-white border border-slate-200 rounded hover:bg-slate-50 transition-colors"
                        >
                            <span className="font-medium">Demo User:</span>{' '}
                            demo@example.com / password
                        </button>
                    </div>
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-4">
                {!isLogin && (
                    <FormInput
                        id="name"
                        name="name"
                        label="Full Name"
                        value={formData.name}
                        onChange={handleInputChange}
                        placeholder="Enter your full name"
                        required={!isLogin}
                    />
                )}

                <FormInput
                    id="email"
                    name="email"
                    label="Email"
                    type="email"
                    value={formData.email}
                    onChange={handleInputChange}
                    placeholder="Enter your email"
                    required
                />

                <FormInput
                    id="password"
                    name="password"
                    label="Password"
                    type="password"
                    value={formData.password}
                    onChange={handleInputChange}
                    placeholder="Enter your password"
                    required
                />

                {!isLogin && (
                    <FormInput
                        id="password_confirmation"
                        name="password_confirmation"
                        label="Confirm Password"
                        type="password"
                        value={formData.password_confirmation}
                        onChange={handleInputChange}
                        placeholder="Confirm your password"
                        required={!isLogin}
                    />
                )}

                {message && (
                    <div
                        className={`p-3 rounded-lg text-sm ${
                            message.includes('successful') ||
                            message.includes('Success')
                                ? 'bg-green-50 text-green-700 border border-green-200'
                                : 'bg-red-50 text-red-700 border border-red-200'
                        }`}
                    >
                        {message}
                    </div>
                )}

                <div className="space-y-3">
                    <ActionButton
                        type="submit"
                        disabled={loading}
                        className="w-full"
                    >
                        {loading
                            ? 'Processing...'
                            : isLogin
                              ? 'Sign In'
                              : 'Create Account'}
                    </ActionButton>

                    <div className="text-center">
                        <button
                            type="button"
                            onClick={toggleMode}
                            className="text-sm text-slate-600 hover:text-slate-800 underline"
                        >
                            {isLogin
                                ? "Don't have an account? Sign up"
                                : 'Already have an account? Sign in'}
                        </button>
                    </div>
                </div>
            </form>
        </BaseDialog>
    );
}
