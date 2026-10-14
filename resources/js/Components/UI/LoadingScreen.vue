<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    // Jika true, komponen menjalankan animasi progress otomatis 0% - 100%
    autoAnimate: {
        type: Boolean,
        default: true,
    },
    // Durasi animasi otomatis dalam milidetik
    duration: {
        type: Number,
        default: 2000,
    },
    // Override manual nilai progress jika autoAnimate = false
    manualProgress: {
        type: Number,
        default: 0,
    },
    // Callback ketika loading selesai (progress mencapai 100%)
    onFinished: {
        type: Function,
        default: null,
    },
});

const emit = defineEmits(['finish']);

const progress = ref(props.autoAnimate ? 0 : props.manualProgress);
const currentStatus = ref('Memverifikasi akun mahasiswa...');

const statusSteps = [
    { at: 0, text: 'Memverifikasi akun mahasiswa...' },
    { at: 35, text: 'Mencari makanan surplus di sekitar Ketintang...' },
    { at: 75, text: 'Menyiapkan rekomendasi hemat...' },
    { at: 98, text: 'Selamat datang di PlusFood!' },
];

let timer = null;

onMounted(() => {
    if (props.autoAnimate) {
        const interval = 40;
        const totalSteps = props.duration / interval;
        let step = 0;

        timer = setInterval(() => {
            step++;
            const pct = Math.min(100, Math.round((step / totalSteps) * 100));
            progress.value = pct;

            // Update status text
            for (let i = statusSteps.length - 1; i >= 0; i--) {
                if (pct >= statusSteps[i].at) {
                    currentStatus.value = statusSteps[i].text;
                    break;
                }
            }

            if (pct >= 100) {
                clearInterval(timer);
                setTimeout(() => {
                    emit('finish');
                    if (props.onFinished) {
                        props.onFinished();
                    }
                }, 150);
            }
        }, interval);
    }
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>

<template>
    <div class="fixed inset-0 z-[9999] flex flex-col items-center justify-between bg-[#FAF7F2] p-8 text-center select-none animate-fade-in overflow-hidden">
        
        <!-- Top Spacer -->
        <div class="h-6"></div>

        <!-- Center Content: Orbit, Logo Card, Text, Progress -->
        <div class="flex flex-col items-center justify-center max-w-md w-full">
            
            <!-- Orbit Ring Container -->
            <div class="relative w-36 h-36 flex items-center justify-center mb-6">
                
                <!-- Rotating Orbit Border with Dots -->
                <div class="absolute inset-0 rounded-full border border-[#E8DFD8] animate-spin-orbit">
                    <!-- Top Dot -->
                    <span class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 rounded-full bg-brand-maroon shadow-xs ring-4 ring-rose-100"></span>
                    <!-- Bottom Dot -->
                    <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-brand-maroon/80"></span>
                </div>

                <!-- White Card Center -->
                <div class="relative z-10 w-20 h-20 rounded-2xl bg-white shadow-lg shadow-brand-maroon/5 border border-[#E8DFD8] flex items-center justify-center p-3 transition-transform duration-500 hover:scale-105">
                    <!-- Maroon Emblem with Bowl Logo -->
                    <div class="w-14 h-14 rounded-xl bg-brand-maroon flex items-center justify-center text-white shadow-xs">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Bowl -->
                            <path
                                d="M3 10C3 14.9706 7.02944 19 12 19C16.9706 19 21 14.9706 21 10H3Z"
                                fill="currentColor"
                            />
                            <!-- Plus Cross inside/above bowl -->
                            <path
                                d="M12 4V8M10 6H14"
                                stroke="#FAF7F2"
                                stroke-width="2.2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <!-- Base stand -->
                            <path
                                d="M8.5 20H15.5"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="text-3xl font-black tracking-tight text-brand-maroon">
                Plus Food
            </h1>
            <p class="text-xs sm:text-sm font-medium text-[#7A6662] mt-1.5 max-w-xs leading-relaxed">
                Food Rescue Kampus · selamatkan makanan, hemat kantong
            </p>

            <!-- Thin Progress Bar (Image 1 replica) -->
            <div class="w-64 sm:w-72 h-1.5 rounded-full bg-[#EAE0D5] overflow-hidden mt-6 shadow-inner">
                <div
                    class="h-full bg-brand-maroon rounded-full transition-all duration-150 ease-out"
                    :style="{ width: progress + '%' }"
                ></div>
            </div>

            <!-- Dynamic Status Message -->
            <p class="text-xs font-semibold text-[#8A7570] mt-3 tracking-wide h-4 transition-all duration-300">
                {{ currentStatus }}
            </p>
        </div>

        <!-- Bottom Footer (Image 1 replica) -->
        <div class="flex items-center justify-center gap-1.5 text-xs font-medium text-[#7A6662] pb-4">
            <span class="text-brand-maroon text-sm">🍂</span>
            <span>1.284 porsi diselamatkan minggu ini</span>
        </div>
    </div>
</template>

<style scoped>
@keyframes spin-orbit {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
.animate-spin-orbit {
    animation: spin-orbit 8s linear infinite;
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
