<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { getPayoutHistory, addPayoutRecord } from '@/Utils/mockFoods';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    availableBalance: {
        type: Number,
        default: 216000,
    },
    todayRevenue: {
        type: Number,
        default: 216000,
    },
});

const emit = defineEmits(['close', 'withdrawn']);

// Rekening Bank Mitra UMKM (Sinkron dengan Profil Mitra)
const mitraBank = ref({
    bankName: 'Bank Mandiri',
    bankAccount: '1420-0192-3841',
    bankHolder: 'Sari Wulandari',
    storeName: 'Warung Bu Sari'
});

const loadMitraProfile = () => {
    if (typeof window !== 'undefined') {
        try {
            const saved = localStorage.getItem('plusfood_mitra_profile');
            if (saved) {
                const parsed = JSON.parse(saved);
                if (parsed.bankName) mitraBank.value.bankName = parsed.bankName;
                if (parsed.bankAccount) mitraBank.value.bankAccount = parsed.bankAccount;
                if (parsed.bankHolder) mitraBank.value.bankHolder = parsed.bankHolder;
                if (parsed.storeName) mitraBank.value.storeName = parsed.storeName;
            }
        } catch (e) {}
    }
};

const withdrawalAmount = ref(50000);
const isSubmitting = ref(false);
const isSuccess = ref(false);
const errorMessage = ref('');
const payoutHistory = ref([]);
const lastWithdrawnAmount = ref(0);

const reloadHistory = () => {
    payoutHistory.value = getPayoutHistory();
};

watch(() => props.show, (newVal) => {
    if (newVal) {
        loadMitraProfile();
        reloadHistory();
        isSuccess.value = false;
        errorMessage.value = '';
        withdrawalAmount.value = Math.min(100000, props.availableBalance) || props.availableBalance || 50000;
    }
});

onMounted(() => {
    loadMitraProfile();
    reloadHistory();
});

const setPreset = (amount) => {
    if (amount === 'all') {
        withdrawalAmount.value = props.availableBalance;
    } else {
        withdrawalAmount.value = Math.min(amount, props.availableBalance);
    }
    errorMessage.value = '';
};

const handleConfirmWithdrawal = () => {
    errorMessage.value = '';
    const amount = Number(withdrawalAmount.value);

    if (!amount || isNaN(amount) || amount <= 0) {
        errorMessage.value = 'Silakan masukkan nominal penarikan yang valid.';
        return;
    }

    if (amount < 20000) {
        errorMessage.value = 'Batas minimal pencairan saldo adalah Rp 20.000.';
        return;
    }

    if (amount > props.availableBalance) {
        errorMessage.value = `Saldo Anda tidak mencukupi. Saldo tersedia saat ini: Rp${props.availableBalance.toLocaleString('id-ID')}.`;
        return;
    }

    isSubmitting.value = true;

    // Simulasi loading 1 detik sesuai spesifikasi tugas
    setTimeout(() => {
        isSubmitting.value = false;
        isSuccess.value = true;
        lastWithdrawnAmount.value = amount;

        const newRecord = {
            id: 'WD-' + Math.floor(100000 + Math.random() * 900000),
            amount: amount,
            bankName: mitraBank.value.bankName,
            bankAccount: mitraBank.value.bankAccount,
            bankHolder: mitraBank.value.bankHolder,
            status: 'proses',
            statusLabel: 'Sedang Diproses (1x24 Jam)',
            date: 'Hari ini, ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB',
            referenceId: 'TRX-MDR-' + Math.floor(10000 + Math.random() * 90000)
        };

        payoutHistory.value = addPayoutRecord(newRecord);
        emit('withdrawn', { amount, record: newRecord });
    }, 1000);
};

const handleClose = () => {
    emit('close');
};
</script>

<template>
    <Teleport to="body">
        <div
            v-if="show"
            class="fixed inset-0 z-[9999] w-screen h-screen bg-black/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5 overflow-y-auto animate-in fade-in duration-200"
            style="top: 0; left: 0; margin: 0;"
        >
            <div class="relative w-full max-w-xl rounded-[28px] bg-white p-6 sm:p-7 shadow-2xl border border-slate-200/90 my-auto max-h-[92vh] overflow-y-auto space-y-6">
                
                <!-- Tombol Tutup Silang -->
                <button
                    @click="handleClose"
                    class="absolute top-5 right-5 flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition cursor-pointer"
                    title="Tutup Modal"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Header Modal -->
                <div class="flex items-center gap-3">
                    <div class="h-11 w-11 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">Pencairan Saldo Mitra</h2>
                            <span class="rounded-md bg-emerald-100 text-emerald-800 px-2 py-0.5 text-[10px] font-black border border-emerald-300">
                                Real-Time Payout
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Cairkan pendapatan hasil penjualan makanan surplus langsung ke rekening bank kasir gerai Anda.
                        </p>
                    </div>
                </div>

                <!-- Kartu Saldo Tersedia -->
                <div class="rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-5 text-white shadow-lg relative overflow-hidden">
                    <!-- Subtle background badge -->
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-brand-maroon/20 rounded-full blur-2xl"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                                Saldo Kasir Tersedia untuk Ditarik
                            </span>
                            <p class="text-3xl font-black text-white tracking-tight mt-1">
                                Rp{{ availableBalance.toLocaleString('id-ID') }}
                            </p>
                        </div>
                        <div class="text-left sm:text-right border-t sm:border-t-0 sm:border-l border-white/10 pt-2 sm:pt-0 sm:pl-4">
                            <span class="text-[10px] font-semibold text-slate-400 block">Total Omzet Hari Ini:</span>
                            <span class="text-sm font-extrabold text-amber-300">Rp{{ todayRevenue.toLocaleString('id-ID') }}</span>
                            <p class="text-[10px] text-emerald-400 mt-0.5 font-medium">Bebas biaya admin penarikan</p>
                        </div>
                    </div>
                </div>

                <!-- Banner Status Sukses Setelah Pengajuan -->
                <div
                    v-if="isSuccess"
                    class="rounded-2xl bg-emerald-500/10 border border-emerald-500/30 p-4 text-emerald-900 shadow-sm animate-in fade-in zoom-in-95"
                >
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-extrabold text-emerald-950">
                                Pengajuan Pencairan Berhasil Diproses!
                            </p>
                            <p class="text-xs text-emerald-800 mt-0.5 leading-relaxed">
                                Nominal <span class="font-bold">Rp{{ lastWithdrawnAmount.toLocaleString('id-ID') }}</span> berhasil diajukan. Dana akan masuk ke rekening <span class="font-bold">{{ mitraBank.bankName }}</span> dalam <span class="underline font-bold">1x24 jam kerja</span>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Rekening Bank Tujuan (Sinkron dari Profil Mitra) -->
                <div class="rounded-2xl border border-slate-200/90 bg-slate-50/70 p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-brand-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Rekening Tujuan Mitra
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-[10px] font-extrabold border border-emerald-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                            Terverifikasi
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1 text-xs">
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/80 shadow-2xs">
                            <span class="text-[10px] text-slate-400 block font-semibold">Bank Penerima</span>
                            <span class="font-black text-slate-900 text-sm mt-0.5 block">{{ mitraBank.bankName }}</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/80 shadow-2xs">
                            <span class="text-[10px] text-slate-400 block font-semibold">Nomor Rekening</span>
                            <span class="font-mono font-black text-brand-maroon text-sm mt-0.5 block">{{ mitraBank.bankAccount }}</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/80 shadow-2xs">
                            <span class="text-[10px] text-slate-400 block font-semibold">Atas Nama</span>
                            <span class="font-extrabold text-slate-900 text-sm mt-0.5 block truncate">{{ mitraBank.bankHolder }}</span>
                        </div>
                    </div>
                </div>

                <!-- Input Nominal Penarikan & Preset Buttons -->
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-800">
                        Nominal Penarikan Dana (Rp)
                    </label>

                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-base font-black text-slate-400">Rp</span>
                        <input
                            v-model.number="withdrawalAmount"
                            type="number"
                            min="20000"
                            :max="availableBalance"
                            step="5000"
                            placeholder="Contoh: 100000"
                            :disabled="isSubmitting || availableBalance < 20000"
                            class="w-full rounded-2xl border border-slate-200 bg-white py-3.5 pl-12 pr-4 text-lg font-black text-slate-900 shadow-xs focus:border-brand-maroon focus:ring-2 focus:ring-brand-maroon/20 transition disabled:opacity-60 disabled:cursor-not-allowed"
                        />
                    </div>

                    <!-- Tombol Preset Cepat (Rp 50.000, Rp 100.000, Tarik Semua) -->
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="text-[11px] font-bold text-slate-500 mr-1">Pilihan Cepat:</span>
                        <button
                            type="button"
                            @click="setPreset(50000)"
                            :disabled="availableBalance < 50000 || isSubmitting"
                            class="rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-bold text-slate-700 hover:border-brand-maroon hover:text-brand-maroon hover:bg-rose-50 transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                        >
                            Rp 50.000
                        </button>
                        <button
                            type="button"
                            @click="setPreset(100000)"
                            :disabled="availableBalance < 100000 || isSubmitting"
                            class="rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-bold text-slate-700 hover:border-brand-maroon hover:text-brand-maroon hover:bg-rose-50 transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                        >
                            Rp 100.000
                        </button>
                        <button
                            type="button"
                            @click="setPreset('all')"
                            :disabled="availableBalance <= 0 || isSubmitting"
                            class="rounded-xl border border-amber-300 bg-amber-50 px-3.5 py-1.5 text-xs font-black text-amber-900 hover:bg-amber-100 transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                        >
                            ⚡ Tarik Semua (Rp{{ availableBalance.toLocaleString('id-ID') }})
                        </button>
                    </div>

                    <!-- Pesan Error Validasi -->
                    <p v-if="errorMessage" class="text-xs font-bold text-rose-600 flex items-center gap-1.5 animate-in fade-in">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ errorMessage }}</span>
                    </p>
                </div>

                <!-- Tombol Konfirmasi Aksi -->
                <div class="pt-2">
                    <button
                        type="button"
                        @click="handleConfirmWithdrawal"
                        :disabled="isSubmitting || availableBalance < 20000"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-brand-maroon to-[#8E1032] py-4 text-sm font-black text-white shadow-lg hover:opacity-95 hover:shadow-xl transition active:scale-98 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <svg
                            v-if="isSubmitting"
                            class="h-5 w-5 animate-spin text-white"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span v-if="isSubmitting">Memproses Pengajuan ke Bank Mandiri...</span>
                        <span v-else>Ajukan Pencairan Sekarang (Rp{{ (withdrawalAmount || 0).toLocaleString('id-ID') }})</span>
                    </button>
                    <p class="text-[10px] text-slate-400 text-center mt-2">
                        Pencairan diproses secara aman ke rekening resmi mitra terdaftar tanpa potongan perantara.
                    </p>
                </div>

                <!-- Riwayat Mutasi Penarikan Sederhana untuk Demo -->
                <div class="border-t border-slate-100 pt-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Riwayat Mutasi Pencairan Terkini
                        </h3>
                        <span class="text-[10px] font-bold text-slate-400">Sinkron Otomatis</span>
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="item in payoutHistory.slice(0, 4)"
                            :key="item.id"
                            class="rounded-xl p-3 border border-slate-200/80 bg-white flex items-center justify-between shadow-2xs hover:border-slate-300 transition"
                        >
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-extrabold text-slate-900">Rp{{ item.amount.toLocaleString('id-ID') }}</span>
                                    <span class="text-[10px] font-mono text-slate-400">({{ item.id }})</span>
                                </div>
                                <p class="text-[10px] text-slate-500">
                                    {{ item.bankName }} · {{ item.date }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-black"
                                    :class="item.status === 'selesai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200'"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full" :class="item.status === 'selesai' ? 'bg-emerald-600' : 'bg-amber-500 animate-pulse'"></span>
                                    {{ item.statusLabel }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </Teleport>
</template>
