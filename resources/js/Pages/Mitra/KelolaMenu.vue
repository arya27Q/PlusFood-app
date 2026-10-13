<script setup>
import { Head } from '@inertiajs/vue3';
import MitraLayout from '@/Layouts/MitraLayout.vue';
import { sampleFoods } from '@/Utils/mockFoods';
import { ref, computed } from 'vue';

// Listing lokal yang aktif untuk gerai Warung Bu Sari
const activeListings = ref([
    {
        id: 101,
        name: 'Nasi Ayam Bakar Bumbu Rujak',
        category: 'Nasi',
        priceOriginal: 25000,
        priceDiscount: 12000,
        stock: 3,
        pickupStart: '19.30',
        pickupEnd: '20.30',
        status: 'aktif', // 'aktif', 'habis'
        tags: ['Halal', 'Pedas Sedang'],
        image: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=500&q=80'
    },
    {
        id: 102,
        name: 'Sop Ayam & Sayur Segar Warung',
        category: 'Soto',
        priceOriginal: 18000,
        priceDiscount: 9000,
        stock: 2,
        pickupStart: '19.00',
        pickupEnd: '20.30',
        status: 'aktif',
        tags: ['Halal', 'Bebas Kacang'],
        image: 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=500&q=80'
    }
]);

// Form Model Input Kilat (< 1 Menit)
const form = ref({
    name: '',
    category: 'Nasi',
    priceOriginal: 22000,
    priceDiscount: 11000,
    estimatedCost: 8000, // modal pokok pembuatan
    stock: 4,
    pickupStart: '19.00',
    pickupEnd: '20.30',
    tags: ['Halal'],
    image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80'
});

const isPublished = ref(false);

// Kalkulasi Diskon & Smart Recovery Assistant Sesuai PRD
const discountPercent = computed(() => {
    if (!form.value.priceOriginal || form.value.priceOriginal <= 0) return 0;
    const diff = form.value.priceOriginal - form.value.priceDiscount;
    return Math.max(0, Math.round((diff / form.value.priceOriginal) * 100));
});

const capitalRecoveryRate = computed(() => {
    if (!form.value.estimatedCost || form.value.estimatedCost <= 0) return 100;
    // Berapa persen modal yang tertutupi oleh harga diskon
    return Math.round((form.value.priceDiscount / form.value.estimatedCost) * 100);
});

// Form Guard Validasi Ketat
const isFormValid = computed(() => {
    return form.value.name.trim() !== '' &&
           form.value.priceDiscount > 0 &&
           form.value.priceDiscount < form.value.priceOriginal &&
           form.value.stock > 0;
});

const presetMenus = [
    { name: 'Nasi Ayam Geprek Sambal Korek', cat: 'Nasi', orig: 20000, disc: 10000, cost: 8000 },
    { name: 'Nasi Pecel Telur Dadar Madiun', cat: 'Nasi', orig: 16000, disc: 8000, cost: 6000 },
    { name: 'Soto Daging Santan Gurih', cat: 'Soto', orig: 24000, disc: 12000, cost: 9000 },
    { name: 'Roti Bakar Bandung Cokelat Keju', cat: 'Roti', orig: 18000, disc: 9000, cost: 7000 },
    { name: 'Aneka Gorengan Campur (Isi 6)', cat: 'Camilan', orig: 12000, disc: 6000, cost: 4000 }
];

const applyPreset = (preset) => {
    form.value.name = preset.name;
    form.value.category = preset.cat;
    form.value.priceOriginal = preset.orig;
    form.value.priceDiscount = preset.disc;
    form.value.estimatedCost = preset.cost;
};

const submitListing = () => {
    if (!isFormValid.value) return;

    activeListings.value.unshift({
        id: Date.now(),
        name: form.value.name,
        category: form.value.category,
        priceOriginal: Number(form.value.priceOriginal),
        priceDiscount: Number(form.value.priceDiscount),
        stock: Number(form.value.stock),
        pickupStart: form.value.pickupStart,
        pickupEnd: form.value.pickupEnd,
        status: 'aktif',
        tags: [...form.value.tags],
        image: form.value.image
    });

    isPublished.value = true;
    setTimeout(() => {
        isPublished.value = false;
        form.value.name = '';
    }, 2500);
};

const toggleStatus = (item) => {
    item.status = item.status === 'aktif' ? 'habis' : 'aktif';
};

const adjustStock = (item, delta) => {
    const next = item.stock + delta;
    if (next >= 0) {
        item.stock = next;
        if (next === 0) item.status = 'habis';
        else item.status = 'aktif';
    }
};
</script>

<template>
    <MitraLayout>
        <Head title="Kelola Menu Surplus Kilat - PlusFood Mitra" />

        <div class="space-y-7">
            
            <!-- Header Halaman -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-black text-[#2D1B1E]">Kelola & Input Surplus Kilat</h1>
                    <p class="text-xs sm:text-sm text-[#6E5A54] mt-0.5">
                        Posting sisa makanan layak konsumsi dalam kurang dari 1 menit saat jam tutup toko.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 px-3.5 py-1.5 text-xs font-bold">
                        {{ activeListings.filter(l => l.status === 'aktif').length }} Menu Sedang Tayang
                    </span>
                </div>
            </div>

            <!-- Toast Sukses Posting -->
            <div v-if="isPublished" class="rounded-2xl bg-emerald-600 p-4 text-white shadow-lg flex items-center justify-between animate-in fade-in zoom-in-95">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <p class="text-sm font-extrabold">Listing Berhasil Dipublikasikan!</p>
                        <p class="text-xs text-white/90">Mahasiswa di sekitar kampus kini sudah bisa melihat dan mereservasi menu ini.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- ================= SISI KIRI: FORMULIR INPUT KILAT (Span 7) ================= -->
                <div class="lg:col-span-7 rounded-[26px] bg-white p-6 sm:p-7 border border-[#EAE0D5] shadow-sm space-y-6">
                    <div class="border-b border-[#F5EFE6] pb-3">
                        <span class="rounded bg-rose-50 px-2 py-0.5 text-[10px] font-black text-brand-maroon uppercase">
                            Input Singkat &lt; 1 Menit
                        </span>
                        <h2 class="text-lg font-black text-[#2D1B1E] mt-1">Formulir Menu Surplus Baru</h2>
                        <p class="text-xs text-[#8C7A72]">Isi field penting saja untuk mempercepat rilis flash sale malam.</p>
                    </div>

                    <!-- Pilihan Cepat Preset Menu -->
                    <div>
                        <label class="block text-xs font-bold text-[#2D1B1E] mb-2">Preset Cepat (Klik untuk Mengisi Otomatis):</label>
                        <div class="flex flex-wrap gap-1.5">
                            <button
                                v-for="p in presetMenus"
                                :key="p.name"
                                type="button"
                                @click="applyPreset(p)"
                                class="rounded-lg bg-[#FAF7F2] hover:bg-[#F5EFE6] border border-[#EAE0D5] px-2.5 py-1 text-[11px] font-semibold text-[#6E5A54] transition"
                            >
                                + {{ p.name }}
                            </button>
                        </div>
                    </div>

                    <form @submit.prevent="submitListing" class="space-y-4">
                        
                        <!-- 1. Nama Menu & Kategori -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1">Nama Makanan Surplus *</label>
                                <input
                                    type="text"
                                    v-model="form.name"
                                    required
                                    placeholder="Contoh: Nasi Ayam Geprek Crispy"
                                    class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] p-2.5 text-xs font-bold text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1">Kategori</label>
                                <select
                                    v-model="form.category"
                                    class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] p-2.5 text-xs font-bold text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none"
                                >
                                    <option value="Nasi">Nasi / Berat</option>
                                    <option value="Soto">Soto / Kuah</option>
                                    <option value="Roti">Roti / Pastry</option>
                                    <option value="Camilan">Camilan</option>
                                    <option value="Minuman">Minuman</option>
                                </select>
                            </div>
                        </div>

                        <!-- 2. Harga Asli, Harga Diskon & Modal Pokok -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1">Harga Normal (Rp) *</label>
                                <input
                                    type="number"
                                    v-model="form.priceOriginal"
                                    step="1000"
                                    required
                                    class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] p-2.5 text-xs font-bold text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1">Harga Diskon (Rp) *</label>
                                <input
                                    type="number"
                                    v-model="form.priceDiscount"
                                    step="1000"
                                    required
                                    class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] p-2.5 text-xs font-bold text-brand-maroon focus:border-brand-maroon focus:bg-white focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1">Perkiraan Modal/Porsi</label>
                                <input
                                    type="number"
                                    v-model="form.estimatedCost"
                                    step="500"
                                    class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] p-2.5 text-xs font-bold text-[#6E5A54] focus:border-brand-maroon focus:bg-white focus:outline-none"
                                />
                            </div>
                        </div>

                        <!-- Kalkulator Pemulihan Modal (Smart Assistant Card) -->
                        <div class="rounded-2xl bg-amber-50/70 p-4 border border-amber-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <span class="text-[10px] font-black uppercase text-amber-800 tracking-wider">
                                    Smart Break-Even Assistant
                                </span>
                                <p class="text-xs font-bold text-[#2D1B1E] mt-0.5">
                                    Diskon <strong class="text-rose-700 font-black">{{ discountPercent }}%</strong> · Pemulihan Modal: <strong class="text-emerald-700 font-black">{{ capitalRecoveryRate }}%</strong>
                                </p>
                                <p class="text-[11px] text-[#6E5A54] mt-0.5">
                                    {{ capitalRecoveryRate >= 100 ? '✅ Modal aman 100% dan Anda masih mendapatkan margin tipis!' : '⚠️ Harga diskon menutup ' + capitalRecoveryRate + '% dari biaya bahan dasar Anda.' }}
                                </p>
                            </div>
                            <span class="rounded-xl bg-amber-400 text-stone-900 px-3 py-1.5 text-xs font-black self-start sm:self-center shadow-xs">
                                {{ discountPercent }}% OFF
                            </span>
                        </div>

                        <!-- 3. Sisa Porsi & Jam Pengambilan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1">Sisa Porsi (Stok) *</label>
                                <input
                                    type="number"
                                    v-model="form.stock"
                                    min="1"
                                    max="50"
                                    required
                                    class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] p-2.5 text-xs font-bold text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1">Jam Mulai Ambil</label>
                                <input
                                    type="text"
                                    v-model="form.pickupStart"
                                    class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] p-2.5 text-xs font-bold text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#2D1B1E] mb-1">Batas Jam Ambil</label>
                                <input
                                    type="text"
                                    v-model="form.pickupEnd"
                                    class="w-full rounded-xl border border-[#EAE0D5] bg-[#FAF7F2] p-2.5 text-xs font-bold text-[#2D1B1E] focus:border-brand-maroon focus:bg-white focus:outline-none"
                                />
                            </div>
                        </div>

                        <!-- 4. Dietary Tag Checkboxes Sesuai PRD -->
                        <div>
                            <label class="block text-xs font-bold text-[#2D1B1E] mb-1.5">Dietary & Allergen Tags:</label>
                            <div class="flex flex-wrap gap-3 text-xs">
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" value="Halal" v-model="form.tags" class="rounded text-brand-maroon focus:ring-brand-maroon" />
                                    <span>Halal Tersertifikasi</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" value="Bebas Kacang" v-model="form.tags" class="rounded text-brand-maroon focus:ring-brand-maroon" />
                                    <span>Bebas Kacang</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" value="Bebas Gluten" v-model="form.tags" class="rounded text-brand-maroon focus:ring-brand-maroon" />
                                    <span>Bebas Gluten</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" value="Vegetarian" v-model="form.tags" class="rounded text-brand-maroon focus:ring-brand-maroon" />
                                    <span>Vegetarian</span>
                                </label>
                            </div>
                        </div>

                        <!-- Tombol Submit Publikasi -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="!isFormValid"
                                class="w-full rounded-xl bg-brand-maroon py-3.5 text-xs font-black text-white shadow-md hover:bg-brand-maroon-hover transition disabled:opacity-40 disabled:cursor-not-allowed"
                            >
                                Publikasikan Surplus Sekarang ({{ form.stock }} Porsi)
                            </button>
                            <p v-if="!isFormValid" class="text-[10px] text-rose-600 mt-1.5 text-center">
                                * Pastikan nama menu terisi, harga diskon lebih rendah dari harga asli, dan porsi &gt; 0.
                            </p>
                        </div>
                    </form>
                </div>

                <!-- ================= SISI KANAN: DAFTAR LISTING AKTIF HARI INI (Span 5) ================= -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="rounded-[26px] bg-white p-6 border border-[#EAE0D5] shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-[#F5EFE6]">
                            <h2 class="text-base font-black text-[#2D1B1E]">Daftar Listing Aktif Hari Ini</h2>
                            <span class="text-xs text-[#8C7A72] font-semibold">{{ activeListings.length }} Menu</span>
                        </div>

                        <!-- List Items -->
                        <div class="space-y-3">
                            <div
                                v-for="item in activeListings"
                                :key="item.id"
                                class="rounded-2xl p-4 border border-[#EAE0D5] bg-[#FAF7F2] space-y-3 transition"
                                :class="{ 'opacity-60': item.status === 'habis' }"
                            >
                                <div class="flex items-start gap-3">
                                    <img :src="item.image" :alt="item.name" class="h-14 w-16 rounded-xl object-cover bg-white shrink-0 border border-[#EAE0D5]" />
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <span class="rounded bg-rose-50 text-rose-700 text-[10px] font-black px-1.5 py-0.2">
                                                {{ item.category }}
                                            </span>
                                            <button
                                                @click="toggleStatus(item)"
                                                class="rounded-full px-2 py-0.5 text-[10px] font-extrabold transition"
                                                :class="item.status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-700'"
                                            >
                                                {{ item.status === 'aktif' ? '🟢 TAYANG' : '🔴 HABIS' }}
                                            </button>
                                        </div>
                                        <h3 class="text-xs font-black text-[#2D1B1E] truncate mt-1">{{ item.name }}</h3>
                                        <p class="text-[11px] font-bold text-brand-maroon mt-0.5">
                                            Rp{{ item.priceDiscount.toLocaleString('id-ID') }}
                                            <span class="text-[10px] text-[#9E8B83] line-through font-normal ml-1">Rp{{ item.priceOriginal.toLocaleString('id-ID') }}</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Stepper Stok Cepat & Rentang Waktu -->
                                <div class="flex items-center justify-between pt-2 border-t border-[#EAE0D5] text-xs">
                                    <span class="text-[10px] text-[#8C7A72]">
                                        ⏱ Ambil: {{ item.pickupStart }}–{{ item.pickupEnd }}
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[11px] font-bold text-[#6E5A54]">Sisa:</span>
                                        <button
                                            @click="adjustStock(item, -1)"
                                            class="h-6 w-6 rounded-md bg-white border border-[#EAE0D5] font-black text-gray-700 text-xs hover:bg-[#F5EFE6]"
                                        >
                                            -
                                        </button>
                                        <span class="font-extrabold text-sm text-[#2D1B1E] px-1">{{ item.stock }}</span>
                                        <button
                                            @click="adjustStock(item, 1)"
                                            class="h-6 w-6 rounded-md bg-white border border-[#EAE0D5] font-black text-gray-700 text-xs hover:bg-[#F5EFE6]"
                                        >
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </MitraLayout>
</template>
