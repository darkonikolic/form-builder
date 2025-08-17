import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

export default function Welcome() {
    const [isOpen, setIsOpen] = useState(false);

    const handleOpenDialog = () => {
        setIsOpen(true);
    };

    const handleCloseDialog = () => {
        setIsOpen(false);
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center p-4">
            <Card className="w-full max-w-md shadow-xl border-0 bg-white/80 backdrop-blur-sm">
                <CardHeader className="text-center pb-4">
                    <CardTitle className="text-3xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                        Form Builder
                    </CardTitle>
                    <CardDescription className="text-slate-600 text-base">
                        Create beautiful forms with ease
                    </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                    <div className="text-center space-y-2">
                        <p className="text-sm text-slate-500">
                            Ready to build amazing forms?
                        </p>
                        <Dialog open={isOpen} onOpenChange={setIsOpen}>
                            <DialogTrigger asChild>
                                <Button
                                    onClick={handleOpenDialog}
                                    className="w-full bg-gradient-to-r from-slate-900 to-slate-700 hover:from-slate-800 hover:to-slate-600 text-white font-medium py-2 px-6 rounded-lg transition-all duration-200 hover:shadow-lg hover:scale-105"
                                >
                                    Get Started
                                </Button>
                            </DialogTrigger>
                            <DialogContent className="sm:max-w-md bg-white/95 backdrop-blur-sm border-0 shadow-2xl">
                                <DialogHeader>
                                    <DialogTitle className="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                                        Welcome to Form Builder! 🚀
                                    </DialogTitle>
                                    <DialogDescription className="text-slate-600 mt-2">
                                        Let's create something amazing together
                                    </DialogDescription>
                                </DialogHeader>
                                <div className="space-y-4 py-4">
                                    <div className="grid grid-cols-2 gap-4">
                                        <div className="text-center p-4 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200">
                                            <div className="text-2xl mb-2">
                                                📝
                                            </div>
                                            <h3 className="font-semibold text-blue-900">
                                                Create Forms
                                            </h3>
                                            <p className="text-sm text-blue-700">
                                                Build beautiful forms
                                            </p>
                                        </div>
                                        <div className="text-center p-4 rounded-lg bg-gradient-to-br from-green-50 to-green-100 border border-green-200">
                                            <div className="text-2xl mb-2">
                                                🎨
                                            </div>
                                            <h3 className="font-semibold text-green-900">
                                                Customize
                                            </h3>
                                            <p className="text-sm text-green-700">
                                                Tailwind + Shadcn
                                            </p>
                                        </div>
                                    </div>
                                    <div className="text-center p-4 rounded-lg bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200">
                                        <div className="text-2xl mb-2">⚡</div>
                                        <h3 className="font-semibold text-purple-900">
                                            Modern Tech Stack
                                        </h3>
                                        <p className="text-sm text-purple-700">
                                            Laravel + React + Tailwind
                                        </p>
                                    </div>
                                </div>
                                <div className="flex justify-end space-x-2">
                                    <Button
                                        variant="outline"
                                        onClick={handleCloseDialog}
                                        className="border-slate-300 text-slate-700 hover:bg-slate-50"
                                    >
                                        Close
                                    </Button>
                                    <Button
                                        onClick={handleCloseDialog}
                                        className="bg-gradient-to-r from-slate-900 to-slate-700 hover:from-slate-800 hover:to-slate-600"
                                    >
                                        Continue
                                    </Button>
                                </div>
                            </DialogContent>
                        </Dialog>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}
