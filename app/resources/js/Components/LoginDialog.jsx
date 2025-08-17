import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { useAuth } from '@/contexts/AuthContext';

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
        <Dialog open={isOpen} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-md bg-white/95 backdrop-blur-sm border-0 shadow-2xl">
                <DialogHeader>
                    <DialogTitle className="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                        {isLogin ? 'Welcome Back! 🔐' : 'Create Account ✨'}
                    </DialogTitle>
                    <DialogDescription className="text-slate-600 mt-2">
                        {isLogin
                            ? 'Sign in to your account'
                            : 'Join us and start building forms'}
                    </DialogDescription>

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
                                    <span className="font-medium">
                                        Admin User:
                                    </span>{' '}
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
                                    <span className="font-medium">
                                        Test User:
                                    </span>{' '}
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
                                    <span className="font-medium">
                                        Demo User:
                                    </span>{' '}
                                    demo@example.com / password
                                </button>
                            </div>
                        </div>
                    )}
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-4">
                    {!isLogin && (
                        <div className="space-y-2">
                            <Label
                                htmlFor="name"
                                className="text-slate-700 font-medium"
                            >
                                Full Name
                            </Label>
                            <Input
                                id="name"
                                name="name"
                                type="text"
                                value={formData.name}
                                onChange={handleInputChange}
                                placeholder="Enter your full name"
                                required={!isLogin}
                                className="border-slate-300 focus:border-slate-500 focus:ring-slate-500"
                            />
                        </div>
                    )}

                    <div className="space-y-2">
                        <Label
                            htmlFor="email"
                            className="text-slate-700 font-medium"
                        >
                            Email
                        </Label>
                        <Input
                            id="email"
                            name="email"
                            type="email"
                            value={formData.email}
                            onChange={handleInputChange}
                            placeholder="Enter your email"
                            required
                            className="border-slate-300 focus:border-slate-500 focus:ring-slate-500"
                        />
                    </div>

                    <div className="space-y-2">
                        <Label
                            htmlFor="password"
                            className="text-slate-700 font-medium"
                        >
                            Password
                        </Label>
                        <Input
                            id="password"
                            name="password"
                            type="password"
                            value={formData.password}
                            onChange={handleInputChange}
                            placeholder="Enter your password"
                            required
                            className="border-slate-300 focus:border-slate-500 focus:ring-slate-500"
                        />
                    </div>

                    {!isLogin && (
                        <div className="space-y-2">
                            <Label
                                htmlFor="password_confirmation"
                                className="text-slate-700 font-medium"
                            >
                                Confirm Password
                            </Label>
                            <Input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                value={formData.password_confirmation}
                                onChange={handleInputChange}
                                placeholder="Confirm your password"
                                required={!isLogin}
                                className="border-slate-300 focus:border-slate-500 focus:ring-slate-500"
                            />
                        </div>
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
                        <Button
                            type="submit"
                            disabled={loading}
                            className="w-full bg-gradient-to-r from-slate-900 to-slate-700 hover:from-slate-800 hover:to-slate-600 text-white font-medium py-2 px-6 rounded-lg transition-all duration-200 hover:shadow-lg"
                        >
                            {loading
                                ? 'Processing...'
                                : isLogin
                                  ? 'Sign In'
                                  : 'Create Account'}
                        </Button>

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
            </DialogContent>
        </Dialog>
    );
}
