import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useAuth } from '@/Composables/useAuth';
import { sampleFoods, campusZones, syncLocalOrdersWithDb, getCustomFoods } from '@/Utils/mockFoods';

export function useBeranda(props) {
    const auth = useAuth();
    const page = usePage();
    const searchQuery = ref('');
    const selectedCampus = ref('unesa-ketintang');
    const selectedRadius = ref('2 km');
    const selectedFastFilter = ref('Semua');
    const showLocationModal = ref(false);
    const isLoading = ref(true);
    const customFoods = ref([]);
    const allOrders = ref([]);

    const reloadCustomFoods = () => {
        customFoods.value = getCustomFoods();
    };

    const reloadOrders = () => {
        if (typeof window !== 'undefined' && localStorage.getItem('plusfood_orders_cleared') === 'true') {
            allOrders.value = [];
            return;
        }
        const currentUserId = auth.user.value?.id;
        const currentUserEmail = auth.user.value?.email;
        const dbOrders = page.props?.allMitraOrders || page.props?.dbOrders || page.props?.activeOrders || [];
        const synced = syncLocalOrdersWithDb(dbOrders, currentUserId, currentUserEmail);
        allOrders.value = (synced || []).filter(o => {
            if (currentUserId) {
                const oUserId = o.user_id || o.userId;
                const oEmail = o.customer_email || o.email || o.userEmail;
                if (oUserId && Number(oUserId) !== Number(currentUserId)) return false;
                if (oEmail && currentUserEmail && String(oEmail).trim().toLowerCase() !== String(currentUserEmail).trim().toLowerCase()) return false;
            }
            return true;
        });
    };

    watch(() => page.props?.allMitraOrders, () => {
        reloadOrders();
    });

    watch(() => auth.user.value?.id, () => {
        reloadOrders();
        checkDraftCheckout();
    });

    const activePendingOrders = computed(() => {
        return allOrders.value.filter(o => o.status === 'menunggu' || o.status === 'siap_ambil');
    });

    // ================= STATE DRAFT CHECKOUT =================
    const draftCheckout = ref(null);

    const checkDraftCheckout = () => {
        if (typeof window === 'undefined') return;
        try {
            const raw = localStorage.getItem('plusfood_draft_checkout');
            if (raw) {
                const data = JSON.parse(raw);
                const currentUserId = auth.user.value?.id;
                const currentUserEmail = auth.user.value?.email;
                if (currentUserId && data.userId && Number(data.userId) !== Number(currentUserId)) {
                    draftCheckout.value = null;
                    return;
                }
                if (currentUserEmail && data.userEmail && String(data.userEmail).trim().toLowerCase() !== String(currentUserEmail).trim().toLowerCase()) {
                    draftCheckout.value = null;
                    return;
                }
                if (Date.now() - (data.timestamp || 0) < 2 * 60 * 60 * 1000) {
                    draftCheckout.value = data;
                } else {
                    localStorage.removeItem('plusfood_draft_checkout');
                    draftCheckout.value = null;
                }
            } else {
                draftCheckout.value = null;
            }
        } catch (e) {
            draftCheckout.value = null;
        }
    };

    const discardDraft = () => {
        if (typeof window !== 'undefined') {
            localStorage.removeItem('plusfood_draft_checkout');
        }
        draftCheckout.value = null;
    };

    const getMerchantName = (food) => {
        if (!food) return 'Mitra Kampus';
        if (typeof food.merchant === 'string') return food.merchant;
        if (typeof food.merchant === 'object' && food.merchant?.name) return food.merchant.name;
        if (food.merchantName && typeof food.merchantName === 'string') return food.merchantName;
        return 'Mitra Kampus';
    };

    const radiuses = ['500 m', '1 km', '2 km', '3 km', '5 km'];
    const availableCampuses = computed(() => campusZones.filter(c => !c.isComingSoon));

    const currentCampusLabel = computed(() => {
        const found = campusZones.find(c => c.id === selectedCampus.value);
        return found ? found.name : 'UNESA Ketintang';
    });

    const fastFilters = [
        'Semua',
        'Nasi',
        'Roti',
        'Soto',
        'Halal Only',
        'Bebas Kacang',
        '< Rp15.000'
    ];

    let storageHandler = null;
    let customFoodsHandler = null;
    let orderCreatedHandler = null;
    let orderStatusHandler = null;

    onMounted(() => {
        checkDraftCheckout();
        reloadCustomFoods();
        reloadOrders();
        isLoading.value = false;

        if (typeof window !== 'undefined') {
            storageHandler = (e) => {
                if (e.key === 'plusfood_custom_foods') {
                    reloadCustomFoods();
                }
                if (e.key === 'plusfood_draft_checkout') {
                    checkDraftCheckout();
                }
                if (e.key === 'plusfood_orders') {
                    reloadOrders();
                }
            };
            customFoodsHandler = () => reloadCustomFoods();
            orderCreatedHandler = () => reloadOrders();
            orderStatusHandler = () => reloadOrders();

            window.addEventListener('storage', storageHandler);
            window.addEventListener('plusfood_custom_foods_updated', customFoodsHandler);
            window.addEventListener('plusfood_order_created', orderCreatedHandler);
            window.addEventListener('plusfood_order_status_updated', orderStatusHandler);
        }
    });

    onUnmounted(() => {
        if (typeof window !== 'undefined') {
            if (storageHandler) window.removeEventListener('storage', storageHandler);
            if (customFoodsHandler) window.removeEventListener('plusfood_custom_foods_updated', customFoodsHandler);
            if (orderCreatedHandler) window.removeEventListener('plusfood_order_created', orderCreatedHandler);
            if (orderStatusHandler) window.removeEventListener('plusfood_order_status_updated', orderStatusHandler);
        }
    });

    const foodsList = computed(() => {
        const base = (props.foods && props.foods.length > 0) ? props.foods : sampleFoods;
        const custom = customFoods.value;

        const seenNames = new Set();
        const seenSlugs = new Set();
        const seenIds = new Set();
        const list = [];

        // 1. Prioritaskan makanan resmi dari database MySQL (base)
        base.forEach(item => {
            if (!item || !item.name) return;
            const normName = item.name.trim().toLowerCase();
            const baseSlug = String(item.slug || '').replace(/-\d+$/, '');
            seenNames.add(normName);
            seenSlugs.add(baseSlug);
            seenIds.add(String(item.id));
            list.push(item);
        });

        // 2. Masukkan custom foods HANYA jika menu tersebut belum ada di database
        custom.forEach(item => {
            if (!item || !item.name) return;
            const normName = item.name.trim().toLowerCase();
            const baseSlug = String(item.slug || '').replace(/-\d+$/, '');
            if (!seenNames.has(normName) && !seenSlugs.has(baseSlug) && !seenIds.has(String(item.id))) {
                seenNames.add(normName);
                seenSlugs.add(baseSlug);
                seenIds.add(String(item.id));
                list.push(item);
            }
        });

        return list;
    });

    const filteredFoods = computed(() => {
        return foodsList.value.filter(item => {
            // Hilangkan makanan yang stoknya sudah habis dari beranda mahasiswa
            const currentStock = Number(item.stock ?? item.portions ?? 1);
            if (currentStock <= 0 || item.status === 'habis') {
                return false;
            }

            // Search query filter
            if (searchQuery.value) {
                const q = searchQuery.value.toLowerCase();
                const name = (item.name || '').toLowerCase();
                const shortName = (item.shortName || '').toLowerCase();
                const merchantStr = typeof item.merchant === 'string' ? item.merchant : (item.merchant?.name || item.merchantName || '');
                const matchMerchant = merchantStr.toLowerCase().includes(q);
                const category = (item.category || '').toLowerCase();
                if (!name.includes(q) && !shortName.includes(q) && !matchMerchant && !category.includes(q)) return false;
            }

            // Campus zone filter if not all
            const itemZone = item.campusZone ?? item.campus_zone ?? item.merchant?.campus_zone;
            if (selectedCampus.value !== 'all' && itemZone !== selectedCampus.value) {
                const distKm = typeof item.distanceKm === 'number' ? item.distanceKm :
                               parseFloat(String(item.distance || '1.2').replace(',', '.')) || 1.2;
                const maxRadius = selectedRadius.value === '500 m' ? 0.5 :
                                  selectedRadius.value === '1 km' ? 1.0 :
                                  selectedRadius.value === '2 km' ? 2.0 :
                                  selectedRadius.value === '3 km' ? 3.0 : 5.0;
                if (distKm > maxRadius) {
                    return false;
                }
            }

            // Fast filter bar
            if (selectedFastFilter.value === 'Nasi' && item.category !== 'Nasi') return false;
            if (selectedFastFilter.value === 'Roti' && item.category !== 'Roti') return false;
            if (selectedFastFilter.value === 'Soto' && item.category !== 'Soto') return false;
            const price = Number(item.priceDiscount ?? item.price_discount ?? 0);
            if (selectedFastFilter.value === '< Rp15.000' && price >= 15000) return false;

            const rawDietTags = item.dietaryTags ?? item.dietary_tags ?? [];
            const rawTags = item.tags ?? [];
            const allTags = [...rawDietTags, ...rawTags].map(t => String(t).toLowerCase());

            if (selectedFastFilter.value === 'Halal Only') {
                const isHalal = allTags.some(t => t.includes('halal'));
                if (!isHalal) return false;
            }
            if (selectedFastFilter.value === 'Bebas Kacang') {
                const isNutFree = allTags.some(t => t.includes('bebas kacang')) || (!item.name.toLowerCase().includes('kacang') && !(item.description || '').toLowerCase().includes('kacang'));
                if (!isNutFree) return false;
            }

            return true;
        });
    });

    return {
        auth,
        searchQuery,
        selectedCampus,
        selectedRadius,
        selectedFastFilter,
        showLocationModal,
        isLoading,
        customFoods,
        allOrders,
        activePendingOrders,
        draftCheckout,
        discardDraft,
        getMerchantName,
        radiuses,
        availableCampuses,
        currentCampusLabel,
        fastFilters,
        foodsList,
        filteredFoods,
    };
}
