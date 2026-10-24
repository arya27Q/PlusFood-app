import { ref, computed } from 'vue';
import { sampleFoods, getCustomFoods } from '@/Utils/mockFoods';

export function useDetailProduk(props) {
    const getCardTheme = (pkg) => {
        const raw = (pkg.icon || '').toLowerCase();
        const t = (pkg.title || '').toLowerCase();

        if (raw === 'rice' || raw === 'bowl' || t.includes('nasi')) {
            return {
                card: 'border-2 border-amber-300 bg-amber-50/70 hover:bg-amber-50 hover:border-amber-400',
                badge: 'bg-amber-100/90 text-amber-800 border border-amber-300/80',
            };
        }
        if (raw === 'chicken' || raw === 'meat' || t.includes('ayam') || t.includes('paha') || t.includes('dada') || t.includes('bakar') || t.includes('daging')) {
            return {
                card: 'border-2 border-rose-300 bg-rose-50/70 hover:bg-rose-50 hover:border-rose-400',
                badge: 'bg-rose-100/90 text-rose-700 border border-rose-300/80',
            };
        }
        if (raw === 'sambal' || raw === 'chili' || t.includes('sambal') || t.includes('cabe') || t.includes('rawit') || t.includes('pedas')) {
            return {
                card: 'border-2 border-red-300 bg-red-50/70 hover:bg-red-50 hover:border-red-400',
                badge: 'bg-red-100/90 text-red-700 border border-red-300/80',
            };
        }
        if (raw === 'salad' || raw === 'leaf' || t.includes('lalap') || t.includes('timun') || t.includes('kemangi') || t.includes('urab') || t.includes('sayur') || t.includes('mentimun') || t.includes('acar')) {
            return {
                card: 'border-2 border-emerald-300 bg-emerald-50/70 hover:bg-emerald-50 hover:border-emerald-400',
                badge: 'bg-emerald-100/90 text-emerald-800 border border-emerald-300/80',
            };
        }
        if (raw === 'tempeh' || t.includes('tempe') || t.includes('tahu') || t.includes('crispy')) {
            return {
                card: 'border-2 border-orange-300 bg-orange-50/70 hover:bg-orange-50 hover:border-orange-400',
                badge: 'bg-orange-100/90 text-orange-800 border border-orange-300/80',
            };
        }
        if (raw === 'soup' || t.includes('soto') || t.includes('kuah') || t.includes('sop')) {
            return {
                card: 'border-2 border-yellow-300 bg-yellow-50/70 hover:bg-yellow-50 hover:border-yellow-400',
                badge: 'bg-yellow-100/90 text-yellow-800 border border-yellow-300/80',
            };
        }
        if (raw === 'noodles' || t.includes('mie') || t.includes('bakmi') || t.includes('bihun')) {
            return {
                card: 'border-2 border-orange-300 bg-orange-50/70 hover:bg-orange-50 hover:border-orange-400',
                badge: 'bg-orange-100/90 text-orange-800 border border-orange-300/80',
            };
        }
        if (raw === 'bread' || t.includes('roti') || t.includes('sobek') || t.includes('butter')) {
            return {
                card: 'border-2 border-amber-300 bg-amber-50/70 hover:bg-amber-50 hover:border-amber-400',
                badge: 'bg-amber-100/90 text-amber-900 border border-amber-300/80',
            };
        }
        if (raw === 'egg' || t.includes('telur')) {
            return {
                card: 'border-2 border-amber-300 bg-amber-50/70 hover:bg-amber-50 hover:border-amber-400',
                badge: 'bg-yellow-100/90 text-amber-800 border border-yellow-300/80',
            };
        }
        if (raw === 'cracker' || t.includes('koya') || t.includes('kerupuk') || t.includes('rempeyek')) {
            return {
                card: 'border-2 border-orange-300 bg-orange-50/70 hover:bg-orange-50 hover:border-orange-400',
                badge: 'bg-orange-100/90 text-orange-800 border border-orange-300/80',
            };
        }
        if (raw === 'cheese' || t.includes('keju') || t.includes('cokelat')) {
            return {
                card: 'border-2 border-amber-300 bg-amber-50/70 hover:bg-amber-50 hover:border-amber-400',
                badge: 'bg-amber-100/90 text-amber-900 border border-amber-300/80',
            };
        }
        if (raw === 'snack' || t.includes('gorengan') || t.includes('mendoan') || t.includes('bakwan')) {
            return {
                card: 'border-2 border-orange-300 bg-orange-50/70 hover:bg-orange-50 hover:border-orange-400',
                badge: 'bg-orange-100/90 text-orange-800 border border-orange-300/80',
            };
        }
        if (raw === 'sauce' || t.includes('petis') || t.includes('saus') || t.includes('bumbu')) {
            return {
                card: 'border-2 border-stone-300 bg-stone-50/70 hover:bg-stone-50 hover:border-stone-400',
                badge: 'bg-stone-100 text-stone-800 border border-stone-300/80',
            };
        }
        return {
            card: 'border-2 border-[#EAE0D5] bg-[#FAF7F2]/80 hover:bg-white hover:border-brand-maroon/40',
            badge: 'bg-[#F5EFE6] text-brand-maroon border border-[#EAE0D5]',
        };
    };

    // Ambil makanan dari custom foods, database via props.food, atau fallback ke sampleFoods
    const food = computed(() => {
        const custom = getCustomFoods();
        const foundCustom = custom.find(f => f.slug === props.slug);
        if (foundCustom) return foundCustom;
        if (props.food) return props.food;
        return sampleFoods.find(f => f.slug === props.slug) || sampleFoods[0];
    });

    // State pemesanan single-product quantity
    const quantity = ref(1);
    const ecoContainer = ref('bawa_sendiri'); // 'bawa_sendiri' atau 'kotak_kertas'
    const isBookmarked = ref(false);

    const increment = () => {
        if (quantity.value < food.value.stock) {
            quantity.value++;
        }
    };

    const decrement = () => {
        if (quantity.value > 1) {
            quantity.value--;
        }
    };

    const containerFee = computed(() => {
        return ecoContainer.value === 'kotak_kertas' ? 2000 : 0;
    });

    const subtotal = computed(() => {
        return (food.value.priceDiscount * quantity.value) + containerFee.value;
    });

    const originalTotal = computed(() => {
        return food.value.priceOriginal * quantity.value;
    });

    const subsidySaved = computed(() => {
        return originalTotal.value - (food.value.priceDiscount * quantity.value);
    });

    return {
        food,
        quantity,
        ecoContainer,
        isBookmarked,
        increment,
        decrement,
        containerFee,
        subtotal,
        originalTotal,
        subsidySaved,
        getCardTheme,
    };
}
