import { ref, computed, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { sampleFoods, addOrder, getCustomFoods, decrementCustomFoodStock } from '@/Utils/mockFoods';
import { useAuth } from '@/Composables/useAuth';

export function useCheckout(props) {
    const auth = useAuth();

    const food = ref(props.food || (getCustomFoods()[0] || sampleFoods[0]));
    const quantity = ref(props.initialQuantity || 1);

    // State: 'idle' | 'loading' | 'success'
    const orderState = ref('idle');
    const generatedOrderId = ref('');
    const loadingProgress = ref(0);
    const isSubmitting = ref(false);

    // Form
    const userName = ref(props.customer?.name || auth.user.value?.name || '');
    const userAddress = ref(props.customer?.address || auth.user.value?.address || '');
    const userWhatsapp = ref(props.customer?.phone || auth.user.value?.phone || '');
    const userEmail = ref(props.customer?.email || auth.user.value?.email || '');
    const userNim = ref(props.customer?.nim || auth.user.value?.nim || '');
    const userNote = ref('');

    // Touched tracking for inline field errors (Modul 8 Target 2)
    const touched = ref({
        name: false,
        address: false,
        whatsapp: false,
        email: false,
    });

    // Inline Field Validation & Sanitization (.trim() checked)
    const nameError = computed(() => {
        const val = userName.value.trim();
        if (!val) return 'Nama lengkap wajib diisi.';
        if (val.length < 3) return 'Nama minimal 3 karakter.';
        return '';
    });

    const addressError = computed(() => {
        const val = userAddress.value.trim();
        if (!val) return 'Alamat kos/penjemputan wajib diisi.';
        return '';
    });

    const whatsappError = computed(() => {
        const raw = userWhatsapp.value.trim().replace(/[-\s]/g, '');
        if (!raw) return 'Nomor WhatsApp wajib diisi.';
        if (!/^\d+$/.test(raw)) return 'Nomor WhatsApp hanya boleh angka.';
        if (raw.length < 10 || raw.length > 15) return 'Nomor WhatsApp tidak valid (10-15 digit).';
        return '';
    });

    const emailError = computed(() => {
        const val = userEmail.value.trim();
        if (!val) return 'Alamat email wajib diisi.';
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(val)) return 'Format email tidak valid (contoh: nama@gmail.com).';
        return '';
    });

    // Overall Form Validity & Toast state
    const isFormValid = computed(() => {
        return !nameError.value && !addressError.value && !whatsappError.value && !emailError.value;
    });

    const showSuccessToast = ref(false);

    // Packaging
    const ecoPackaging = ref(props.initialPackaging || 'bawa_sendiri');

    // Payment (100% Cashless Sandbox Simulator untuk proteksi mitra dari order fiktif)
    const paymentMethod = ref('qris');

    // Pickup time & arrival
    const arrivalEstimates = [
        { id: 'awal', label: 'Awal Jendela', sub: 'Saat surplus siap' },
        { id: 'tengah', label: 'Pertengahan', sub: 'Fleksibel santai' },
        { id: 'akhir', label: 'Menjelang Tutup', sub: 'Sebelum batas akhir' }
    ];
    const selectedArrival = ref('tengah');
    const remindBefore = ref(true);

    const unitPrice = computed(() => {
        return food.value?.priceDiscount ?? food.value?.price_discount ?? 12000;
    });
    const originalPrice = computed(() => {
        return food.value?.priceOriginal ?? food.value?.price_original ?? food.value?.price ?? 25000;
    });
    const subtotal = computed(() => unitPrice.value * quantity.value);
    const packagingFee = computed(() => {
        if (ecoPackaging.value === 'kotak_kertas') return 1000;
        if (ecoPackaging.value === 'thinwall') return 2000;
        return 0;
    });
    const serviceFee = 1000;
    const totalPayment = computed(() => subtotal.value + packagingFee.value + serviceFee);
    const savedAmount = computed(() => (originalPrice.value - unitPrice.value) * quantity.value);
    const weightSaved = computed(() => quantity.value * (food.value?.weight_grams || 400));

    // Simpan & Kelola Draft Checkout (Mencegah pesanan hilang jika kepencet Home)
    const saveCheckoutDraft = () => {
        if (typeof window === 'undefined' || !food.value) return;
        try {
            const draft = {
                slug: food.value.slug,
                userId: auth.user.value?.id || null,
                userEmail: auth.user.value?.email || userEmail.value || null,
                foodId: food.value.id,
                foodName: food.value.name,
                foodImage: food.value.image,
                merchantName: food.value.merchantName || food.value.merchant?.name || food.value.merchant || 'Mitra Kampus',
                quantity: quantity.value,
                packaging: ecoPackaging.value,
                paymentMethod: paymentMethod.value,
                totalPayment: totalPayment.value,
                pickupTime: food.value.pickupTime || food.value.pickup_time_display || '19.30 – 20.30 WIB',
                address: food.value.address || 'Surabaya',
                timestamp: Date.now(),
            };
            localStorage.setItem('plusfood_draft_checkout', JSON.stringify(draft));
        } catch (e) {}
    };

    const clearCheckoutDraft = () => {
        if (typeof window === 'undefined') return;
        try {
            localStorage.removeItem('plusfood_draft_checkout');
        } catch (e) {}
    };

    onMounted(() => {
        if (typeof window !== 'undefined') {
            const allAvailableFoods = [...getCustomFoods(), ...sampleFoods];
            const params = new URLSearchParams(window.location.search);
            const slug = params.get('slug');
            const qty = parseInt(params.get('qty'), 10);
            if (slug) {
                const found = allAvailableFoods.find(f => f.slug === slug);
                if (found) food.value = found;
            } else {
                // Pulihkan dari draft lokal jika membuka checkout kembali
                const rawDraft = localStorage.getItem('plusfood_draft_checkout');
                if (rawDraft) {
                    try {
                        const draft = JSON.parse(rawDraft);
                        if (draft.slug) {
                            const found = allAvailableFoods.find(f => f.slug === draft.slug);
                            if (found) food.value = found;
                        }
                        if (draft.quantity) quantity.value = Math.min(draft.quantity, food.value?.stock || 10);
                        if (draft.packaging) ecoPackaging.value = draft.packaging;
                        if (draft.paymentMethod) paymentMethod.value = draft.paymentMethod;
                    } catch (e) {}
                }
            }
            if (qty && !isNaN(qty) && qty > 0 && food.value) {
                quantity.value = Math.min(qty, food.value.stock);
            }
        }
        saveCheckoutDraft();
    });

    watch([quantity, ecoPackaging, paymentMethod, food], () => {
        saveCheckoutDraft();
    });

    const paymentOptions = [
        { id: 'qris', label: 'QRIS Dinamis', sublabel: 'BCA, Mandiri, BRI & Semua E-Wallet', color: 'bg-rose-600', initial: 'QRIS' },
        { id: 'gopay', label: 'GoPay Sandbox', sublabel: 'Simulasi Pembayaran Instan 1-Klik', color: 'bg-sky-500', initial: 'GoPay' },
        { id: 'dana', label: 'DANA Sandbox', sublabel: 'Dompet Digital Mahasiswa Unesa', color: 'bg-blue-600', initial: 'DANA' },
    ];

    const fmt = (n) => (n || 0).toLocaleString('id-ID');

    let progressTimer = null;
    const confirmReservation = () => {
        // Sanitasi data form (.trim())
        userName.value = userName.value.trim();
        userAddress.value = userAddress.value.trim();
        userWhatsapp.value = userWhatsapp.value.trim();
        userEmail.value = userEmail.value.trim();

        // Tandai semua field sebagai disentuh
        touched.value = { name: true, address: true, whatsapp: true, email: true };

        if (!isFormValid.value || isSubmitting.value) return;

        isSubmitting.value = true;
        orderState.value = 'loading';
        loadingProgress.value = 10;

        let elapsed = 0;
        const duration = 1800;
        const tick = 40;
        progressTimer = setInterval(() => {
            elapsed += tick;
            loadingProgress.value = Math.min(94, Math.round(100 * (1 - Math.pow(1 - elapsed / duration, 2))));
        }, tick);

        // Kirim request ke backend Laravel Database
        const preGeneratedId = 'PF-' + Math.floor(10000 + Math.random() * 90000);
        generatedOrderId.value = preGeneratedId;
        const backendFoodId = (food.value.id && Number(food.value.id) <= 99) ? Number(food.value.id) : 1;
        router.post(route('checkout.store'), {
            food_id: backendFoodId,
            quantity: quantity.value,
            eco_packaging: ecoPackaging.value,
            customer_name: userName.value,
            customer_phone: userWhatsapp.value,
            customer_email: userEmail.value,
            customer_address: userAddress.value,
            customer_nim: userNim.value,
            notes: userNote.value.trim(),
            payment_method: paymentMethod.value,
            arrival_estimate: selectedArrival.value,
            remind_before: remindBefore.value,
            order_number: preGeneratedId,
        }, {
            onSuccess: (pageResp) => {
                clearInterval(progressTimer);
                loadingProgress.value = 100;
                isSubmitting.value = false;

                const newId = pageResp?.props?.flash?.order_id || preGeneratedId;
                generatedOrderId.value = newId;

                const cleanMerchantName = typeof food.value?.merchant === 'object' && food.value?.merchant?.name
                    ? food.value.merchant.name
                    : (food.value?.merchantName || (typeof food.value?.merchant === 'string' ? food.value.merchant : 'Mitra PlusFood'));

                addOrder({
                    id: newId,
                    userId: auth.user.value?.id || null,
                    user_id: auth.user.value?.id || null,
                    userEmail: auth.user.value?.email || userEmail.value || null,
                    customerName: userName.value,
                    nim: userNim.value,
                    whatsapp: userWhatsapp.value,
                    email: userEmail.value,
                    address: userAddress.value,
                    note: userNote.value.trim(),
                    food: {
                        ...food.value,
                        merchant: cleanMerchantName,
                        merchantName: cleanMerchantName,
                        shortName: food.value?.shortName || food.value?.name,
                    },
                    quantity: quantity.value,
                    ecoPackaging: ecoPackaging.value,
                    ecoPackagingLabel: ecoPackaging.value === 'bawa_sendiri' ? 'Bawa Wadah Sendiri' : ecoPackaging.value === 'kotak_kertas' ? 'Kotak Kertas' : 'Thinwall',
                    subtotal: subtotal.value,
                    packagingFee: packagingFee.value,
                    total: totalPayment.value,
                    pickupTime: food.value.pickupTime || food.value.pickup_time_display || '19.30 – 20.30 WIB',
                    pickupDeadline: food.value.pickupDeadlineInfo || food.value.pickup_deadline_info || 'Hari ini tutup 21.00 WIB',
                    date: 'Hari ini',
                    paymentMethod: paymentMethod.value,
                    paymentGateway: 'sandbox_simulator',
                    paymentReference: 'SBX-' + paymentMethod.value.toUpperCase() + '-' + newId,
                    paymentStatus: 'lunas',
                    paidAt: new Date().toISOString(),
                    created_at: new Date().toISOString(),
                    createdAt: new Date().toISOString(),
                    status: 'menunggu',
                    statusLabel: 'Menunggu Pickup',
                    qrCode: newId + '-' + (food.value.merchantInitial || food.value.merchant?.initial || 'PF'),
                    mapsUrl: 'https://maps.google.com/?q=-7.3115,112.7275',
                    hasReview: false,
                });

                // Kurangi stok di localStorage custom foods secara realtime
                if (food.value) {
                    decrementCustomFoodStock(food.value.id, quantity.value);
                    if (food.value.slug) decrementCustomFoodStock(food.value.slug, quantity.value);
                    if (food.value.name) decrementCustomFoodStock(food.value.name, quantity.value);
                }

                // Hapus draft checkout karena pesanan sudah resmi masuk
                clearCheckoutDraft();

                showSuccessToast.value = true;
                setTimeout(() => { orderState.value = 'success'; }, 300);
                setTimeout(() => { showSuccessToast.value = false; }, 4000);
            },
            onError: (errors) => {
                // Fallback graceful untuk demo offline / custom foods
                clearInterval(progressTimer);
                loadingProgress.value = 100;
                isSubmitting.value = false;

                const newId = 'PF-' + Math.floor(10000 + Math.random() * 90000);
                generatedOrderId.value = newId;

                const cleanMerchantName = typeof food.value?.merchant === 'object' && food.value?.merchant?.name
                    ? food.value.merchant.name
                    : (food.value?.merchantName || (typeof food.value?.merchant === 'string' ? food.value.merchant : 'Mitra PlusFood'));

                addOrder({
                    id: newId,
                    userId: auth.user.value?.id || null,
                    user_id: auth.user.value?.id || null,
                    userEmail: auth.user.value?.email || userEmail.value || null,
                    customerName: userName.value,
                    nim: userNim.value,
                    whatsapp: userWhatsapp.value,
                    email: userEmail.value,
                    address: userAddress.value,
                    note: userNote.value.trim(),
                    food: {
                        ...food.value,
                        merchant: cleanMerchantName,
                        merchantName: cleanMerchantName,
                        shortName: food.value?.shortName || food.value?.name,
                    },
                    quantity: quantity.value,
                    ecoPackaging: ecoPackaging.value,
                    ecoPackagingLabel: ecoPackaging.value === 'bawa_sendiri' ? 'Bawa Wadah Sendiri' : ecoPackaging.value === 'kotak_kertas' ? 'Kotak Kertas' : 'Thinwall',
                    subtotal: subtotal.value,
                    packagingFee: packagingFee.value,
                    total: totalPayment.value,
                    pickupTime: food.value.pickupTime || food.value.pickup_time_display || '19.30 – 20.30 WIB',
                    pickupDeadline: food.value.pickupDeadlineInfo || food.value.pickup_deadline_info || 'Hari ini tutup 21.00 WIB',
                    date: 'Hari ini',
                    paymentMethod: paymentMethod.value,
                    paymentGateway: 'sandbox_simulator',
                    paymentReference: 'SBX-' + paymentMethod.value.toUpperCase() + '-' + newId,
                    paymentStatus: 'lunas',
                    paidAt: new Date().toISOString(),
                    created_at: new Date().toISOString(),
                    createdAt: new Date().toISOString(),
                    status: 'menunggu',
                    statusLabel: 'Menunggu Pickup',
                    qrCode: newId + '-' + (food.value.merchantInitial || food.value.merchant?.initial || 'PF'),
                    mapsUrl: 'https://maps.google.com/?q=-7.3115,112.7275',
                    hasReview: false,
                });

                if (food.value) {
                    decrementCustomFoodStock(food.value.id, quantity.value);
                    if (food.value.slug) decrementCustomFoodStock(food.value.slug, quantity.value);
                    if (food.value.name) decrementCustomFoodStock(food.value.name, quantity.value);
                }

                clearCheckoutDraft();
                showSuccessToast.value = true;
                setTimeout(() => { orderState.value = 'success'; }, 300);
                setTimeout(() => { showSuccessToast.value = false; }, 4000);
            }
        });
    };

    return {
        auth,
        food,
        quantity,
        orderState,
        generatedOrderId,
        loadingProgress,
        isSubmitting,
        userName,
        userAddress,
        userWhatsapp,
        userEmail,
        userNim,
        userNote,
        touched,
        nameError,
        addressError,
        whatsappError,
        emailError,
        isFormValid,
        showSuccessToast,
        ecoPackaging,
        paymentMethod,
        arrivalEstimates,
        selectedArrival,
        remindBefore,
        unitPrice,
        originalPrice,
        subtotal,
        packagingFee,
        serviceFee,
        totalPayment,
        savedAmount,
        weightSaved,
        saveCheckoutDraft,
        clearCheckoutDraft,
        paymentOptions,
        fmt,
        confirmReservation,
    };
}
