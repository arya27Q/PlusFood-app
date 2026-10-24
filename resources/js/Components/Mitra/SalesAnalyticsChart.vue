<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    chartData: {
        type: Array,
        default: () => [],
    },
    orders: {
        type: Array,
        default: () => [],
    },
    merchant: {
        type: Object,
        default: () => ({}),
    },
    metrics: {
        type: Object,
        default: () => ({}),
    },
    ecoPercent: {
        type: Number,
        default: 0,
    },
});

// Filter metrik yang dipilih mitra
const activeMetric = ref('porsi'); // 'porsi', 'pendapatan', 'lingkungan'
const activePeriod = ref('7hari'); // '7hari', 'bulan'

function generateDefault7Days() {
    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const dayShorts = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    const result = [];
    const now = new Date();

    for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setDate(now.getDate() - i);
        const dayIdx = d.getDay();
        const isToday = i === 0;

        result.push({
            day: isToday ? `${dayNames[dayIdx]} (Hari Ini)` : dayNames[dayIdx],
            short: dayShorts[dayIdx],
            date: `${d.getDate()} ${monthNames[d.getMonth()]}`,
            porsi: 0,
            totalListing: 1,
            revenue: 0,
            costSaved: 0,
            co2: 0,
            peakHour: '19.30 WIB',
            isToday: isToday,
        });
    }
    return result;
}

// Data harian (7 Hari Terakhir) dinamis riil
const dailyData = computed(() => {
    let base = props.chartData && props.chartData.length === 7 
        ? JSON.parse(JSON.stringify(props.chartData))
        : generateDefault7Days();

    // Reconcile secara reaktif dengan props.orders
    if (props.orders && props.orders.length > 0) {
        const completed = props.orders.filter(o => o.status === 'selesai');
        const todayPorsi = completed.reduce((acc, o) => {
            return acc + (o.items ? o.items.reduce((sum, it) => sum + (Number(it.quantity) || 1), 0) : 1);
        }, 0);
        const todayRevenue = completed.reduce((acc, o) => acc + Number(o.total_price || o.total || 0), 0);
        const todayCostSaved = completed.reduce((acc, o) => acc + Number(o.money_saved || 0), 0);
        const todayWeight = completed.reduce((acc, o) => acc + Number(o.weight_saved_grams || 0), 0);
        const todayCo2 = Number((todayWeight > 0 ? (todayWeight / 1000 * 2.5) : (todayPorsi * 0.25)).toFixed(1));

        if (base.length > 0) {
            const todayItem = base[base.length - 1];
            todayItem.porsi = Math.max(todayItem.porsi, todayPorsi);
            todayItem.revenue = Math.max(todayItem.revenue, todayRevenue);
            todayItem.costSaved = Math.max(todayItem.costSaved, todayCostSaved);
            todayItem.co2 = Math.max(todayItem.co2, todayCo2);
            todayItem.totalListing = Math.max(todayItem.totalListing, todayItem.porsi, 1);
            if (completed.length > 0) {
                todayItem.peakHour = completed[0]?.pickup_time || completed[0]?.pickupTime || '19.30 WIB';
            }
        }
    }

    return base;
});

const hoveredIndex = ref(6); // Default fokus ke hari ini
const activeHoveredData = computed(() => dailyData.value[hoveredIndex.value] || dailyData.value[dailyData.value.length - 1] || {});

// Statistik Akumulasi
const totalPorsiRescued = computed(() => dailyData.value.reduce((acc, curr) => acc + (curr.porsi || 0), 0));
const totalRevenue = computed(() => dailyData.value.reduce((acc, curr) => acc + (curr.revenue || 0), 0));
const totalCostSaved = computed(() => dailyData.value.reduce((acc, curr) => acc + (curr.costSaved || 0), 0));
const totalCo2 = computed(() => {
    const sum = dailyData.value.reduce((acc, curr) => acc + (curr.co2 || 0), 0);
    return sum.toFixed(1);
});
const rescueRateWeekly = computed(() => {
    const listing = dailyData.value.reduce((acc, curr) => acc + (curr.totalListing || 0), 0);
    if (listing <= 0 || totalPorsiRescued.value <= 0) return 0;
    return Math.min(100, Math.round((totalPorsiRescued.value / listing) * 100));
});

// SVG Chart Dimension & Calculations
const svgWidth = 620;
const svgHeight = 220;
const paddingX = 40;
const paddingY = 30;

// Menentukan nilai max berdasarkan metrik aktif (aman dari pembagian nol)
const maxValue = computed(() => {
    if (activeMetric.value === 'porsi') {
        const max = Math.max(1, ...dailyData.value.map(d => Math.max(d.totalListing || 0, d.porsi || 0)));
        return Math.ceil(max * 1.15);
    }
    if (activeMetric.value === 'pendapatan') {
        const max = Math.max(10000, ...dailyData.value.map(d => d.revenue || 0));
        return Math.ceil(max * 1.15);
    }
    // lingkungan (co2)
    const max = Math.max(1, ...dailyData.value.map(d => d.co2 || 0));
    return Math.ceil(max * 1.2);
});

// Koordinat Titik untuk Line / Curve
const points = computed(() => {
    const stepX = (svgWidth - paddingX * 2) / (dailyData.value.length - 1);
    return dailyData.value.map((d, index) => {
        const val = activeMetric.value === 'porsi' 
            ? d.porsi 
            : activeMetric.value === 'pendapatan' 
                ? d.revenue 
                : d.co2;
        
        const x = paddingX + index * stepX;
        const y = svgHeight - paddingY - (val / maxValue.value) * (svgHeight - paddingY * 2);
        return { x, y, data: d, val };
    });
});

// Membangun Smooth Curved SVG Path (Cubic Bezier Spline)
const linePath = computed(() => {
    const pts = points.value;
    if (pts.length === 0) return '';
    let d = `M ${pts[0].x} ${pts[0].y}`;
    for (let i = 0; i < pts.length - 1; i++) {
        const p0 = i > 0 ? pts[i - 1] : pts[i];
        const p1 = pts[i];
        const p2 = pts[i + 1];
        const p3 = i != pts.length - 2 ? pts[i + 2] : p2;

        const cp1x = p1.x + (p2.x - p0.x) / 6;
        const cp1y = p1.y + (p2.y - p0.y) / 6;
        const cp2x = p2.x - (p3.x - p1.x) / 6;
        const cp2y = p2.y - (p3.y - p1.y) / 6;

        d += ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${p2.x} ${p2.y}`;
    }
    return d;
});

// Path Area Fill di Bawah Curve
const areaPath = computed(() => {
    const pts = points.value;
    if (pts.length === 0) return '';
    const bottomY = svgHeight - paddingY;
    const baseLine = linePath.value;
    return `${baseLine} L ${pts[pts.length - 1].x} ${bottomY} L ${pts[0].x} ${bottomY} Z`;
});
</script>

<template>
    <div class="rounded-[28px] bg-white border border-slate-200/90 p-6 sm:p-7 shadow-[0_4px_24px_-4px_rgba(0,0,0,0.04)] space-y-6">
        
        <!-- Header & Navigasi Metrik Grafik -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between border-b border-slate-100 pb-5">
            <div>
                <div class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-amber-100 text-amber-900 text-xs font-black shadow-2xs">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </span>
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight">
                        Grafik Pemulihan Modal & Penjualan Surplus
                    </h2>
                    <span class="rounded-full bg-emerald-50 text-emerald-800 text-[10px] font-extrabold px-2.5 py-0.5 border border-emerald-300">
                        Live 7 Hari
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Visualisasi porsi terselamatkan dari pembuangan dan pemulihan modal kasir warung Anda.
                </p>
            </div>

            <!-- Tab Selector Metrik -->
            <div class="flex flex-wrap items-center gap-2">
                <button
                    @click="activeMetric = 'porsi'"
                    class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition flex items-center gap-1.5"
                    :class="activeMetric === 'porsi' ? 'bg-brand-maroon text-white shadow-xs font-extrabold' : 'bg-slate-50 text-slate-600 border border-slate-200/80 hover:bg-slate-100 hover:text-slate-900'"
                >
                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Porsi Makanan</span>
                </button>
                <button
                    @click="activeMetric = 'pendapatan'"
                    class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition flex items-center gap-1.5"
                    :class="activeMetric === 'pendapatan' ? 'bg-brand-maroon text-white shadow-xs font-extrabold' : 'bg-slate-50 text-slate-600 border border-slate-200/80 hover:bg-slate-100 hover:text-slate-900'"
                >
                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Pemulihan Modal (Rp)</span>
                </button>
                <button
                    @click="activeMetric = 'lingkungan'"
                    class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition flex items-center gap-1.5"
                    :class="activeMetric === 'lingkungan' ? 'bg-brand-maroon text-white shadow-xs font-extrabold' : 'bg-slate-50 text-slate-600 border border-slate-200/80 hover:bg-slate-100 hover:text-slate-900'"
                >
                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Dampak CO2</span>
                </button>
            </div>
        </div>

        <!-- Konten Grafik & Sidebar Ringkasan -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            
            <!-- Kolom Kiri: Visual SVG Chart (Span 8) -->
            <div class="lg:col-span-8 space-y-3">
                <div class="relative w-full overflow-hidden rounded-2xl bg-gradient-to-b from-white via-slate-50/50 to-white border border-slate-200/90 p-3 sm:p-4 shadow-2xs">
                    
                    <!-- SVG Chart Container -->
                    <svg :viewBox="`0 0 ${svgWidth} ${svgHeight}`" class="w-full h-auto overflow-visible select-none">
                        <defs>
                            <!-- Gradient Area Maroon -->
                            <linearGradient id="maroonGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#7A0C2E" stop-opacity="0.30" />
                                <stop offset="100%" stop-color="#7A0C2E" stop-opacity="0.01" />
                            </linearGradient>

                            <!-- Gradient Area Emerald (Lingkungan) -->
                            <linearGradient id="emeraldGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#059669" stop-opacity="0.30" />
                                <stop offset="100%" stop-color="#059669" stop-opacity="0.01" />
                            </linearGradient>

                            <!-- Gradient Area Amber (Revenue) -->
                            <linearGradient id="amberGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#D97706" stop-opacity="0.30" />
                                <stop offset="100%" stop-color="#D97706" stop-opacity="0.01" />
                            </linearGradient>
                        </defs>

                        <!-- Horizontal Grid Lines -->
                        <g class="opacity-60">
                            <line :x1="paddingX" :y1="paddingY" :x2="svgWidth - paddingX" :y2="paddingY" stroke="#E2E8F0" stroke-dasharray="4 4" />
                            <line :x1="paddingX" :y1="svgHeight / 2" :x2="svgWidth - paddingX" :y2="svgHeight / 2" stroke="#E2E8F0" stroke-dasharray="4 4" />
                            <line :x1="paddingX" :y1="svgHeight - paddingY" :x2="svgWidth - paddingX" :y2="svgHeight - paddingY" stroke="#CBD5E1" stroke-width="1.2" />
                        </g>

                        <!-- Bar Background Listing Total (Hanya pada mode porsi) -->
                        <g v-if="activeMetric === 'porsi'">
                            <rect
                                v-for="(p, i) in points"
                                :key="'bar-' + i"
                                :x="p.x - 14"
                                :y="svgHeight - paddingY - (p.data.totalListing / maxValue) * (svgHeight - paddingY * 2)"
                                width="28"
                                :height="(p.data.totalListing / maxValue) * (svgHeight - paddingY * 2)"
                                rx="7"
                                class="fill-slate-100 hover:fill-slate-200/80 transition-all duration-300"
                            />
                        </g>

                        <!-- Area Fill -->
                        <path
                            :d="areaPath"
                            :fill="activeMetric === 'lingkungan' ? 'url(#emeraldGradient)' : activeMetric === 'pendapatan' ? 'url(#amberGradient)' : 'url(#maroonGradient)'"
                            class="transition-all duration-500 ease-out"
                        />

                        <!-- Line Stroke -->
                        <path
                            :d="linePath"
                            fill="none"
                            :stroke="activeMetric === 'lingkungan' ? '#059669' : activeMetric === 'pendapatan' ? '#D97706' : '#7A0C2E'"
                            stroke-width="3"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="transition-all duration-500 ease-out"
                        />

                        <!-- Data Points & Interactive Hover Circles -->
                        <g v-for="(p, i) in points" :key="'point-' + i">
                            <!-- Background invisible hit area for easy hovering -->
                            <rect
                                :x="p.x - 22"
                                :y="0"
                                width="44"
                                :height="svgHeight"
                                fill="transparent"
                                class="cursor-pointer"
                                @mouseenter="hoveredIndex = i"
                            />

                            <!-- Vertical guide line on active hover -->
                            <line
                                v-if="hoveredIndex === i"
                                :x1="p.x"
                                :y1="paddingY"
                                :x2="p.x"
                                :y2="svgHeight - paddingY"
                                stroke="#7A0C2E"
                                stroke-width="1.5"
                                stroke-dasharray="3 3"
                                opacity="0.6"
                            />

                            <!-- Outer circle pulse on hover -->
                            <circle
                                v-if="hoveredIndex === i"
                                :cx="p.x"
                                :cy="p.y"
                                r="9"
                                :fill="activeMetric === 'lingkungan' ? '#059669' : activeMetric === 'pendapatan' ? '#D97706' : '#7A0C2E'"
                                opacity="0.25"
                                class="animate-pulse"
                            />

                            <!-- Circle Node -->
                            <circle
                                :cx="p.x"
                                :cy="p.y"
                                :r="hoveredIndex === i ? 6 : 4"
                                :fill="hoveredIndex === i ? (activeMetric === 'lingkungan' ? '#059669' : activeMetric === 'pendapatan' ? '#D97706' : '#7A0C2E') : '#ffffff'"
                                :stroke="activeMetric === 'lingkungan' ? '#059669' : activeMetric === 'pendapatan' ? '#D97706' : '#7A0C2E'"
                                stroke-width="2.5"
                                class="transition-all duration-200 cursor-pointer shadow-sm"
                                @mouseenter="hoveredIndex = i"
                            />

                            <!-- Day Labels on X Axis -->
                            <text
                                :x="p.x"
                                :y="svgHeight - 10"
                                text-anchor="middle"
                                class="text-[10px] font-bold select-none cursor-pointer"
                                :class="hoveredIndex === i ? 'fill-brand-maroon font-black' : 'fill-slate-400'"
                                @mouseenter="hoveredIndex = i"
                            >
                                {{ p.data.short }}
                            </text>
                        </g>
                    </svg>

                    <!-- Interactive Tooltip Overlay Bar -->
                    <div class="mt-2.5 flex items-center justify-between rounded-xl bg-white p-3 border border-slate-200/90 text-xs shadow-xs">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full" :class="activeHoveredData.isToday ? 'bg-emerald-500 animate-ping' : 'bg-brand-maroon'"></span>
                            <span class="font-extrabold text-slate-900">{{ activeHoveredData.day }} ({{ activeHoveredData.date }})</span>
                            <span v-if="activeHoveredData.isToday" class="rounded bg-rose-50 text-brand-maroon text-[9px] font-black px-1.5 py-0.5 border border-rose-200/60">HARI INI</span>
                        </div>
                        <div class="flex items-center gap-4 text-xs">
                            <div>
                                <span class="text-slate-500">Porsi:</span>
                                <strong class="ml-1 text-slate-900 font-extrabold">{{ activeHoveredData.porsi }}/{{ activeHoveredData.totalListing }} Porsi</strong>
                            </div>
                            <div>
                                <span class="text-slate-500">Modal Pulih:</span>
                                <strong class="ml-1 text-emerald-700 font-extrabold">Rp{{ activeHoveredData.revenue.toLocaleString('id-ID') }}</strong>
                            </div>
                            <div class="hidden sm:block">
                                <span class="text-slate-500">Jam Ramai:</span>
                                <strong class="ml-1 text-amber-700 font-bold">{{ activeHoveredData.peakHour }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keterangan Legend Bawah -->
                <div class="flex items-center justify-between text-[11px] text-slate-500 px-1">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-brand-maroon"></span>
                            <span>Terselamatkan Mahasiswa</span>
                        </div>
                        <div v-if="activeMetric === 'porsi'" class="flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-sm bg-slate-300"></span>
                            <span>Total Porsi Listing</span>
                        </div>
                    </div>
                    <span>*Rata-rata 92% makanan surplus habis dalam 45 menit</span>
                </div>
            </div>

            <!-- Kolom Kanan: Kartu Ringkasan Finansial & Dampak (Span 4) -->
            <div class="lg:col-span-4 space-y-3.5">
                
                <!-- Card 1: Akumulasi Minggu Ini (Luminous Ruby Gradient Hero) -->
                <div class="rounded-2xl bg-gradient-to-br from-[#800C30] via-[#9B123C] to-[#BD194B] p-5 text-white shadow-lg shadow-rose-950/15 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-black tracking-wider text-rose-200">
                            Total Rekap 7 Hari
                        </span>
                        <span class="rounded-md bg-white/20 text-[10px] font-black px-2 py-0.5 border border-white/20">
                            Minggu Berjalan
                        </span>
                    </div>
                    <div>
                        <p class="text-3xl font-black tracking-tight text-white">
                            Rp{{ totalRevenue.toLocaleString('id-ID') }}
                        </p>
                        <p class="text-xs text-rose-100 font-medium mt-1">
                            Total modal terselamatkan dari masakan berlebih
                        </p>
                    </div>
                    <div class="pt-2.5 border-t border-white/20 flex items-center justify-between text-xs">
                        <span>Porsi Terjual: <strong class="font-bold">{{ totalPorsiRescued }} Porsi</strong></span>
                        <span class="text-emerald-300 font-extrabold bg-emerald-950/40 px-2 py-0.5 rounded border border-emerald-400/30">{{ rescueRateWeekly }}% Lolos Buang</span>
                    </div>
                </div>

                <!-- Card 2: Dampak Lingkungan & Sinkronisasi Mahasiswa -->
                <div class="rounded-2xl bg-white p-4 border border-slate-200/90 shadow-xs space-y-2.5">
                    <span class="text-[10px] uppercase font-black text-slate-500 tracking-wider block">
                        Dampak Lingkungan & Komunitas
                    </span>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200/80">
                            <p class="text-[10px] text-slate-500 font-semibold">CO2 Dicegah</p>
                            <p class="text-sm font-black text-emerald-700 mt-0.5">{{ totalCo2 }} kg</p>
                            <p class="text-[9px] text-slate-500">Setara 42 km motor</p>
                        </div>
                        <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200/80">
                            <p class="text-[10px] text-slate-500 font-semibold">Bawa Wadah</p>
                            <p class="text-sm font-black text-brand-maroon mt-0.5">{{ props.ecoPercent || 0 }}% Mahasiswa</p>
                            <p class="text-[9px] text-emerald-700 font-bold">Hemat sampah mika</p>
                        </div>
                    </div>
                    <div class="text-[11px] text-slate-600 font-medium pt-1 flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-brand-maroon shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span><strong class="text-slate-900">Sentra {{ merchant?.campus_name || 'UNESA Ketintang' }}:</strong> Menu warung Anda terhubung ke 1.200+ mahasiswa aktif sekitar kampus.</span>
                    </div>
                </div>

            </div>

        </div>

    </div>
</template>
