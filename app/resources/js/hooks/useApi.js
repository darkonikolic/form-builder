export const useApi = () => {
    const apiCall = async (endpoint, options = {}) => {
        const headers = {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
            ...options.headers,
        };

        try {
            const response = await fetch(`/api${endpoint}`, {
                ...options,
                headers,
            });
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'API Error');
            }
            return await response.json();
        } catch (error) {
            throw new Error(error.message || 'Network error occurred');
        }
    };

    return { apiCall };
};
