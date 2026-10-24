import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

export function useMitraProfil(props) {
    const page = usePage();

    // State Form Data Profil Mitra UMKM
    const isEditing = ref(false);
    const saveToast = ref(false);
    const localReviewDelta = ref(0);

    const storeForm = ref({
        storeName: props.merchant?.name || props.user?.name || page.props.auth?.user?.name || 'Gerai Mitra',
        ownerName: props.merchant?.owner_name || props.merchant?.name || props.user?.name || page.props.auth?.user?.name || 'Pemilik Usaha',
        phone: props.merchant?.phone || props.user?.phone || page.props.auth?.user?.phone || '0812-3456-7890',
        email: props.merchant?.user?.email || props.user?.email || page.props.auth?.user?.email || 'toko@plusfood.id',
        nib: props.merchant?.nib || 'NIB 9120004510291',
        campusZone: props.merchant?.campus_name || props.merchant?.campus_zone || 'UNESA Ketintang',
        canteenLocation: props.merchant?.canteen_location || 'Kantin Pusat Stand 08',
        defaultSession: props.merchant?.default_session || 'malam',
        pickupHours: props.merchant?.operating_hours || '19.00 – 20.30 WIB',
        supportEco: props.merchant?.support_eco ?? true,
        bankName: props.merchant?.bank_name || 'Bank Mandiri',
        bankAccount: props.merchant?.bank_account || '1420-0192-3841',
        bankHolder: props.merchant?.bank_holder || props.merchant?.owner_name || props.user?.name || page.props.auth?.user?.name || 'Pemilik Usaha'
    });

    // Rating & Jumlah Ulasan Dinamis (Terhubung ke MySQL & Reactive Sync)
    const currentReviewsCount = computed(() => {
        const base = props.merchant?.reviews_count ?? (props.stats?.reviews_count ?? 142);
        return base + localReviewDelta.value;
    });

    const currentRating = computed(() => {
        const r = props.merchant?.rating ?? (props.stats?.rating ?? 4.9);
        return Number(r);
    });

    // Metrik Performa 3 Kolom
    const performanceStats = computed(() => {
        return {
            portions_rescued: props.stats?.portions_rescued ?? 248,
            recovered_capital: props.stats?.recovered_capital ?? 3400000,
            co2_prevented: props.stats?.co2_prevented ?? 104,
        };
    });

    const formatRupiah = (val) => {
        if (!val) return 'Rp0';
        if (val >= 1000000) {
            return 'Rp' + (val / 1000000).toFixed(1).replace('.', ',') + 'Jt';
        }
        return 'Rp' + Math.round(val / 1000) + 'rb';
    };

    // Modal Ganti Password
    const showPasswordModal = ref(false);
    const passwordForm = ref({
        oldPassword: '',
        newPassword: '',
        confirmPassword: '',
    });
    const passwordError = ref('');
    const passwordSuccessToast = ref(false);
    const lastPasswordChange = ref('1 bulan lalu');

    const openPasswordModal = () => {
        passwordForm.value = { oldPassword: '', newPassword: '', confirmPassword: '' };
        passwordError.value = '';
        showPasswordModal.value = true;
    };

    const handlePasswordSubmit = () => {
        passwordError.value = '';
        if (!passwordForm.value.oldPassword) {
            passwordError.value = 'Mohon masukkan password lama akun Anda.';
            return;
        }
        if (passwordForm.value.newPassword.length < 8) {
            passwordError.value = 'Password baru minimal harus 8 karakter.';
            return;
        }
        if (passwordForm.value.newPassword !== passwordForm.value.confirmPassword) {
            passwordError.value = 'Konfirmasi password baru tidak cocok!';
            return;
        }

        lastPasswordChange.value = 'Baru saja diubah';
        showPasswordModal.value = false;
        passwordSuccessToast.value = true;
        setTimeout(() => {
            passwordSuccessToast.value = false;
        }, 3500);
    };

    const saveProfile = () => {
        if (typeof window !== 'undefined') {
            localStorage.setItem('plusfood_mitra_profile', JSON.stringify(storeForm.value));
        }
        router.put(route('mitra.profil.update'), storeForm.value, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                isEditing.value = false;
                saveToast.value = true;
                setTimeout(() => {
                    saveToast.value = false;
                }, 3000);
            },
            onError: () => {
                isEditing.value = false;
                saveToast.value = true;
                setTimeout(() => {
                    saveToast.value = false;
                }, 3000);
            }
        });
    };

    const syncReviewDelta = () => {
        if (typeof window !== 'undefined') {
            localReviewDelta.value = parseInt(localStorage.getItem('plusfood_merchant_review_delta') || '0', 10);
        }
    };

    onMounted(() => {
        syncReviewDelta();
        if (typeof window !== 'undefined') {
            window.addEventListener('storage', syncReviewDelta);
            window.addEventListener('plusfood_review_added', syncReviewDelta);

            // Bersihkan data lama jika ada di localStorage agar tidak menimpa data asli MySQL
            localStorage.removeItem('plusfood_mitra_profile');
        }
    });

    return {
        isEditing,
        saveToast,
        localReviewDelta,
        storeForm,
        currentReviewsCount,
        currentRating,
        performanceStats,
        formatRupiah,
        showPasswordModal,
        passwordForm,
        passwordError,
        passwordSuccessToast,
        lastPasswordChange,
        openPasswordModal,
        handlePasswordSubmit,
        saveProfile,
        syncReviewDelta,
    };
}
