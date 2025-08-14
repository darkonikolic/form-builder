import PrimaryButton from '@/Components/PrimaryButton';

export default function Welcome() {
    const handleClick = () => {
        alert('Hello from Form Builder!');
    };

    return (
        <div className="min-h-screen bg-gray-100 flex items-center justify-center">
            <div className="text-center">
                <h1 className="text-4xl font-bold text-gray-900 mb-6">
                    Welcome to Form Builder
                </h1>
                <p className="text-lg text-gray-600 mb-8">
                    Simple and clean React application
                </p>
                <PrimaryButton onClick={handleClick}>Click Me!</PrimaryButton>
            </div>
        </div>
    );
}
