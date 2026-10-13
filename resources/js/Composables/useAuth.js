import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

// Default mock user
const defaultUser = {
    name: 'Raka Pratama',
    email: 'raka@mhs.unesa.ac.id',
    nim: '23051204088',
    whatsapp: '081234567890',
};

// Persistent reactive state via localStorage
const storedUser = typeof window !== 'undefined' ? localStorage.getItem('plusfood_mock_user') : null;
const currentUser = ref(storedUser ? JSON.parse(storedUser) : defaultUser);

export function useAuth() {
    const isLoggedIn = computed(() => !!currentUser.value);

    const login = (email = '', password = '') => {
        // Ambil nama dari email jika ada, atau gunakan default
        let name = 'Raka Pratama';
        if (email && email.includes('@')) {
            const prefix = email.split('@')[0];
            name = prefix.charAt(0).toUpperCase() + prefix.slice(1);
        }

        const userObj = {
            name: name,
            email: email || 'raka@mhs.unesa.ac.id',
            nim: '23051204088',
            whatsapp: '081234567890',
        };

        currentUser.value = userObj;
        if (typeof window !== 'undefined') {
            localStorage.setItem('plusfood_mock_user', JSON.stringify(userObj));
        }

        router.visit(route('beranda'));
    };

    const register = (userData = {}) => {
        const userObj = {
            name: userData.name || 'Raka Pratama',
            email: userData.email || 'raka@mhs.unesa.ac.id',
            nim: userData.nim || '23051204088',
            whatsapp: userData.whatsapp || '081234567890',
        };

        currentUser.value = userObj;
        if (typeof window !== 'undefined') {
            localStorage.setItem('plusfood_mock_user', JSON.stringify(userObj));
        }

        router.visit(route('beranda'));
    };

    const logout = () => {
        currentUser.value = null;
        if (typeof window !== 'undefined') {
            localStorage.removeItem('plusfood_mock_user');
        }
        router.visit(route('login'));
    };

    return {
        user: currentUser,
        isLoggedIn,
        login,
        register,
        logout,
    };
}
