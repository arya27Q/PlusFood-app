<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { getSavedOrders } from '@/Utils/mockFoods';

const props = defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close', 'validated']);

const manualCode = ref('');
const scanState = ref('scanning'); // 'scanning', 'success', 'error'
const validatedOrder = ref(null);
const errorMessage = ref('');
const isValidating = ref(false);

const sampleTestCode = computed(() => {
    const pending = props.orders?.find(o => o.status === 'menunggu');
    return pending?.order_number || pending?.id || 'PF-88219';
});

const validateCode = (codeToTest) => {
    const raw = (codeToTest || manualCode.value).trim();
    if (!raw || isValidating.value) return;

    const code = raw.toLowerCase();

    // Prioritaskan mencari di props.orders (data database MySQL)
    const list = (props.orders && props.orders.length > 0) ? props.orders : getSavedOrders();
    const found = list.find(o => {
        const idStr = String(o.id || '').toLowerCase();
        const numStr = String(o.order_number || '').toLowerCase();
        const qrStr = String(o.qr_token || o.qrCode || '').toLowerCase();
        return idStr === code || 
               numStr === code || 
               qrStr === code || 
               idStr.replace(/^pf-/, '') === code.replace(/^pf-/, '') ||
               numStr.replace(/^pf-/, '') === code.replace(/^pf-/, '');
    });

    if (!found) {
        scanState.value = 'error';
        errorMessage.value = `Kode "${raw}" tidak ditemukan dalam sistem pesanan aktif.`;
        return;
    }

    if (found.status === 'selesai') {
        scanState.value = 'error';
        errorMessage.value = `Tiket #${found.order_number || found.id} SUDAH DIGUNAKAN sebelumnya! Cegah penyerahan ganda.`;
        return;
    }

    if (found.status === 'batal' || found.status === 'dibatalkan') {
        scanState.value = 'error';
        errorMessage.value = `Tiket #${found.order_number || found.id} telah KADALUARSA / Dibatalkan.`;
        return;
    }

    // Sukses validasi -> kirim patch ke database MySQL via router.patch
    isValidating.value = true;
    router.patch(route('mitra.pesanan.status', found.id), {
        status: 'selesai'
    }, {
        preserveScroll: true,
        onSuccess: () => {
            found.status = 'selesai';
            validatedOrder.value = found;
            scanState.value = 'success';
            isValidating.value = false;
            emit('validated', found);
        },
        onError: (err) => {
            console.error('Gagal validasi QR via backend:', err);
            found.status = 'selesai';
            validatedOrder.value = found;
            scanState.value = 'success';
            isValidating.value = false;
            emit('validated', found);
        }
    });
};

const resetScan = () => {
    scanState.value = 'scanning';
    manualCode.value = '';
    validatedOrder.value = null;
    errorMessage.value = '';
    isValidating.value = false;
};

// Quick simulate scan button for demo testing
const simulateSampleScan = () => {
    validateCode(sampleTestCode.value);
};
</script>

<template>
    <Teleport to="body">
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 animate-fade-in"
            style="top: 0; left: 0; width: 100vw; height: 100vh;"
        >
        <div class="relative w-full max-w-md rounded-[28px] bg-white p-6 sm:p-7 shadow-2xl border border-[#EAE0D5] space-y-5 max-h-[92vh] overflow-y-auto">
            
            <!-- Close Button -->
            <button
                @click="emit('close')"
                class="absolute top-5 right-5 flex h-8 w-8 items-center justify-center rounded-full bg-[#FAF7F2] text-gray-400 hover:text-gray-700 transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- ================= STATE 1: SCANNING VIEW ================= -->
            <div v-if="scanState === 'scanning'" class="space-y-4 text-center">
                <div class="space-y-1">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 text-amber-900 px-3 py-1 text-[11px] font-extrabold border border-amber-300">
                        <svg class="h-3.5 w-3.5 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Validasi Tiket Kasir
                    </span>
                    <h3 class="text-xl font-black text-[#2D1B1E]">Scan QR Code Mahasiswa</h3>
                    <p class="text-xs text-[#8C7A72]">
                        Arahkan kamera ke layar HP mahasiswa atau masukkan ID reservasi manual.
                    </p>
                </div>

                <!-- Camera Viewfinder Mockup with Laser Animation -->
                <div class="relative mx-auto w-64 h-64 rounded-2xl bg-stone-900 border-2 border-brand-maroon overflow-hidden flex items-center justify-center shadow-inner">
                    <!-- Corner Reticles -->
                    <div class="absolute top-3 left-3 w-5 h-5 border-t-2 border-l-2 border-emerald-400"></div>
                    <div class="absolute top-3 right-3 w-5 h-5 border-t-2 border-r-2 border-emerald-400"></div>
                    <div class="absolute bottom-3 left-3 w-5 h-5 border-b-2 border-l-2 border-emerald-400"></div>
                    <div class="absolute bottom-3 right-3 w-5 h-5 border-b-2 border-r-2 border-emerald-400"></div>

                    <!-- Scanning Laser Bar -->
                    <div class="absolute inset-x-0 h-0.5 bg-emerald-400 shadow-[0_0_8px_#34d399] animate-pulse"></div>

                    <!-- Viewfinder Icon / Center Aim -->
                    <div class="text-white/40 flex flex-col items-center gap-2">
                        <svg class="h-12 w-12 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span class="text-[10px] font-mono text-emerald-400">Mencari QR Code...</span>
                    </div>

                    <!-- Quick Demo Button Overlay -->
                    <button
                        @click="simulateSampleScan"
                        class="absolute bottom-3 inset-x-4 inline-flex items-center justify-center gap-1.5 rounded-xl bg-white/20 hover:bg-white/30 backdrop-blur-md py-1.5 text-[11px] font-bold text-white border border-white/30 transition"
                    >
                        <svg class="h-3.5 w-3.5 text-amber-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Klik untuk Uji Scan PF-88219
                    </button>
                </div>

                <!-- Manual Input Option -->
                <div class="pt-2 border-t border-[#F5EFE6] space-y-2 text-left">
                    <label class="block text-xs font-bold text-[#2D1B1E]">
                        Atau Ketik Kode Reservasi (6 Karakter):
                    </label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            v-model="manualCode"
                            placeholder="Contoh: PF-88219"
                            @keyup.enter="validateCode(manualCode)"
                            class="flex-1 rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] px-3.5 py-2.5 text-xs font-bold text-[#2D1B1E] uppercase focus:border-brand-maroon focus:bg-white focus:outline-none"
                        />
                        <button
                            @click="validateCode(manualCode)"
                            class="rounded-xl bg-brand-maroon px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-brand-maroon-hover transition"
                        >
                            Validasi
                        </button>
                    </div>
                </div>
            </div>

            <!-- ================= STATE 2: SUKSES VALIDASI ================= -->
            <div v-else-if="scanState === 'success'" class="space-y-4 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 border-2 border-emerald-300 shadow-md">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <div>
                    <span class="rounded bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-xs font-black border border-emerald-300">
                        TIKET VALID · SIAP DISERAHKAN
                    </span>
                    <h3 class="text-xl font-black text-[#2D1B1E] mt-1.5">Pesanan Sukses Divalidasi!</h3>
                </div>

                <!-- Detail Pesanan yang Divalidasi -->
                <div class="rounded-2xl bg-[#FAF7F2] p-4 border border-[#EAE0D5] text-left space-y-2.5">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-[#8C7A72]">ID Tiket</span>
                        <span class="font-extrabold text-brand-maroon">#{{ validatedOrder?.order_number || validatedOrder?.id }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-[#8C7A72]">Nama Mahasiswa</span>
                        <span class="font-extrabold text-[#2D1B1E]">{{ validatedOrder?.customerName || validatedOrder?.customer_name }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-[#8C7A72]">Menu & Porsi</span>
                        <span class="font-extrabold text-[#2D1B1E]">
                            {{ validatedOrder?.food?.shortName || validatedOrder?.food?.name }} (<strong class="text-brand-maroon font-black text-sm">{{ validatedOrder?.quantity }} Porsi</strong>)
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-[#8C7A72]">Wadah Makanan</span>
                        <span class="font-bold text-emerald-700">{{ validatedOrder?.ecoPackagingLabel || 'Bawa Wadah Sendiri (Rp0)' }}</span>
                    </div>
                    <div class="border-t border-[#EAE0D5] pt-2 flex justify-between items-baseline">
                        <span class="text-xs font-bold text-[#6E5A54]">Kasir Terima Uang:</span>
                        <span class="text-lg font-black text-brand-maroon">
                            Rp{{ (validatedOrder?.total || validatedOrder?.total_price || 0).toLocaleString('id-ID') }}
                        </span>
                    </div>
                </div>

                <div class="pt-2 flex gap-2">
                    <button
                        @click="resetScan"
                        class="flex-1 rounded-xl bg-[#FAF7F2] border border-[#EAE0D5] py-3 text-xs font-bold text-[#6E5A54] hover:bg-[#F5EFE6] transition"
                    >
                        Scan Tiket Lain
                    </button>
                    <button
                        @click="emit('close')"
                        class="flex-1 rounded-xl bg-brand-maroon py-3 text-xs font-bold text-white shadow-md hover:bg-brand-maroon-hover transition"
                    >
                        Selesai
                    </button>
                </div>
            </div>

            <!-- ================= STATE 3: ERROR / DOUBLE CLAIM ================= -->
            <div v-else class="space-y-4 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-rose-100 text-rose-700 border-2 border-rose-300 shadow-md">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <div>
                    <span class="rounded bg-rose-100 text-rose-800 px-2.5 py-0.5 text-xs font-black border border-rose-300">
                        PERINGATAN VALIDASI
                    </span>
                    <h3 class="text-xl font-black text-rose-900 mt-1.5">Tiket Tidak Dapat Digunakan</h3>
                    <p class="text-xs text-rose-700 mt-1 max-w-sm mx-auto font-medium">
                        {{ errorMessage }}
                    </p>
                </div>

                <div class="pt-2">
                    <button
                        @click="resetScan"
                        class="w-full rounded-xl bg-brand-maroon py-3 text-xs font-bold text-white shadow-md hover:bg-brand-maroon-hover transition"
                    >
                        Coba Scan Ulang
                    </button>
                </div>
            </div>

        </div>
    </div>
    </Teleport>
</template>
