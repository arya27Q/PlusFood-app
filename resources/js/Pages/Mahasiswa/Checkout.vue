<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MahasiswaLayout from '@/Layouts/MahasiswaLayout.vue';
import { sampleFoods, addOrder } from '@/Utils/mockFoods';
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '@/Composables/useAuth';

const auth = useAuth();

const food = ref(sampleFoods[0]);
const quantity = ref(1);

// State: 'idle' | 'loading' | 'success'
const orderState = ref('idle');
const generatedOrderId = ref('');
const loadingProgress = ref(0);

// Form
const userName = ref(auth.user.value?.name || 'Raka Pratama');
const userAddress = ref('');
const userWhatsapp = ref(auth.user.value?.whatsapp || '0812 3456 7890');
const userEmail = ref(auth.user.value?.email || 'raka.pratama@mhs.unesa.ac.id');
const userNim = ref(auth.user.value?.nim || '23051204088');
const userNote = ref('');

// Packaging
const ecoPackaging = ref('bawa_sendiri');

// Payment
const paymentMethod = ref('kasir');

// Pickup time & arrival
const arrivalEstimates = [
    { id: 'awal', label: 'Awal Jendela', sub: 'Saat surplus siap' },
    { id: 'tengah', label: 'Pertengahan', sub: 'Fleksibel santai' },
    { id: 'akhir', label: 'Menjelang Tutup', sub: 'Sebelum batas akhir' }
];
const selectedArrival = ref('tengah');
const remindBefore = ref(true);

onMounted(() => {
    if (typeof window !== 'undefined') {
        const params = new URLSearchParams(window.location.search);
        const slug = params.get('slug');
        const qty = parseInt(params.get('qty'), 10);
        if (slug) {
            const found = sampleFoods.find(f => f.slug === slug);
            if (found) food.value = found;
        }
        if (qty && !isNaN(qty) && qty > 0) {
            quantity.value = Math.min(qty, food.value.stock);
        }
    }
});

const subtotal = computed(() => food.value.priceDiscount * quantity.value);
const packagingFee = computed(() => {
    if (ecoPackaging.value === 'kotak_kertas') return 1000;
    if (ecoPackaging.value === 'thinwall') return 2000;
    return 0;
});
const serviceFee = 1000;
const totalPayment = computed(() => subtotal.value + packagingFee.value + serviceFee);
const savedAmount = computed(() => (food.value.price - food.value.priceDiscount) * quantity.value);
const weightSaved = computed(() => quantity.value * 400);

const isFormValid = computed(() => userName.value.trim() !== '' && userWhatsapp.value.trim() !== '');

const paymentOptions = [
    { id: 'qris', label: 'QRIS', sublabel: 'Scan & bayar', color: 'bg-blue-600', initial: 'GR' },
    { id: 'gopay', label: 'GoPay', sublabel: 'SaldoGoPay', color: 'bg-emerald-500', initial: 'GP' },
    { id: 'dana', label: 'DANA', sublabel: 'Saldo DANA', color: 'bg-blue-500', initial: 'DA' },
    { id: 'kasir', label: 'Bayar di Kasir', sublabel: 'Tunai atau QRIS', color: 'bg-brand-maroon', initial: 'Rp' },
];

const fmt = (n) => n.toLocaleString('id-ID');

let progressTimer = null;
const confirmReservation = () => {
    if (!isFormValid.value) return;
    orderState.value = 'loading';
    loadingProgress.value = 0;
    let elapsed = 0;
    const duration = 2200;
    const tick = 40;
    progressTimer = setInterval(() => {
        elapsed += tick;
        loadingProgress.value = Math.min(100, Math.round(100 * (1 - Math.pow(1 - elapsed / duration, 2))));
        if (elapsed >= duration) {
            clearInterval(progressTimer);
            const newId = 'PF-' + Math.floor(10000 + Math.random() * 90000);
            generatedOrderId.value = newId;
            addOrder({
                id: newId,
                customerName: userName.value,
                nim: userNim.value,
                whatsapp: userWhatsapp.value,
                email: userEmail.value,
                food: food.value,
                quantity: quantity.value,
                ecoPackaging: ecoPackaging.value,
                ecoPackagingLabel: ecoPackaging.value === 'bawa_sendiri' ? 'Bawa Wadah Sendiri' : ecoPackaging.value === 'kotak_kertas' ? 'Kotak Kertas' : 'Thinwall',
                subtotal: subtotal.value,
                packagingFee: packagingFee.value,
                total: totalPayment.value,
                pickupTime: food.value.pickupTime || '19.30 – 20.30 WIB',
                pickupDeadline: food.value.pickupDeadlineInfo || 'Hari ini tutup 21.00 WIB',
                date: 'Hari ini, 13 Okt 2026',
                status: 'menunggu',
                statusLabel: 'Menunggu Pickup',
                qrCode: newId + '-' + (food.value.merchantInitial || 'PF'),
                mapsUrl: 'https://maps.google.com/?q=-7.3115,112.7275',
                hasReview: false,
            });
            setTimeout(() => { orderState.value = 'success'; }, 300);
        }
    }, tick);
};
</script>

<template>
    <MahasiswaLayout>
        <Head title="Checkout - PlusFood" />

        <!-- ===== SUCCESS PAGE ===== -->
        <div v-if="orderState === 'success'" class="min-h-[72vh] flex flex-col items-center justify-center text-center px-4 py-16 animate-fade-in">
            <!-- Success icon: dark green solid circle + white check -->
            <div class="relative mb-6">
                <!-- Soft glow ring -->
                <div class="absolute inset-0 rounded-full bg-emerald-100 scale-150 animate-ping-slow opacity-40"></div>
                <div class="relative h-20 w-20 rounded-full bg-[#1B5E3B] flex items-center justify-center mx-auto shadow-lg">
                    <svg class="h-10 w-10 text-white animate-check-draw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-xs font-bold text-[#8C7A72] uppercase tracking-widest mb-1">Reservasi Berhasil · {{ generatedOrderId }}</p>
            <h1 class="text-3xl font-black text-brand-maroon mb-3">Pickup Berhasil!</h1>
            <p class="text-sm text-[#6E5A54] max-w-xs leading-relaxed">
                QR terverifikasi oleh <strong>{{ food.merchant }}</strong>. Selamat menikmati {{ food.name }}-mu, {{ userName.split(' ')[0] }}!
            </p>
            <div class="grid grid-cols-2 gap-4 mt-8 w-full max-w-xs">
                <div class="rounded-2xl border border-[#EAE0D5] bg-white p-4 text-center shadow-sm">
                    <span class="text-2xl mb-1 block">🌿</span>
                    <p class="text-xl font-black text-[#2D1B1E]">{{ weightSaved }} g</p>
                    <p class="text-[11px] text-[#8C7A72] mt-0.5">makanan diselamatkan</p>
                </div>
                <div class="rounded-2xl border border-[#EAE0D5] bg-white p-4 text-center shadow-sm">
                    <span class="text-2xl mb-1 block">💰</span>
                    <p class="text-xl font-black text-[#2D1B1E]">Rp{{ fmt(savedAmount) }}</p>
                    <p class="text-[11px] text-[#8C7A72] mt-0.5">berhasil dihemat</p>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-8 w-full max-w-xs">
                <Link :href="route('riwayat')" class="flex-1 rounded-xl bg-brand-maroon py-3 text-sm font-bold text-white text-center shadow-md hover:bg-brand-maroon-hover transition active:scale-95">
                    Beri Ulasan →
                </Link>
                <Link :href="route('beranda')" class="flex-1 rounded-xl border-2 border-brand-maroon py-3 text-sm font-bold text-brand-maroon text-center hover:bg-[#FAF7F2] transition">
                    Ke Beranda
                </Link>
            </div>
        </div>

        <!-- ===== LOADING OVERLAY ===== -->
        <Transition name="fade-blur">
            <div v-if="orderState === 'loading'" class="fixed z-[9999] flex items-center justify-center bg-[#FAF7F2]/95 backdrop-blur-sm" style="top:0;left:0;right:0;bottom:0;height:100vh;width:100vw">
                <div class="w-full max-w-sm mx-4 rounded-[24px] bg-white border border-[#EAE0D5] shadow-xl p-8 text-center space-y-5">
                    <!-- Circular spinner ring -->
                    <div class="relative mx-auto h-16 w-16">
                        <svg class="absolute inset-0 w-full h-full" viewBox="0 0 64 64" fill="none">
                            <circle cx="32" cy="32" r="28" stroke="#EAE0D5" stroke-width="3"/>
                        </svg>
                        <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 64 64" fill="none">
                            <circle
                                cx="32" cy="32" r="28"
                                stroke="#6D2B3D" stroke-width="3"
                                stroke-linecap="round"
                                :stroke-dasharray="175.93"
                                :stroke-dashoffset="175.93 * (1 - loadingProgress / 100)"
                                style="transition: stroke-dashoffset 0.08s linear"
                            />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="h-7 w-7 text-brand-maroon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 2a1 1 0 00-.993.883L5 3v1H4a2 2 0 00-1.995 1.85L2 6v13a2 2 0 001.85 1.995L4 21h16a2 2 0 001.995-1.85L22 19V6a2 2 0 00-1.85-1.995L20 4h-1V3a1 1 0 00-1.993-.117L17 3v1H7V3a1 1 0 00-1-1zm6 5a3 3 0 013 3 1 1 0 01-2 0 1 1 0 00-2 0 1 1 0 01-2 0 3 3 0 013-3z"/>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <p class="text-base font-extrabold text-brand-maroon">Menuju Pembayaran...</p>
                        <p class="text-xs text-[#8C7A72] mt-1">Mohon tunggu, jangan tutup halaman ini.</p>
                    </div>

                    <!-- Total row -->
                    <div class="rounded-xl bg-[#FAF7F2] border border-[#EAE0D5] px-4 py-3 flex items-center justify-between">
                        <span class="text-xs text-[#8C7A72] font-medium">Total bayar</span>
                        <span class="text-sm font-black text-brand-maroon">Rp{{ fmt(totalPayment) }}</span>
                    </div>

                    <!-- Progress bar -->
                    <div class="rounded-full bg-[#EAE0D5] h-1.5 overflow-hidden">
                        <div class="h-full bg-brand-maroon rounded-full" :style="{ width: loadingProgress + '%' }" style="transition: width 0.08s linear"></div>
                    </div>

                    <div class="flex items-center justify-center gap-1.5 text-[#8C7A72]">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="text-[11px] font-medium">Transaksi aman & terenkripsi</span>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ===== CHECKOUT FORM ===== -->
        <div v-if="orderState === 'idle'">
            <!-- Header + Stepper -->
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <Link :href="route('makanan.detail', food.slug)" class="flex h-9 w-9 items-center justify-center rounded-xl bg-white border border-[#EAE0D5] text-[#2D1B1E] hover:bg-[#F5EFE6] transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </Link>
                    <h1 class="text-2xl font-black text-[#2D1B1E]">Checkout</h1>
                </div>

                <!-- Step progress -->
                <div class="flex items-center">
                    <div class="flex items-center gap-1.5">
                        <div class="h-6 w-6 rounded-full bg-brand-maroon flex items-center justify-center">
                            <svg class="h-3 w-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-[#8C7A72]">Pesanan</span>
                    </div>
                    <div class="flex-1 h-px bg-brand-maroon/40 mx-3"></div>
                    <div class="flex items-center gap-1.5">
                        <div class="h-6 w-6 rounded-full bg-brand-maroon flex items-center justify-center">
                            <span class="text-[10px] font-black text-white">2</span>
                        </div>
                        <span class="text-xs font-bold text-brand-maroon">Data Diri</span>
                    </div>
                    <div class="flex-1 h-px bg-[#EAE0D5] mx-3"></div>
                    <div class="flex items-center gap-1.5">
                        <div class="h-6 w-6 rounded-full bg-[#EAE0D5] flex items-center justify-center">
                            <span class="text-[10px] font-bold text-[#8C7A72]">3</span>
                        </div>
                        <span class="text-xs text-[#8C7A72]">Pembayaran</span>
                    </div>
                </div>
            </div>

            <!-- 2-column grid -->
            <div class="grid grid-cols-1 lg:grid-cols-[340px_1fr] gap-6 items-start">

                <!-- LEFT sidebar -->
                <div class="rounded-[20px] bg-white border border-[#EAE0D5] shadow-sm p-5 space-y-5 lg:sticky lg:top-6">
                    <!-- Ringkasan -->
                    <div>
                        <div class="flex items-center gap-2 pb-3 border-b border-[#F5EFE6]">
                            <svg class="h-3.5 w-3.5 text-[#8C7A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span class="text-xs font-bold text-[#2D1B1E]">Ringkasan Pesanan</span>
                        </div>
                        <div class="flex items-start gap-3 mt-3">
                            <img :src="food.image" :alt="food.name" class="h-14 w-16 rounded-xl object-cover bg-[#FAF7F2] border border-[#EAE0D5] shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-[#2D1B1E] line-clamp-2">{{ food.name }}</p>
                                <p class="text-[11px] text-[#8C7A72] mt-0.5">{{ food.merchant }} · {{ quantity }} porsi</p>
                                <span class="inline-block mt-1 rounded bg-rose-100 text-rose-700 text-[9px] font-extrabold px-1.5 py-0.5">Diskon {{ food.discountPercent }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cost breakdown -->
                    <div class="space-y-2 text-xs border-t border-[#F5EFE6] pt-3">
                        <div class="flex justify-between text-[#6E5A54]">
                            <span>Subtotal</span>
                            <span class="font-semibold text-[#2D1B1E]">Rp{{ fmt(subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-[#6E5A54]">
                            <span>Biaya Layanan</span>
                            <span class="font-semibold text-[#2D1B1E]">Rp{{ fmt(serviceFee) }}</span>
                        </div>
                        <div class="flex justify-between text-[#6E5A54]">
                            <span>Wadah</span>
                            <span class="font-semibold" :class="packagingFee === 0 ? 'text-emerald-700' : 'text-[#2D1B1E]'">{{ packagingFee === 0 ? 'Gratis' : 'Rp' + fmt(packagingFee) }}</span>
                        </div>
                        <div class="flex justify-between items-baseline pt-2 border-t border-[#F5EFE6]">
                            <span class="font-extrabold text-[#2D1B1E] text-sm">Total</span>
                            <span class="font-black text-brand-maroon text-lg">Rp{{ fmt(totalPayment) }}</span>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="border-t border-[#F5EFE6] pt-4">
                        <p class="text-[11px] font-bold text-[#8C7A72] mb-2">Metode Pembayaran</p>
                        <div class="grid grid-cols-2 gap-2">
                            <label
                                v-for="opt in paymentOptions"
                                :key="opt.id"
                                class="flex items-center gap-2 rounded-xl border-2 p-2.5 cursor-pointer transition"
                                :class="paymentMethod === opt.id ? 'border-brand-maroon bg-rose-50/40' : 'border-[#EAE0D5] bg-[#FAF7F2] hover:bg-white'"
                            >
                                <input type="radio" :value="opt.id" v-model="paymentMethod" class="sr-only" />
                                <div class="h-7 w-7 rounded-lg flex items-center justify-center text-white text-[9px] font-black shrink-0" :class="opt.color">{{ opt.initial }}</div>
                                <div>
                                    <p class="text-[10px] font-bold text-[#2D1B1E] leading-none">{{ opt.label }}</p>
                                    <p class="text-[9px] text-[#8C7A72] mt-0.5">{{ opt.sublabel }}</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Pickup reminder -->
                    <div class="rounded-xl bg-[#FAF7F2] border border-[#EAE0D5] p-3 text-[11px] text-[#6E5A54] leading-relaxed space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-amber-900">
                            <svg class="h-3.5 w-3.5 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                            <span>Jadwal Ambil Gerai:</span>
                        </div>
                        <p class="font-extrabold text-brand-maroon text-xs">{{ food.pickupTime || '19.30 – 20.30 WIB' }}</p>
                        <p class="text-[10px] text-[#8C7A72]">{{ food.merchant }} · {{ food.address || 'Ketintang Surabaya' }}</p>
                    </div>
                </div>

                <!-- RIGHT form -->
                <div class="space-y-5">

                    <!-- Data Pengambil -->
                    <div class="rounded-[20px] bg-white border border-[#EAE0D5] shadow-sm p-6 space-y-4">
                        <h2 class="text-base font-extrabold text-[#2D1B1E]">Langkah 2 · Data Pengambil</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1.5">Nama Lengkap</label>
                                <input type="text" v-model="userName" class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] px-3.5 py-2.5 text-xs font-semibold text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none transition" placeholder="Nama sesuai KTM" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1.5">Alamat</label>
                                <input type="text" v-model="userAddress" class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] px-3.5 py-2.5 text-xs font-semibold text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none transition" placeholder="Alamat kos/rumah" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1.5">No. WhatsApp</label>
                                <input type="tel" v-model="userWhatsapp" class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] px-3.5 py-2.5 text-xs font-semibold text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none transition" placeholder="08xxxxxxxxxx" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1.5">Email</label>
                                <input type="email" v-model="userEmail" class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] px-3.5 py-2.5 text-xs font-semibold text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none transition" placeholder="email@mhs.ac.id" />
                            </div>
                        </div>
                    </div>

                    <!-- Waktu + Wadah -->
                    <div class="rounded-[20px] bg-white border border-[#EAE0D5] shadow-sm p-6 space-y-5">
                        <!-- Official Merchant Pickup Time Card -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <svg class="h-3.5 w-3.5 text-brand-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                                    <span class="text-xs font-bold text-[#2D1B1E]">Jadwal Ambil Resmi Gerai (Self-Pickup)</span>
                                </div>
                                <span class="rounded-lg bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 text-[10px] font-extrabold">
                                    Jadwal UMKM
                                </span>
                            </div>

                            <!-- Big highlighted pickup time box -->
                            <div class="rounded-2xl bg-amber-50/80 border-2 border-amber-300/80 p-4 space-y-2">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div>
                                        <p class="text-[11px] font-semibold text-amber-900">Rentang Waktu Pengambilan:</p>
                                        <p class="text-lg font-black text-brand-maroon tracking-tight">
                                            {{ food.pickupTime || '19.30 – 20.30 WIB' }}
                                        </p>
                                    </div>
                                    <span class="self-start sm:self-auto rounded-xl bg-white px-3 py-1.5 text-xs font-bold text-amber-900 border border-amber-300 shadow-xs">
                                        {{ food.pickupDeadlineInfo || 'Hari ini · sebelum gerai tutup' }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-[#6E5A54] leading-relaxed pt-1.5 border-t border-amber-200/60">
                                    📍 Ambil di <strong>{{ food.merchant }}</strong> ({{ food.address }}). Tunjukkan tiket QR ke kasir saat tiba.
                                </p>
                            </div>

                            <!-- Pilihan Perkiraan Tiba Mahasiswa -->
                            <div class="mt-3.5">
                                <label class="block text-[11px] font-bold text-[#2D1B1E] mb-1.5">Perkiraan Kedatangan Saya (Fleksibel):</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        v-for="arr in arrivalEstimates"
                                        :key="arr.id"
                                        type="button"
                                        @click="selectedArrival = arr.id"
                                        class="rounded-xl py-2 px-2 text-center border-2 transition"
                                        :class="selectedArrival === arr.id ? 'bg-brand-maroon text-white border-brand-maroon shadow-xs' : 'bg-[#FAF7F2] text-[#6E5A54] border-[#EAE0D5] hover:border-brand-maroon/40'"
                                    >
                                        <p class="text-xs font-bold leading-tight">{{ arr.label }}</p>
                                        <p class="text-[9px] opacity-80 mt-0.5 hidden sm:block">{{ arr.sub }}</p>
                                    </button>
                                </div>
                            </div>

                            <label class="flex items-start gap-2 mt-3 cursor-pointer">
                                <input type="checkbox" v-model="remindBefore" class="h-4 w-4 rounded text-brand-maroon focus:ring-brand-maroon mt-0.5 shrink-0" />
                                <span class="text-[11px] text-[#6E5A54]">
                                    Ingatkan via WhatsApp 30 menit sebelum batas waktu pickup berakhir
                                </span>
                            </label>
                        </div>

                        <!-- Packaging -->
                        <div class="border-t border-[#F5EFE6] pt-4">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="h-3.5 w-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                <span class="text-xs font-bold text-[#2D1B1E]">Pilihan Wadah</span>
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="flex items-center justify-between p-3 rounded-xl border-2 cursor-pointer transition"
                                    :class="ecoPackaging === 'bawa_sendiri' ? 'border-brand-maroon bg-rose-50/30' : 'border-[#EAE0D5] hover:bg-[#FAF7F2]'"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <input type="radio" value="bawa_sendiri" v-model="ecoPackaging" class="h-3.5 w-3.5 text-brand-maroon focus:ring-brand-maroon" />
                                        <span class="text-xs font-bold text-[#2D1B1E]">Bawa wadah sendiri</span>
                                    </div>
                                    <span class="rounded-lg bg-emerald-100 text-emerald-800 text-[10px] font-black px-2 py-0.5">Eco · Gratis</span>
                                </label>
                                <label
                                    class="flex items-center justify-between p-3 rounded-xl border-2 cursor-pointer transition"
                                    :class="ecoPackaging === 'kotak_kertas' ? 'border-brand-maroon bg-rose-50/30' : 'border-[#EAE0D5] hover:bg-[#FAF7F2]'"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <input type="radio" value="kotak_kertas" v-model="ecoPackaging" class="h-3.5 w-3.5 text-brand-maroon focus:ring-brand-maroon" />
                                        <span class="text-xs font-bold text-[#2D1B1E]">Kotak kertas</span>
                                    </div>
                                    <span class="text-xs font-bold text-[#6E5A54]">+Rp1.000</span>
                                </label>
                                <label
                                    class="flex items-center justify-between p-3 rounded-xl border-2 cursor-pointer transition"
                                    :class="ecoPackaging === 'thinwall' ? 'border-brand-maroon bg-rose-50/30' : 'border-[#EAE0D5] hover:bg-[#FAF7F2]'"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <input type="radio" value="thinwall" v-model="ecoPackaging" class="h-3.5 w-3.5 text-brand-maroon focus:ring-brand-maroon" />
                                        <span class="text-xs font-bold text-[#2D1B1E]">Thinwall tahan panas</span>
                                    </div>
                                    <span class="text-xs font-bold text-[#6E5A54]">+Rp2.000</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Notes & Allergen Request -->
                    <div class="rounded-[20px] bg-white border border-[#EAE0D5] shadow-sm p-6 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-[#2D1B1E]">Catatan untuk Penjual / Pantangan Alergi</label>
                            <span class="text-[10px] text-[#8C7A72]">Maks. 120 karakter</span>
                        </div>
                        <textarea
                            v-model="userNote"
                            rows="3"
                            maxlength="120"
                            placeholder="Contoh: Sambal dipisah ya Bu, ada alergi kacang & telur..."
                            class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] px-3.5 py-2.5 text-xs text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none resize-none transition"
                        ></textarea>
                        <div class="rounded-xl bg-[#FAF7F2] p-2.5 border border-[#EAE0D5] text-[11px] text-[#8C7A72] flex items-center gap-2">
                            <svg class="h-4 w-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="16" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                            </svg>
                            <span>💡 Punya pantangan atau alergi khusus? Tuliskan instruksi di atas agar penjual dapat mempersiapkannya dengan aman.</span>
                        </div>
                    </div>

                    <!-- Action bar -->
                    <div class="flex items-center gap-3 pb-6">
                        <Link :href="route('makanan.detail', food.slug)" class="flex items-center gap-1.5 px-5 py-3 rounded-xl border-2 border-[#EAE0D5] text-xs font-bold text-[#6E5A54] hover:bg-[#FAF7F2] transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Kembali
                        </Link>
                        <button
                            type="button"
                            @click="confirmReservation"
                            :disabled="!isFormValid"
                            class="flex-1 rounded-xl bg-brand-maroon py-3 text-sm font-bold text-white shadow-lg shadow-brand-maroon/20 hover:bg-brand-maroon-hover transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Lanjut Bayar
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </MahasiswaLayout>
</template>

<style scoped>
.fade-blur-enter-active,
.fade-blur-leave-active { transition: opacity 0.25s ease; }
.fade-blur-enter-from,
.fade-blur-leave-to { opacity: 0; }

@keyframes fade-in {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fade-in 0.5s ease both; }

@keyframes check-draw {
    0%   { stroke-dashoffset: 30; opacity: 0; }
    30%  { opacity: 1; }
    100% { stroke-dashoffset: 0; }
}
.animate-check-draw {
    stroke-dasharray: 30;
    stroke-dashoffset: 30;
    animation: check-draw 0.65s cubic-bezier(0.22, 1, 0.36, 1) 0.2s both;
}

@keyframes ping-slow {
    0%, 100% { transform: scale(1.5); opacity: 0.4; }
    50%       { transform: scale(1.9); opacity: 0.05; }
}
.animate-ping-slow {
    animation: ping-slow 2s ease-in-out infinite;
}
</style>
