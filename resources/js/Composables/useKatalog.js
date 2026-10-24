import { ref, computed, onMounted } from 'vue';
import { sampleFoods, campusZones, getCustomFoods } from '@/Utils/mockFoods';

export function useKatalog(props) {
    const customFoods = ref([]);
    const reloadCustomFoods = () => {
        customFoods.value = getCustomFoods();
    };

    const maxPrice = ref(props.filters?.max_price || 25000);
    const selectedCategory = ref(props.filters?.category || 'Semua');
    const selectedCampus = ref(props.filters?.campus || 'all');
    const selectedDistance = ref('2 km');
    const searchQuery = ref(props.filters?.q || '');
    const isLoading = ref(true);
    const fetchError = ref(null);

    // Dietary & Pantangan Alergi Filters
    const filterHalal = ref(true);
    const filterVegetarian = ref(false);
    const filterTidakPedas = ref(false);

    // Safety & Quality Filters
    const filterVerified = ref(true);
    const filterCookedToday = ref(true);
    const filterRating = ref(false);
    const filterSealed = ref(false);

    const categories = ['Nasi', 'Mie', 'Soto', 'Roti', 'Camilan', 'Minuman', 'Semua'];
    const distances = ['500 m', '1 km', '2 km', '3 km'];

    const activeZones = computed(() => campusZones.filter(c => !c.isComingSoon));
    const comingSoonZones = computed(() => campusZones.filter(c => c.isComingSoon));

    // Modal Partner Onboarding & Student Waitlist
    const showPartnerModal = ref(false);
    const partnerType = ref('merchant'); // 'merchant' | 'student'
    const partnerForm = ref({
        name: '',
        businessOrCampus: '',
        whatsapp: '',
        notes: ''
    });
    const formSubmitted = ref(false);

    const openPartnerModal = (type = 'merchant') => {
        partnerType.value = type;
        formSubmitted.value = false;
        partnerForm.value = {
            name: '',
            businessOrCampus: '',
            whatsapp: '',
            notes: ''
        };
        showPartnerModal.value = true;
    };

    const closePartnerModal = () => {
        showPartnerModal.value = false;
    };

    const submitPartnerForm = () => {
        if (!partnerForm.value.name || !partnerForm.value.whatsapp) return;
        formSubmitted.value = true;
    };

    // Custom Dropdown Kampus State
    const isCampusDropdownOpen = ref(false);

    const selectCampusZone = (id) => {
        selectedCampus.value = id;
        isCampusDropdownOpen.value = false;
    };

    const currentCampus = computed(() => {
        return campusZones.find(c => c.id === selectedCampus.value) || campusZones[0];
    });

    const resetFilters = () => {
        searchQuery.value = '';
        fetchError.value = null;
        maxPrice.value = 25000;
        selectedCategory.value = 'Semua';
        selectedCampus.value = 'all';
        isCampusDropdownOpen.value = false;
        selectedDistance.value = '2 km';
        filterHalal.value = true;
        filterVegetarian.value = false;
        filterTidakPedas.value = false;
        filterVerified.value = true;
        filterCookedToday.value = true;
        filterRating.value = false;
        filterSealed.value = false;
    };

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

    // Filtered Foods - Seluruh Filter Aktif & Responsif
    const filteredFoods = computed(() => {
        return foodsList.value.filter(item => {
            // Hilangkan menu yang stoknya sudah habis dari katalog mahasiswa
            const currentStock = Number(item.stock ?? item.portions ?? 1);
            if (currentStock <= 0 || item.status === 'habis') {
                return false;
            }

            // 1. Filter Pencarian Teks (Nama, Menu, Warung, Kategori, Deskripsi)
            if (searchQuery.value && searchQuery.value.trim() !== '') {
                const q = searchQuery.value.trim().toLowerCase();
                const name = (item.name || '').toLowerCase();
                const shortName = (item.shortName || '').toLowerCase();
                const merchantStr = typeof item.merchant === 'string' ? item.merchant : (item.merchant?.name || item.merchantName || '');
                const matchMerchant = merchantStr.toLowerCase().includes(q);
                const category = (item.category || '').toLowerCase();
                const desc = (item.description || '').toLowerCase();
                if (!name.includes(q) && !shortName.includes(q) && !matchMerchant && !category.includes(q) && !desc.includes(q)) {
                    return false;
                }
            }

            // 2. Filter Rentang Harga (Rp5.000 – Rp25.000)
            const price = Number(item.priceDiscount ?? item.price_discount ?? 0);
            if (price > Number(maxPrice.value)) return false;

            // 3. Filter Kategori Menu
            if (selectedCategory.value !== 'Semua' && item.category !== selectedCategory.value) return false;

            // 4. Filter Zona Kampus
            const itemZone = item.campusZone ?? item.campus_zone ?? item.merchant?.campus_zone;
            if (selectedCampus.value !== 'all' && itemZone !== selectedCampus.value) return false;

            // 5. Filter Radius Jarak (500 m, 1 km, 2 km, 3 km)
            if (selectedDistance.value) {
                const maxRadiusKm = selectedDistance.value === '500 m' ? 0.5 :
                                    selectedDistance.value === '1 km' ? 1.0 :
                                    selectedDistance.value === '2 km' ? 2.0 :
                                    selectedDistance.value === '3 km' ? 3.0 : 5.0;
                const distKm = typeof item.distanceKm === 'number' ? item.distanceKm :
                               parseFloat(String(item.distance || '1.2').replace(',', '.')) || 1.2;
                if (distKm > maxRadiusKm) return false;
            }

            // 6. Filter Standar Mutu & Keamanan
            const rating = Number(item.rating ?? item.merchantRating ?? item.merchant?.rating ?? 4.8);
            if (filterRating.value && rating < 4.8) return false;

            const isVerified = item.isVerified ?? item.is_verified ?? item.merchant?.is_verified ?? true;
            if (filterVerified.value && !isVerified) return false;

            const isCookedToday = item.isCookedToday ?? item.is_cooked_today ?? true;
            if (filterCookedToday.value && !isCookedToday) return false;

            const isSealed = item.isSealed ?? item.is_sealed ?? true;
            if (filterSealed.value && !isSealed) return false;

            // 7. Filter Pantangan & Preferensi Diet
            const rawDietTags = item.dietaryTags ?? item.dietary_tags ?? [];
            const rawTags = item.tags ?? [];
            const tags = [...rawDietTags, ...rawTags].map(t => String(t).toLowerCase());

            if (filterHalal.value && !tags.some(t => t.includes('halal'))) return false;
            if (filterVegetarian.value && !(tags.some(t => t.includes('vegetarian')) || item.category === 'Roti' || item.category === 'Camilan')) return false;
            if (filterTidakPedas.value && (tags.includes('pedas') && !tags.some(t => t.includes('tidak pedas')))) return false;

            return true;
        });
    });

    const isMobileFilterOpen = ref(false);

    onMounted(() => {
        reloadCustomFoods();
        try {
            isLoading.value = true;
            setTimeout(() => {
                isLoading.value = false;
            }, 350);

            if (typeof window !== 'undefined') {
                window.addEventListener('storage', (e) => {
                    if (e.key === 'plusfood_custom_foods') {
                        reloadCustomFoods();
                    }
                });
                window.addEventListener('plusfood_custom_foods_updated', reloadCustomFoods);

                const urlParams = new URLSearchParams(window.location.search);
                const q = urlParams.get('q');
                if (q) searchQuery.value = q;

                const savedPref = localStorage.getItem('plusfood_preferences');
                if (savedPref) {
                    try {
                        const p = JSON.parse(savedPref);
                        if (p.campus && p.campus !== 'all') selectedCampus.value = p.campus;
                        if (p.radius) selectedDistance.value = p.radius;
                        if (p.dietary) {
                            const halalTag = p.dietary.find(d => d.id === 'halal');
                            if (halalTag) filterHalal.value = halalTag.selected;
                            const vegTag = p.dietary.find(d => d.id === 'vegetarian');
                            if (vegTag) filterVegetarian.value = vegTag.selected;
                            const spicyTag = p.dietary.find(d => d.id === 'tidak_pedas');
                            if (spicyTag) filterTidakPedas.value = spicyTag.selected;
                        }
                    } catch (e) {}
                }
            }
        } catch (err) {
            fetchError.value = 'Gagal memuat katalog makanan. Coba lagi ↻';
            isLoading.value = false;
        }
    });

    return {
        customFoods,
        reloadCustomFoods,
        foodsList,
        maxPrice,
        selectedCategory,
        selectedCampus,
        selectedDistance,
        searchQuery,
        isLoading,
        fetchError,
        filterHalal,
        filterVegetarian,
        filterTidakPedas,
        filterVerified,
        filterCookedToday,
        filterRating,
        filterSealed,
        categories,
        distances,
        activeZones,
        comingSoonZones,
        showPartnerModal,
        partnerType,
        partnerForm,
        formSubmitted,
        openPartnerModal,
        closePartnerModal,
        submitPartnerForm,
        isCampusDropdownOpen,
        selectCampusZone,
        currentCampus,
        resetFilters,
        filteredFoods,
        isMobileFilterOpen,
    };
}
