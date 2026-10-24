import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

export function useAuth() {
    const page = usePage();
    const serverUser = computed(() => page?.props?.auth?.user || null);

    // Akun Mahasiswa murni dari session guard web dengan role mahasiswa
    const isMahasiswa = computed(() => !!serverUser.value && serverUser.value?.role === 'mahasiswa');
    const isLoggedIn = computed(() => isMahasiswa.value);
    const isMitra = computed(() => !!(page?.props?.auth?.mitra || page?.props?.auth?.isMitra));

    const user = computed(() => {
        if (serverUser.value && isMahasiswa.value) {
            return {
                id: serverUser.value.id,
                name: serverUser.value.name,
                email: serverUser.value.email,
                role: 'mahasiswa',
                phone: serverUser.value.phone || '',
                whatsapp: serverUser.value.phone || '',
                nim: serverUser.value.nim || '-',
                campus: serverUser.value.campus || 'Universitas Negeri Surabaya',
                major: serverUser.value.major || 'Mahasiswa UNESA',
                address: serverUser.value.address || '',
                avatar: serverUser.value.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
                co2_saved_grams: serverUser.value.co2_saved_grams ?? 0,
                money_saved_rp: serverUser.value.money_saved_rp ?? 0,
                portions_rescued: serverUser.value.portions_rescued ?? 0,
                rescuer_level: serverUser.value.rescuer_level ?? 1,
                preference: serverUser.value.preference || null,
            };
        }
        return null;
    });

    const logout = () => {
        if (typeof window !== 'undefined') {
            try {
                localStorage.removeItem('plusfood_draft_checkout');
                localStorage.removeItem('plusfood_orders');
                localStorage.removeItem('plusfood_orders_cleared');
            } catch (e) {}
        }
        router.post(route('logout'));
    };

    return {
        user,
        serverUser,
        isLoggedIn,
        isMahasiswa,
        isMitra,
        logout,
    };
}
