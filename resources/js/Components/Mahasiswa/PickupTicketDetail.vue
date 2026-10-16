<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['back', 'cancel']);

// Format manual code (e.g. "S B - 4 X 2 9 A")
const manualCode = computed(() => {
    if (props.order.manualCode) return props.order.manualCode;
    const raw = props.order.id ? props.order.id.replace(/[^A-Za-z0-9]/g, '') : 'SB4X29A';
    if (raw.length >= 6) {
        return `${raw.substring(0, 2)} - ${raw.substring(2, 6)}`;
    }
    return 'SB - 4X29A';
});

// Clipboard copy feedback
const isCopied = ref(false);
const copyCode = () => {
    try {
        const clean = manualCode.value.replace(/\s+/g, '');
        navigator.clipboard.writeText(clean);
        isCopied.value = true;
        setTimeout(() => {
            isCopied.value = false;
        }, 2200);
    } catch (e) {
        console.error('Clipboard copy error', e);
    }
};

// Google Maps URL
const googleMapsUrl = computed(() => {
    if (props.order.mapsUrl && props.order.mapsUrl.startsWith('http')) {
        return props.order.mapsUrl;
    }
    const store = props.order.food?.merchant || 'Warung Bu Sari';
    const addr = props.order.food?.address || 'Jl. Ketintang Baru 12 Surabaya';
    return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(store + ' ' + addr)}`;
});

// Live ticking countdown timer
const countdownSeconds = ref(42 * 60 + 18); // default sesuai screenshot: 00:42:18
let countdownInterval = null;

const countdownFormatted = computed(() => {
    const s = Math.max(0, countdownSeconds.value);
    const hrs = String(Math.floor(s / 3600)).padStart(2, '0');
    const mins = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
    const secs = String(s % 60).padStart(2, '0');
    return `${hrs}:${mins}:${secs}`;
});

onMounted(() => {
    countdownInterval = setInterval(() => {
        if (countdownSeconds.value > 0) {
            countdownSeconds.value--;
        }
    }, 1000);
});

onUnmounted(() => {
    if (countdownInterval) clearInterval(countdownInterval);
});

// Modal konfirmasi pembatalan
const showCancelConfirm = ref(false);
const handleConfirmCancel = () => {
    showCancelConfirm.value = false;
    emit('cancel', props.order);
};
</script>

<template>
    <div class="space-y-6 max-w-4xl mx-auto pb-16 animate-fade-in">
        
        <!-- ================= TOP NAVIGATION / BREADCRUMB ================= -->
        <div class="flex items-center gap-4">
            <button
                @click="emit('back')"
                class="flex h-11 w-11 items-center justify-center rounded-full bg-white border border-[#E8DFD8] text-stone-600 shadow-2xs hover:bg-[#FAF7F2] hover:text-brand-maroon transition active:scale-95"
                title="Kembali ke Riwayat"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </button>

            <div>
                <p class="text-xs font-semibold text-[#8A7570] tracking-wide">
                    Riwayat / Menunggu Pickup
                </p>
                <h1 class="text-2xl sm:text-3xl font-black text-brand-maroon tracking-tight mt-0.5">
                    Tunjukkan QR ke Penjual
                </h1>
            </div>
        </div>

        <!-- ================= MAIN TWO-COLUMN GRID ================= -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
            
            <!-- LEFT COLUMN: TIKET PICKUP CARD (Image 2 Replica) -->
            <div class="md:col-span-5 w-full">
                <div class="relative w-full rounded-[28px] overflow-hidden bg-white shadow-xl shadow-brand-maroon/5 border border-[#E8DFD8]">
                    
                    <!-- Maroon Header Card -->
                    <div class="bg-brand-maroon p-5 text-white flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-extrabold tracking-widest text-rose-200 uppercase">
                                TIKET PICKUP
                            </p>
                            <h3 class="text-base font-black text-white mt-0.5 leading-snug line-clamp-1">
                                {{ order.food?.shortName || order.food?.name || 'Nasi Ayam Bakar' }} × {{ order.quantity || 1 }}
                            </h3>
                        </div>

                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold text-white border border-white/20 backdrop-blur-xs">
                            Lunas
                        </span>
                    </div>

                    <!-- Card Body (QR Box & Manual Code) -->
                    <div class="p-6 text-center bg-white flex flex-col items-center">
                        
                        <!-- QR Code Container -->
                        <div class="relative w-56 h-56 rounded-2xl bg-white p-3 border border-stone-200 shadow-inner flex items-center justify-center">
                            
                            <!-- Authentic SVG QR Code Matrix -->
                            <svg class="w-full h-full text-brand-maroon" viewBox="0 0 160 160" fill="currentColor">
                                <!-- Top-Left Position Finder Pattern -->
                                <rect x="8" y="8" width="44" height="44" rx="8" fill="currentColor" />
                                <rect x="14" y="14" width="32" height="32" rx="4" fill="white" />
                                <rect x="20" y="20" width="20" height="20" rx="3" fill="currentColor" />

                                <!-- Top-Right Position Finder Pattern -->
                                <rect x="108" y="8" width="44" height="44" rx="8" fill="currentColor" />
                                <rect x="114" y="14" width="32" height="32" rx="4" fill="white" />
                                <rect x="120" y="20" width="20" height="20" rx="3" fill="currentColor" />

                                <!-- Bottom-Left Position Finder Pattern -->
                                <rect x="8" y="108" width="44" height="44" rx="8" fill="currentColor" />
                                <rect x="14" y="114" width="32" height="32" rx="4" fill="white" />
                                <rect x="20" y="120" width="20" height="20" rx="3" fill="currentColor" />

                                <!-- Timing Patterns (Horizontal & Vertical) -->
                                <rect x="58" y="24" width="6" height="6" fill="currentColor" />
                                <rect x="70" y="24" width="6" height="6" fill="currentColor" />
                                <rect x="82" y="24" width="6" height="6" fill="currentColor" />
                                <rect x="94" y="24" width="6" height="6" fill="currentColor" />

                                <rect x="24" y="58" width="6" height="6" fill="currentColor" />
                                <rect x="24" y="70" width="6" height="6" fill="currentColor" />
                                <rect x="24" y="82" width="6" height="6" fill="currentColor" />
                                <rect x="24" y="94" width="6" height="6" fill="currentColor" />

                                <!-- Data Modules Matrix Pattern -->
                                <rect x="58" y="10" width="8" height="8" rx="1.5" />
                                <rect x="72" y="12" width="8" height="6" rx="1.5" />
                                <rect x="88" y="10" width="10" height="8" rx="1.5" />

                                <rect x="58" y="38" width="6" height="12" rx="1.5" />
                                <rect x="70" y="38" width="10" height="8" rx="1.5" />
                                <rect x="86" y="42" width="12" height="8" rx="1.5" />
                                <rect x="102" y="38" width="8" height="8" rx="1.5" />

                                <rect x="10" y="58" width="8" height="8" rx="1.5" />
                                <rect x="36" y="60" width="12" height="8" rx="1.5" />
                                <rect x="54" y="56" width="8" height="10" rx="1.5" />

                                <rect x="100" y="56" width="10" height="10" rx="1.5" />
                                <rect x="116" y="60" width="8" height="8" rx="1.5" />
                                <rect x="130" y="56" width="10" height="10" rx="1.5" />
                                <rect x="144" y="60" width="8" height="8" rx="1.5" />

                                <rect x="10" y="74" width="10" height="8" rx="1.5" />
                                <rect x="36" y="76" width="8" height="10" rx="1.5" />
                                <rect x="50" y="72" width="10" height="8" rx="1.5" />

                                <rect x="102" y="72" width="8" height="12" rx="1.5" />
                                <rect x="116" y="76" width="12" height="8" rx="1.5" />
                                <rect x="134" y="72" width="10" height="10" rx="1.5" />
                                <rect x="146" y="76" width="6" height="8" rx="1.5" />

                                <rect x="10" y="90" width="8" height="8" rx="1.5" />
                                <rect x="34" y="92" width="12" height="6" rx="1.5" />
                                <rect x="52" y="88" width="8" height="12" rx="1.5" />

                                <rect x="100" y="90" width="12" height="8" rx="1.5" />
                                <rect x="118" y="92" width="8" height="10" rx="1.5" />
                                <rect x="132" y="88" width="12" height="8" rx="1.5" />
                                <rect x="146" y="92" width="8" height="8" rx="1.5" />

                                <rect x="58" y="108" width="10" height="10" rx="1.5" />
                                <rect x="74" y="112" width="8" height="8" rx="1.5" />
                                <rect x="88" y="106" width="12" height="8" rx="1.5" />
                                <rect x="106" y="110" width="8" height="10" rx="1.5" />
                                <rect x="120" y="108" width="10" height="8" rx="1.5" />
                                <rect x="136" y="112" width="8" height="8" rx="1.5" />
                                <rect x="146" y="108" width="8" height="8" rx="1.5" />

                                <rect x="58" y="124" width="8" height="8" rx="1.5" />
                                <rect x="72" y="126" width="12" height="6" rx="1.5" />
                                <rect x="90" y="122" width="8" height="10" rx="1.5" />
                                <rect x="104" y="126" width="12" height="8" rx="1.5" />
                                <rect x="122" y="122" width="8" height="8" rx="1.5" />
                                <rect x="136" y="126" width="10" height="8" rx="1.5" />
                                <rect x="148" y="122" width="6" height="8" rx="1.5" />

                                <rect x="58" y="138" width="10" height="8" rx="1.5" />
                                <rect x="74" y="140" width="8" height="6" rx="1.5" />
                                <rect x="88" y="136" width="12" height="10" rx="1.5" />
                                <rect x="106" y="138" width="8" height="8" rx="1.5" />
                                <rect x="120" y="136" width="10" height="10" rx="1.5" />
                                <rect x="136" y="140" width="8" height="6" rx="1.5" />
                            </svg>

                            <!-- Center Watermark Logo (PlusFood Bowl Emblem) -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="w-10 h-10 rounded-xl bg-white border-2 border-brand-maroon/20 p-1 shadow-md flex items-center justify-center">
                                    <div class="w-full h-full rounded-lg bg-brand-maroon flex items-center justify-center text-white">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 10C3 14.9706 7.02944 19 12 19C16.9706 19 21 14.9706 21 10H3Z" fill="currentColor" />
                                            <path d="M12 4V8M10 6H14" stroke="#FAF7F2" stroke-width="2" stroke-linecap="round" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Manual Code -->
                        <p class="text-xs font-semibold text-[#8A7570] mt-4">
                            Kode manual
                        </p>
                        <div class="mt-1 flex items-center justify-center gap-2">
                            <h2 class="text-xl sm:text-2xl font-black tracking-[0.2em] text-brand-maroon">
                                {{ manualCode }}
                            </h2>
                            <button
                                @click="copyCode"
                                class="p-1.5 rounded-lg text-stone-400 hover:text-brand-maroon hover:bg-rose-50 transition active:scale-90"
                                :title="isCopied ? 'Tersalin!' : 'Salin Kode Manual'"
                            >
                                <svg v-if="!isCopied" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <svg v-else class="w-5 h-5 text-emerald-600 animate-scale-pop" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <span v-if="isCopied" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 mt-0.5">
                            <svg class="h-3.5 w-3.5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Kode manual berhasil disalin!
                        </span>
                    </div>

                    <!-- Perforated Ticket Divider with Left/Right Semicircle Notches -->
                    <div class="relative flex items-center bg-white px-0 py-1">
                        <!-- Left Semicircle Notch -->
                        <div class="w-6 h-6 rounded-r-full bg-[#FAF7F2] -ml-3 border-r border-[#E8DFD8]"></div>
                        <!-- Perforated Dashed Line -->
                        <div class="flex-1 border-t-2 border-dashed border-[#E8DFD8] mx-2"></div>
                        <!-- Right Semicircle Notch -->
                        <div class="w-6 h-6 rounded-l-full bg-[#FAF7F2] -mr-3 border-l border-[#E8DFD8]"></div>
                    </div>

                    <!-- Card Footer: Pemesan & Total -->
                    <div class="p-5 bg-white flex items-center justify-between border-t border-transparent text-xs">
                        <div class="text-left">
                            <p class="text-[#8A7570] font-medium text-[11px]">Pemesan</p>
                            <p class="text-[#2D1B1E] font-black text-sm mt-0.5">
                                {{ order.customerName || 'Raka Pratama' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[#8A7570] font-medium text-[11px]">Total</p>
                            <p class="text-brand-maroon font-black text-base mt-0.5">
                                Rp{{ (order.total || 13000).toLocaleString('id-ID') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Countdown Banner, Merchant Card, Cara Pickup (Image 2 Replica) -->
            <div class="md:col-span-7 w-full space-y-4">
                
                <!-- 1. Countdown Banner -->
                <div class="rounded-2xl bg-[#FFF5F5] border border-rose-200/80 p-3.5 text-center text-xs sm:text-sm font-semibold text-rose-800 flex items-center justify-center gap-2 shadow-2xs">
                    <svg class="w-4 h-4 text-rose-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                    </svg>
                    <span>
                        Ambil sebelum {{ order.pickupDeadline || '20.30' }} · sisa <strong class="font-black text-rose-950 font-mono">{{ countdownFormatted }}</strong>
                    </span>
                </div>

                <!-- 2. Restaurant & Food Info Card with Google Maps Action -->
                <div class="rounded-2xl bg-white border border-[#E8DFD8] p-5 shadow-sm space-y-4">
                    <div class="flex items-start gap-4">
                        <!-- Food Thumbnail -->
                        <img
                            :src="order.food?.image || 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=500&q=80'"
                            :alt="order.food?.name || 'Makanan'"
                            class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover shadow-xs border border-stone-100 shrink-0"
                        />

                        <!-- Merchant Details -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base sm:text-lg font-black text-[#2D1B1E] leading-snug">
                                {{ order.food?.name || 'Nasi Ayam Bakar Bumbu Rujak' }}
                            </h3>
                            
                            <p class="text-xs font-bold text-stone-700 flex items-center gap-1.5 mt-1.5">
                                <svg class="w-3.5 h-3.5 text-[#8A7570]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>{{ order.food?.merchant || 'Warung Bu Sari' }}</span>
                            </p>

                            <p class="text-xs text-stone-500 flex items-center gap-1.5 mt-1 leading-relaxed">
                                <svg class="w-3.5 h-3.5 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="line-clamp-2">
                                    {{ order.food?.address || 'Jl. Ketintang Baru 12 · 2 km dari kampus' }}
                                </span>
                            </p>

                            <!-- Buka Rute di Maps Button (Requested!) -->
                            <div class="mt-3.5">
                                <a
                                    :href="googleMapsUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 rounded-xl border-2 border-brand-maroon px-4 py-2 text-xs font-black text-brand-maroon bg-white hover:bg-rose-50 transition active:scale-95 shadow-xs"
                                >
                                    <svg class="w-4 h-4 text-brand-maroon -rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <span>Buka Rute di Maps</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Cara Pickup Steps Card -->
                <div class="rounded-2xl bg-white border border-[#E8DFD8] p-5 shadow-sm space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-100 text-brand-maroon text-xs font-black">
                            i
                        </span>
                        <h4 class="text-sm font-black text-[#2D1B1E]">
                            Cara Pickup
                        </h4>
                    </div>

                    <div class="space-y-3.5 text-xs text-stone-700">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#FAF0E6] text-amber-950 font-black text-xs shrink-0">
                                1
                            </span>
                            <p class="font-medium">
                                Naikkan kecerahan layar agar QR mudah dipindai
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#FAF0E6] text-amber-950 font-black text-xs shrink-0">
                                2
                            </span>
                            <p class="font-medium">
                                Tunjukkan ke kasir {{ order.food?.merchant || 'Warung Bu Sari' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#FAF0E6] text-amber-950 font-black text-xs shrink-0">
                                3
                            </span>
                            <p class="font-medium">
                                Bawa wadah sendiri sesuai pesanan
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 4. Cancel Policy Note -->
                <div class="pt-1 px-1">
                    <p class="text-xs text-[#8A7570] leading-relaxed">
                        Tidak jadi ambil? 
                        <button
                            @click="showCancelConfirm = true"
                            class="font-black text-brand-maroon hover:underline underline-offset-2 ml-1"
                        >
                            Batalkan pesanan
                        </button> 
                        maksimal 30 menit sebelum jam ambil.
                    </p>
                </div>
            </div>
        </div>

        <!-- ================= MODAL KONFIRMASI PEMBATALAN ================= -->
        <div
            v-if="showCancelConfirm"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 animate-fade-in"
        >
            <div class="w-full max-w-sm rounded-[24px] bg-white p-6 shadow-2xl border border-[#E8DFD8] text-center space-y-4">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-50 text-rose-700">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <div>
                    <h3 class="text-lg font-black text-[#2D1B1E]">Batalkan Pesanan Ini?</h3>
                    <p class="text-xs text-stone-500 mt-1">
                        Porsi makanan surplus ini akan dikembalikan ke listing aktif agar dapat diselamatkan mahasiswa lain.
                    </p>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button
                        @click="showCancelConfirm = false"
                        class="flex-1 rounded-xl border border-stone-200 py-2.5 text-xs font-bold text-stone-600 hover:bg-stone-50 transition"
                    >
                        Kembali
                    </button>
                    <button
                        @click="handleConfirmCancel"
                        class="flex-1 rounded-xl bg-rose-600 py-2.5 text-xs font-bold text-white hover:bg-rose-700 transition"
                    >
                        Ya, Batalkan
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.25s ease-out forwards;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes pop {
    0% { transform: scale(0.6); }
    70% { transform: scale(1.2); }
    100% { transform: scale(1); }
}
.animate-scale-pop {
    animation: pop 0.25s ease-out forwards;
}
</style>
