<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    order: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close', 'view']);
</script>

<template>
    <Teleport to="body">
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-[-20px] opacity-0 scale-95"
            enter-to-class="translate-y-0 opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
        >
            <div
                v-if="order"
                class="fixed top-5 right-3 sm:right-6 z-[9999] max-w-md w-[calc(100vw-1.5rem)] sm:w-auto shadow-2xl rounded-2xl bg-white border-2 border-amber-400 p-4 text-slate-900 pointer-events-auto"
                style="margin: 0;"
            >
                <div class="flex items-start gap-3.5">
                    <!-- Icon Lonceng / Chime Bergetar -->
                    <div class="relative flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-amber-500 text-stone-900 shrink-0 shadow-md animate-bounce">
                        <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-rose-500 border border-white"></span>
                        </span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>

                    <!-- Detail Pesanan Masuk Sesuai Format Spesifikasi -->
                    <div class="flex-1 min-w-0 pr-2">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center gap-1 text-[11px] font-black uppercase text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                🔔 Pesanan Baru Masuk!
                            </span>
                            <span class="text-[10px] font-mono font-bold text-slate-400">
                                {{ order.date || 'Baru Saja' }}
                            </span>
                        </div>

                        <!-- Format Sesuai Instruksi User: [Nama Mahasiswa] memesan [Nama Makanan] (Kode: #ORD-XXX) -->
                        <p class="text-xs sm:text-sm font-extrabold text-slate-900 mt-1 leading-snug">
                            <span class="text-brand-maroon">{{ order.customerName || 'Mahasiswa' }}</span> memesan <span class="text-slate-900 underline decoration-amber-400 decoration-2 underline-offset-2">{{ order.foodName || order.food?.name || 'Makanan Surplus' }}</span>
                        </p>

                        <div class="flex items-center gap-2 mt-2">
                            <span class="rounded-lg bg-slate-100 px-2 py-1 text-[11px] font-mono font-black text-slate-800 border border-slate-200">
                                Kode: #{{ order.id }}
                            </span>
                            <span v-if="order.total" class="text-xs font-black text-emerald-700">
                                Rp{{ order.total.toLocaleString('id-ID') }}
                            </span>
                            <span v-if="order.ecoPackaging" class="rounded bg-emerald-50 text-emerald-800 px-1.5 py-0.5 text-[9px] font-bold border border-emerald-200">
                                Wadah Sendiri
                            </span>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <button
                        @click="emit('close')"
                        class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer shrink-0"
                        title="Tutup Notifikasi"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </transition>
    </Teleport>
</template>
