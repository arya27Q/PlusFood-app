import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useAuth } from '@/Composables/useAuth';
import { campusZones, getSavedOrders } from '@/Utils/mockFoods';

export function useMahasiswaProfil(props) {
    const auth = useAuth();

    // State Data Diri (disinkronkan dengan database user atau auth fallback)
    const isEditing = ref(false);
    const saveToast = ref(false);
    const saveToastMessage = ref('Data profil berhasil diperbarui!');

    const activeUser = computed(() => props.user || auth.user.value || {});

    const profileForm = useForm({
        name: activeUser.value?.name || '',
        nim: activeUser.value?.nim || '',
        phone: activeUser.value?.phone || activeUser.value?.whatsapp || '',
        whatsapp: activeUser.value?.phone || activeUser.value?.whatsapp || '',
        email: activeUser.value?.email || '',
        address: activeUser.value?.address || '',
        major: activeUser.value?.major || '',
        campus: activeUser.value?.campus || '',
    });

    // Lokasi Kampus & Radius Acuan (Sinkron dengan model database user_preferences & mockFoods)
    const availableCampuses = computed(() => campusZones.filter(c => !c.isComingSoon && c.id !== 'all'));
    const selectedCampus = ref(props.preference?.campus_zone || 'unesa-ketintang');
    const selectedRadius = ref(props.preference?.radius || '2 km');
    const radiuses = ['500 m', '1 km', '2 km', '3 km', '5 km'];

    // Preferensi Diet & Pembayaran
    const dietaryTags = ref([
        { id: 'halal', label: 'Halal', selected: props.preference?.prefer_halal ?? true },
        { id: 'vegetarian', label: 'Vegetarian', selected: props.preference?.prefer_vegetarian ?? false },
        { id: 'tidak_pedas', label: 'Tidak pedas', selected: props.preference?.prefer_not_spicy ?? true },
        { id: 'tanpa_kacang', label: 'Tanpa kacang', selected: props.preference?.prefer_nut_free ?? false },
        { id: 'tanpa_seafood', label: 'Tanpa seafood', selected: false },
    ]);

    const toggleDietary = (tag) => {
        tag.selected = !tag.selected;
        savePreferences();
    };

    const selectedPayment = ref('QRIS'); // 'QRIS' | 'Tunai'

    // Warung Favorit Dinamis (Tersinkronisasi Pesanan Selesai Mahasiswa & Database MySQL)
    // Warung Favorit Dinamis (Tersinkronisasi Pesanan Selesai Mahasiswa & Database MySQL)
    const favoriteStores = computed(() => {
        if (props.favoriteStores && props.favoriteStores.length > 0) {
            return props.favoriteStores;
        }

        if (typeof window !== 'undefined') {
            const localOrders = getSavedOrders(activeUser.value?.id, activeUser.value?.email).filter(o => o.status === 'selesai');
            if (localOrders.length > 0) {
                const map = {};
                localOrders.forEach(o => {
                    const mName = o.food?.merchant || o.food?.merchantName || 'Warung Mitra';
                    map[mName] = (map[mName] || 0) + (o.quantity || 1);
                });
                return Object.entries(map).map(([name, count]) => ({
                    name,
                    count,
                    rescuedCount: `${count}x diselamatkan`,
                    image: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=200&q=80',
                }));
            }
        }

        return [];
    });

    // Keamanan Akun & Modal Ganti Password
    const lastPasswordChange = ref('Baru saja disinkronkan');
    const showPasswordModal = ref(false);
    const showOldPassword = ref(false);
    const showNewPassword = ref(false);
    const showConfirmPassword = ref(false);
    const passwordError = ref('');
    const passwordSuccessToast = ref(false);

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const openPasswordModal = () => {
        passwordForm.reset();
        passwordError.value = '';
        showOldPassword.value = false;
        showNewPassword.value = false;
        showConfirmPassword.value = false;
        showPasswordModal.value = true;
    };

    const handlePasswordSubmit = () => {
        passwordError.value = '';
        passwordForm.put(route('profil.password'), {
            preserveScroll: true,
            onSuccess: () => {
                showPasswordModal.value = false;
                passwordForm.reset();
                passwordSuccessToast.value = true;
                lastPasswordChange.value = 'Baru saja diubah';
                setTimeout(() => {
                    passwordSuccessToast.value = false;
                }, 3500);
            },
            onError: (errs) => {
                passwordError.value = errs.current_password || errs.password || errs.password_confirmation || 'Gagal mengubah password. Cek kembali password lama Anda.';
            },
        });
    };

    // Simpan Data Diri ke Backend MySQL
    const saveProfile = () => {
        profileForm.phone = profileForm.phone || profileForm.whatsapp;
        profileForm.put(route('profil.update'), {
            preserveScroll: true,
            onSuccess: () => {
                isEditing.value = false;
                saveToastMessage.value = 'Data profil berhasil diperbarui ke database!';
                saveToast.value = true;
                setTimeout(() => {
                    saveToast.value = false;
                }, 3000);
            },
            onError: () => {
                saveToastMessage.value = 'Gagal menyimpan profil. Mohon periksa inputan.';
                saveToast.value = true;
                setTimeout(() => {
                    saveToast.value = false;
                }, 3000);
            },
        });
    };

    // Simpan Preferensi ke Backend MySQL (tabel user_preferences)
    const savePreferences = () => {
        router.post(route('profil.preferences'), {
            campus_zone: selectedCampus.value,
            radius: selectedRadius.value,
            prefer_halal: dietaryTags.value.find(t => t.id === 'halal')?.selected ?? true,
            prefer_vegetarian: dietaryTags.value.find(t => t.id === 'vegetarian')?.selected ?? false,
            prefer_not_spicy: dietaryTags.value.find(t => t.id === 'tidak_pedas')?.selected ?? true,
            prefer_nut_free: dietaryTags.value.find(t => t.id === 'tanpa_kacang')?.selected ?? false,
        }, {
            preserveScroll: true,
            onSuccess: () => {
                saveToastMessage.value = 'Preferensi makanan berhasil disimpan!';
                saveToast.value = true;
                setTimeout(() => {
                    saveToast.value = false;
                }, 3000);
            },
        });
    };

    // Metrik Dampak & Level Gamifikasi dari Data User Database + Sinkronisasi Aktivitas Penyelamatan
    const userStats = computed(() => {
        const u = activeUser.value;
        let localRescued = 0;
        let reviewedCount = 0;

        if (typeof window !== 'undefined') {
            const localCompleted = getSavedOrders(activeUser.value?.id, activeUser.value?.email).filter(o => o.status === 'selesai');
            localRescued = localCompleted.reduce((acc, curr) => acc + (curr.quantity || 1), 0);
            reviewedCount = localCompleted.filter(o => o.isReviewed || o.hasReview).length;
        }

        const basePortions = u.portions_rescued !== undefined && u.portions_rescued !== null ? Number(u.portions_rescued) : 0;
        const portions = basePortions + localRescued;

        const baseMoney = u.money_saved_rp !== undefined && u.money_saved_rp !== null ? Number(u.money_saved_rp) : 0;
        const money = baseMoney + (localRescued * 12000);

        const baseCo2 = u.co2_saved_grams !== undefined && u.co2_saved_grams !== null ? Number(u.co2_saved_grams) : 0;
        const co2 = baseCo2 + (localRescued * 420);

        const level = u.rescuer_level ?? 1;
        // Poin bertambah dari porsi yang diselamatkan + reward ulasan (+15 poin)
        const points = (portions * 68) + (reviewedCount * 15);
        const nextLevelPoints = Math.max(250, level * 250);
        const pointsRemaining = Math.max(0, nextLevelPoints - points);
        const progressPercent = Math.min(100, Math.round((points / nextLevelPoints) * 100));

        let formattedMoney = 'Rp' + Math.round(money / 1000) + 'rb';
        if (money >= 1000000) {
            formattedMoney = 'Rp' + (money / 1000000).toFixed(1) + 'jt';
        }

        const formattedCo2 = (co2 / 1000).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + ' kg';

        return {
            level,
            portions,
            moneySaved: formattedMoney,
            co2Saved: formattedCo2,
            points,
            nextLevelPoints,
            pointsRemaining,
            progressPercent: progressPercent || 100,
        };
    });

    return {
        auth,
        isEditing,
        saveToast,
        saveToastMessage,
        activeUser,
        profileForm,
        availableCampuses,
        selectedCampus,
        selectedRadius,
        radiuses,
        dietaryTags,
        toggleDietary,
        selectedPayment,
        favoriteStores,
        lastPasswordChange,
        showPasswordModal,
        showOldPassword,
        showNewPassword,
        showConfirmPassword,
        passwordError,
        passwordSuccessToast,
        passwordForm,
        openPasswordModal,
        handlePasswordSubmit,
        saveProfile,
        savePreferences,
        userStats,
    };
}
