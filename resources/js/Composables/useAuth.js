import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

export function useAuth() {
    const page = usePage();
    const serverUser = computed(() => page?.props?.auth?.user || null);

    const isLoggedIn = computed(() => !!serverUser.value);

    const user = computed(() => {
        if (serverUser.value) {
            return {
                id: serverUser.value.id,
                name: serverUser.value.name,
                email: serverUser.value.email,
                phone: serverUser.value.phone || '',
                whatsapp: serverUser.value.phone || '081234567890',
                nim: serverUser.value.nim || '23051204117',
                campus: serverUser.value.campus || 'Universitas Negeri Surabaya',
                major: serverUser.value.major || 'S1 Teknik Informatika · 2023',
                address: serverUser.value.address || 'Jl. Ketintang Madya VI No. 21',
                avatar: serverUser.value.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
                co2_saved_grams: serverUser.value.co2_saved_grams ?? 0,
                money_saved_rp: serverUser.value.money_saved_rp ?? 0,
                portions_rescued: serverUser.value.portions_rescued ?? 0,
                rescuer_level: serverUser.value.rescuer_level ?? 1,
                preference: serverUser.value.preference || null,
            };
        }
        return {
            name: 'Raka Pratama',
            email: 'raka@mhs.unesa.ac.id',
            nim: '23051204117',
            whatsapp: '081234567890',
            phone: '081234567890',
            campus: 'Universitas Negeri Surabaya',
            major: 'S1 Teknik Informatika · 2023',
            address: 'Jl. Ketintang Madya VI No. 21',
            avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
            co2_saved_grams: 3400,
            money_saved_rp: 85000,
            portions_rescued: 5,
            rescuer_level: 2,
        };
    });

    const logout = () => {
        router.post(route('logout'));
    };

    return {
        user,
        isLoggedIn,
        logout,
    };
}
