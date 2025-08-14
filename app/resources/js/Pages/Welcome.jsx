export default function Welcome() {
    const handleTestClick = () => {
        alert('Test Button je kliknut! 🎉');
        console.log('Test Button clicked');
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center">
            <div className="text-center">
                <h1 className="text-6xl font-bold text-indigo-600 mb-6">
                    Form Builder - Welcome 🚀
                </h1>
                <p className="text-xl text-gray-600 mb-8">
                    This is a test component - it should work now!
                </p>
                <div className="space-y-4">
                    <button 
                        onClick={handleTestClick}
                        className="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg text-lg font-medium transition-colors"
                    >
                        Test Button
                    </button>
                </div>
            </div>
        </div>
    );
}
