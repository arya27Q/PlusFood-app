<script setup>
import { computed } from 'vue';

const props = defineProps({
    icon: {
        type: String,
        default: '',
    },
    title: {
        type: String,
        default: '',
    },
});

// Deteksi tipe icon berdasarkan properti icon atau kata kunci di judul
const resolvedType = computed(() => {
    const rawIcon = (props.icon || '').toLowerCase();
    const t = (props.title || '').toLowerCase();

    if (rawIcon === 'rice' || rawIcon === 'bowl' || t.includes('nasi')) return 'rice';
    if (rawIcon === 'chicken' || rawIcon === 'meat' || t.includes('ayam') || t.includes('paha') || t.includes('dada') || t.includes('bakar') || t.includes('daging')) return 'chicken';
    if (rawIcon === 'sambal' || rawIcon === 'chili' || t.includes('sambal') || t.includes('cabe') || t.includes('rawit') || t.includes('pedas')) return 'sambal';
    if (rawIcon === 'salad' || rawIcon === 'leaf' || t.includes('lalap') || t.includes('timun') || t.includes('kemangi') || t.includes('urab') || t.includes('sayur') || t.includes('mentimun') || t.includes('acar')) return 'salad';
    if (rawIcon === 'tempeh' || t.includes('tempe') || t.includes('tahu') || t.includes('crispy')) return 'tempeh';
    if (rawIcon === 'soup' || t.includes('soto') || t.includes('kuah') || t.includes('sop')) return 'soup';
    if (rawIcon === 'noodles' || t.includes('mie') || t.includes('bakmi') || t.includes('bihun')) return 'noodles';
    if (rawIcon === 'bread' || t.includes('roti') || t.includes('sobek') || t.includes('butter')) return 'bread';
    if (rawIcon === 'egg' || t.includes('telur')) return 'egg';
    if (rawIcon === 'cracker' || t.includes('kerupuk') || t.includes('koya') || t.includes('rempeyek')) return 'cracker';
    if (rawIcon === 'cheese' || t.includes('keju') || t.includes('cokelat')) return 'cheese';
    if (rawIcon === 'snack' || t.includes('gorengan') || t.includes('mendoan') || t.includes('bakwan')) return 'snack';
    if (rawIcon === 'sauce' || t.includes('petis') || t.includes('saus') || t.includes('bumbu')) return 'sauce';

    return 'default';
});
</script>

<template>
    <!-- 1. NASI (Warm Rice Bowl with Grains & Steam) -->
    <svg
        v-if="resolvedType === 'rice'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M4 11h16a1 1 0 0 1 1 1c0 5-3.6 9-9 9s-9-4-9-9a1 1 0 0 1 1-1z" fill="currentColor" fill-opacity="0.18" />
        <path d="M4 11h16" />
        <path d="M7 21h10" />
        <path d="M8 7.5c0-1.5 1-2.5 1-3.5" />
        <path d="M12 7c0-1.5 1-2.5 1-3.5" />
        <path d="M16 7.5c0-1.5 1-2.5 1-3.5" />
        <circle cx="8.5" cy="11" r="0.6" fill="currentColor" />
        <circle cx="12" cy="10" r="0.6" fill="currentColor" />
        <circle cx="15.5" cy="11" r="0.6" fill="currentColor" />
    </svg>

    <!-- 2. AYAM / DAGING BAKAR (Roasted Poultry Drumstick with Grill Stripes) -->
    <svg
        v-else-if="resolvedType === 'chicken'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path
            d="M16.2 3.8c2.4 2.4 2.6 6.3.5 8.7-1.8 2-4.6 2.6-7 1.4l-2.8 2.8c-.6.6-1.5.6-2.1 0s-.6-1.5 0-2.1l2.8-2.8C6.4 9.4 7 6.6 9 4.9c2.4-2 6.3-1.8 7.2-1.1z"
            fill="currentColor"
            fill-opacity="0.18"
        />
        <circle cx="4.2" cy="18.8" r="1.4" />
        <circle cx="5.2" cy="19.8" r="1.4" />
        <line x1="11.5" y1="7" x2="14" y2="9.5" />
        <line x1="13.5" y1="5.5" x2="15.5" y2="7.5" />
    </svg>

    <!-- 3. SAMBAL TERASI / CABE (Hot Chili Pepper with Spice Flare) -->
    <svg
        v-else-if="resolvedType === 'sambal'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path
            d="M17.8 4.2c-3.2 1.3-5.2 4.4-6.2 7.5-1.5 4.4-4.8 7.5-8.3 7.5-.6 0-1-.5-.8-1.1 1.5-4.4 4.4-7.8 7.5-10.2 2.4-1.9 5.4-2.9 7.8-2.7z"
            fill="currentColor"
            fill-opacity="0.18"
        />
        <path d="M17.5 4.5c1.2-.8 2.4-1.2 3.8-1.2" />
        <path d="M15 6.5c-.8.8-1.6 2.1-2 3.4" />
        <path d="M18.8 12.2c.4.8 1.1 1.2 1.1 2 0 1-.8 1.8-1.8 1.8s-1.8-.8-1.8-1.8c0-.8.7-1.2 1.1-2z" fill="currentColor" stroke="none" />
    </svg>

    <!-- 4. LALAPAN / SAYUR / DAUN (Fresh Herb Leaf & Cucumber Slice) -->
    <svg
        v-else-if="resolvedType === 'salad'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path
            d="M20.5 3.5c-6.4 0-11.8 4.8-12.8 11.2-.5 3.1 1.5 5.1 4.6 5.1 6.4-1 11.3-6.4 11.3-12.8-.5-1.7-1.7-3-3.1-3.5z"
            fill="currentColor"
            fill-opacity="0.18"
        />
        <path d="M8 14.5c3.6-.9 7.2-4.3 9.8-8.8" />
        <path d="M3.5 20.5c1.8-2.6 4.2-4.8 4.2-4.8" />
        <circle cx="5" cy="8" r="2.2" stroke-dasharray="1 1.5" />
    </svg>

    <!-- 5. TEMPE / TAHU CRISPY (Crispy Soybean Tempeh Cutlet with Grain Cuts) -->
    <svg
        v-else-if="resolvedType === 'tempeh'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <rect
            x="3.5"
            y="6.5"
            width="17"
            height="11"
            rx="3"
            transform="rotate(-4 12 12)"
            fill="currentColor"
            fill-opacity="0.18"
        />
        <path d="M7 9l1.5 6" />
        <path d="M11 8.5l1.5 6" />
        <path d="M15 8.5l1.5 6" />
        <circle cx="8" cy="11.5" r="0.7" fill="currentColor" />
        <circle cx="12" cy="11.5" r="0.7" fill="currentColor" />
        <circle cx="16" cy="11.5" r="0.7" fill="currentColor" />
    </svg>

    <!-- 6. SOTO / KUAH (Steaming Soup Broth) -->
    <svg
        v-else-if="resolvedType === 'soup'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M3 11c0 5 4 9 9 9s9-4 9-9H3z" fill="currentColor" fill-opacity="0.18" />
        <path d="M7 20h10" />
        <path d="M18 4l-4 7" />
        <path d="M8 7c0-1.5 1-2.5 1-3.5" />
        <path d="M12 7c0-1.5 1-2.5 1-3.5" />
    </svg>

    <!-- 7. MIE / BAKMI (Noodles & Chopsticks) -->
    <svg
        v-else-if="resolvedType === 'noodles'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M4 12h16c0 4.5-3.6 8-8 8s-8-3.5-8-8z" fill="currentColor" fill-opacity="0.18" />
        <path d="M3 5l18 3" />
        <path d="M3 8l18 3" />
        <path d="M7 11v-2c0-1 1-2 2-2s2 1 2 2v2" />
        <path d="M13 11v-2c0-1 1-2 2-2s2 1 2 2v2" />
    </svg>

    <!-- 8. ROTI / BAKERY (Soft Bakery Bread Loaf) -->
    <svg
        v-else-if="resolvedType === 'bread'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path
            d="M5 16V9a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2z"
            fill="currentColor"
            fill-opacity="0.18"
        />
        <path d="M9 8c.5 1 1 2 2 2" />
        <path d="M13 8c.5 1 1 2 2 2" />
    </svg>

    <!-- 9. TELUR (Boiled Egg Halves) -->
    <svg
        v-else-if="resolvedType === 'egg'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M12 3C8.5 3 6 7 6 13a6 6 0 0 0 12 0c0-6-2.5-10-6-10z" fill="currentColor" fill-opacity="0.18" />
        <circle cx="12" cy="14" r="3" fill="currentColor" fill-opacity="0.35" />
    </svg>

    <!-- 10. KERUPUK / KOYA (Crispy Cracker / Seasoning) -->
    <svg
        v-else-if="resolvedType === 'cracker'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <circle cx="12" cy="12" r="8" fill="currentColor" fill-opacity="0.18" />
        <path d="M8 12c1-2 2-2 4 0s3 2 4 0" stroke-dasharray="2 1.5" />
        <circle cx="10" cy="9" r="0.9" fill="currentColor" />
        <circle cx="14" cy="15" r="0.9" fill="currentColor" />
    </svg>

    <!-- 11. KEJU & COKELAT (Cheese / Chocolate) -->
    <svg
        v-else-if="resolvedType === 'cheese'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M4 11l14-6 2 2v10a2 2 0 0 1-2 2H4V11z" fill="currentColor" fill-opacity="0.18" />
        <circle cx="10" cy="14" r="1.5" fill="currentColor" fill-opacity="0.4" />
        <circle cx="15" cy="11" r="1" fill="currentColor" fill-opacity="0.4" />
    </svg>

    <!-- 12. GORENGAN / SNACK (Crispy Fritters) -->
    <svg
        v-else-if="resolvedType === 'snack'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <polygon points="12 3 19 8 16 19 8 19 5 8" fill="currentColor" fill-opacity="0.18" />
        <line x1="9" y1="12" x2="15" y2="12" />
        <line x1="10" y1="15" x2="14" y2="15" />
    </svg>

    <!-- 13. SAUS / PETIS (Condiment Ramekin) -->
    <svg
        v-else-if="resolvedType === 'sauce'"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M6 10h12l-1.5 8a2 2 0 0 1-2 1.8h-5a2 2 0 0 1-2-1.8L6 10z" fill="currentColor" fill-opacity="0.18" />
        <path d="M5 10h14" />
        <path d="M12 6v4" />
        <circle cx="12" cy="5" r="1.2" fill="currentColor" />
    </svg>

    <!-- DEFAULT (Curated Dinner Plate) -->
    <svg
        v-else
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <circle cx="12" cy="12" r="9" fill="currentColor" fill-opacity="0.18" />
        <path d="M8 8v8" />
        <path d="M7 8h2" />
        <path d="M16 8v8" />
        <path d="M15 8h2" />
    </svg>
</template>
