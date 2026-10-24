import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { campusZones, saveCustomFood, deleteCustomFood, getCustomFoods } from '@/Utils/mockFoods';

export function useKelolaMenu(props) {
    // Zona kampus aktif di Surabaya (sinkron dengan filter mahasiswa)
    const availableCampusZones = computed(() => campusZones.filter(z => !z.isComingSoon));

    const isCampusDropdownOpen = ref(false);

    // Form Model Input Kilat yang Selaras dengan Filter Katalog Mahasiswa
    const form = ref({
        name: 'Nasi Ayam Bakar Bumbu Rujak',
        category: 'Nasi',
        campusZoneId: props.merchant?.campus_zone || 'unesa-ketintang',
        canteenLocation: props.merchant?.canteen_location || 'Kantin Vokasi Lt. 1 Stand 04',
        priceOriginal: 22000,
        priceDiscount: 11000,
        estimatedCost: 8000,
        stock: 4,
        pickupSession: 'malam', // 'siang', 'sore', 'malam', 'custom'
        pickupStart: '19.00',
        pickupEnd: '20.30',
        tags: ['Halal', 'Bebas Kacang'],
        spiciness: 'Tidak Pedas', // 'Tidak Pedas', 'Pedas Sedang', 'Sangat Pedas'
        supportEcoPackaging: true,
        ecoDiscount: 500,
        kitchenNote: 'Disimpan hangat di food warmer grade A sejak jam masak',
        image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80'
    });

    const selectedCampusZone = computed(() => {
        return availableCampusZones.value.find(z => z.id === form.value.campusZoneId) || availableCampusZones.value[0] || { name: 'Pilih Zona Kampus' };
    });

    const selectCampusZone = (zoneId) => {
        form.value.campusZoneId = zoneId;
        isCampusDropdownOpen.value = false;
    };

    const getTagBadgeClass = (tag) => {
        const t = tag.toLowerCase();
        if (t.includes('halal')) return 'bg-emerald-600 text-white border-emerald-700';
        if (t.includes('kacang')) return 'bg-sky-600 text-white border-sky-700';
        if (t.includes('vegetarian') || t.includes('nabati')) return 'bg-teal-600 text-white border-teal-700';
        if (t.includes('gluten')) return 'bg-purple-600 text-white border-purple-700';
        return 'bg-slate-700 text-white border-slate-800';
    };

    // Listing aktif yang terhubung langsung dengan database MySQL foods
    const activeListings = ref([...props.foods]);

    function syncCustomFoodsToListings() {
        try {
            const custom = getCustomFoods();
            if (!custom || custom.length === 0) return;
            let customChanged = false;

            // Pastikan data localStorage diselaraskan dengan database MySQL yang aktif
            const updatedCustom = custom.filter(c => {
                const found = activeListings.value.find(l => 
                    String(l.id) === String(c.id) || 
                    l.slug === c.slug || 
                    (l.name && c.name && l.name.trim().toLowerCase() === c.name.trim().toLowerCase())
                );
                if (found) {
                    // Jika ada di database, sinkronkan data localStorage agar sama persis
                    const dbStock = Number(found.stock ?? 0);
                    if (c.stock !== dbStock || c.portions !== dbStock || c.status !== found.status) {
                        c.stock = dbStock;
                        c.portions = dbStock;
                        c.status = found.status;
                        customChanged = true;
                    }
                    return true;
                }
                // Jika item di localStorage sudah tidak ada di database (misal telah dihapus oleh mitra),
                // bersihkan dari localStorage agar tidak menjadi data hantu / ghost item
                customChanged = true;
                return false;
            });

            if (customChanged && typeof window !== 'undefined') {
                localStorage.setItem('plusfood_custom_foods', JSON.stringify(updatedCustom));
            }
        } catch (e) {
            console.error('Error syncing custom foods:', e);
        }
    }

    watch(() => props.foods, (newVal) => {
        if (newVal) {
            activeListings.value = [...newVal];
            syncCustomFoodsToListings();
        }
    }, { deep: true, immediate: true });

    onMounted(() => {
        syncCustomFoodsToListings();
        if (typeof window !== 'undefined') {
            const handleStorageOrUpdate = () => {
                syncCustomFoodsToListings();
                // Selalu reload server data di background agar sinkron dengan database MySQL
                router.reload({ only: ['foods'], preserveScroll: true });
            };

            window.addEventListener('plusfood_custom_foods_updated', handleStorageOrUpdate);
            window.addEventListener('storage', handleStorageOrUpdate);

            // Otomatis refresh listing saat mitra kembali membuka/fokus tab
            const handleVisibilityOrFocus = () => {
                router.reload({ only: ['foods'], preserveScroll: true });
            };
            window.addEventListener('visibilitychange', handleVisibilityOrFocus);
            window.addEventListener('focus', handleVisibilityOrFocus);

            onUnmounted(() => {
                window.removeEventListener('plusfood_custom_foods_updated', handleStorageOrUpdate);
                window.removeEventListener('storage', handleStorageOrUpdate);
                window.removeEventListener('visibilitychange', handleVisibilityOrFocus);
                window.removeEventListener('focus', handleVisibilityOrFocus);
            });
        }
    });

    const isPublished = ref(false);
    const publishedFoodName = ref('');

    // Mengubah Jendela Waktu Ambil berdasarkan Preset Sesi
    const setPickupSession = (session) => {
        form.value.pickupSession = session;
        if (session === 'siang') {
            form.value.pickupStart = '13.00';
            form.value.pickupEnd = '15.00';
        } else if (session === 'sore') {
            form.value.pickupStart = '16.30';
            form.value.pickupEnd = '18.00';
        } else if (session === 'malam') {
            form.value.pickupStart = '19.00';
            form.value.pickupEnd = '20.30';
        }
    };

    // Kalkulasi Diskon & Smart Recovery Assistant Sesuai PRD
    const discountPercent = computed(() => {
        if (!form.value.priceOriginal || form.value.priceOriginal <= 0) return 0;
        const diff = form.value.priceOriginal - form.value.priceDiscount;
        return Math.max(0, Math.round((diff / form.value.priceOriginal) * 100));
    });

    const capitalRecoveryRate = computed(() => {
        if (!form.value.estimatedCost || form.value.estimatedCost <= 0) return 100;
        return Math.round((form.value.priceDiscount / form.value.estimatedCost) * 100);
    });

    // Form Guard Validasi
    const isFormValid = computed(() => {
        return form.value.name.trim() !== '' &&
               form.value.priceDiscount > 0 &&
               form.value.priceDiscount < form.value.priceOriginal &&
               form.value.stock > 0 &&
               form.value.canteenLocation.trim() !== '';
    });

    // Preset Menu Cepat Sesuai Makanan di Kampus
    const presetMenus = [
        { 
            name: 'Nasi Ayam Bakar Bumbu Rujak', 
            cat: 'Nasi', 
            orig: 25000, 
            disc: 12000, 
            cost: 9000, 
            session: 'malam',
            tags: ['Halal', 'Bebas Kacang'], 
            spiciness: 'Pedas Sedang',
            canteen: 'Kantin Vokasi Stand 04',
            image: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=500&q=80'
        },
        { 
            name: 'Nasi Campur Bali Ayam Suwir', 
            cat: 'Nasi', 
            orig: 24000, 
            disc: 12000, 
            cost: 8500, 
            session: 'malam',
            tags: ['Halal'], 
            spiciness: 'Pedas Sedang',
            canteen: 'Pujasera Ketintang Stand 02',
            image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80'
        },
        { 
            name: 'Sop Ayam & Sayur Segar Warung', 
            cat: 'Soto', 
            orig: 18000, 
            disc: 9000, 
            cost: 6000, 
            session: 'sore',
            tags: ['Halal', 'Bebas Kacang', 'Bebas Gluten'], 
            spiciness: 'Tidak Pedas',
            canteen: 'Kantin Vokasi Stand 04',
            image: 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=500&q=80'
        },
        { 
            name: 'Nasi Pecel Telur Dadar Madiun', 
            cat: 'Nasi', 
            orig: 16000, 
            disc: 8000, 
            cost: 5500, 
            session: 'siang',
            tags: ['Halal', 'Vegetarian'], 
            spiciness: 'Pedas Sedang',
            canteen: 'Kantin Pusat Stand 08',
            image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=500&q=80'
        },
        { 
            name: 'Roti Bakar Bandung Cokelat Keju', 
            cat: 'Roti', 
            orig: 18000, 
            disc: 9000, 
            cost: 6500, 
            session: 'malam',
            tags: ['Halal', 'Vegetarian'], 
            spiciness: 'Tidak Pedas',
            canteen: 'Sentra Jajanan Malam',
            image: 'https://images.unsplash.com/photo-1509722747041-616f39b57569?auto=format&fit=crop&w=500&q=80'
        }
    ];

    const applyPreset = (preset) => {
        form.value.name = preset.name;
        form.value.category = preset.cat;
        form.value.priceOriginal = preset.orig;
        form.value.priceDiscount = preset.disc;
        form.value.estimatedCost = preset.cost;
        form.value.tags = [...preset.tags];
        form.value.spiciness = preset.spiciness;
        form.value.canteenLocation = preset.canteen;
        form.value.image = preset.image;
        setPickupSession(preset.session);
    };

    // Ambil profil mitra resmi dari localStorage jika ada
    const getMitraStoreInfo = () => {
        let merchantName = 'Warung Bu Sari';
        let merchantAddress = form.value.canteenLocation || 'Kantin Vokasi Lt. 1 Stand 04, UNESA Ketintang';
        if (typeof window !== 'undefined') {
            try {
                const raw = localStorage.getItem('plusfood_mitra_profile');
                if (raw) {
                    const parsed = JSON.parse(raw);
                    if (parsed.storeName) merchantName = parsed.storeName;
                    if (parsed.canteenLocation) merchantAddress = `${parsed.canteenLocation}, ${parsed.campusZone || 'Surabaya'}`;
                }
            } catch (e) {}
        }
        return { merchantName, merchantAddress };
    };

    const isSubmitting = ref(false);

    // Modal State: Sukses Publikasi
    const showSuccessModal = ref(false);
    const publishedItemDetails = ref(null);

    // Modal State: Edit Menu
    const showEditModal = ref(false);
    const editingItem = ref(null);
    const isUpdating = ref(false);
    const editForm = ref({
        id: null,
        name: '',
        category: 'Nasi',
        priceOriginal: 0,
        priceDiscount: 0,
        stock: 1,
        pickupStart: '19.00',
        pickupEnd: '20.30',
        tags: [],
        kitchenNote: '',
    });

    // Modal State: Hapus / Batalkan Publikasi
    const showDeleteConfirm = ref(false);
    const itemToDelete = ref(null);
    const isDeleting = ref(false);

    const scrollToListing = () => {
        showSuccessModal.value = false;
        const el = document.getElementById('daftar-listing-aktif');
        if (el) {
            el.scrollIntoView({ behavior: 'smooth' });
        }
    };

    const resetFormForNewItem = () => {
        isPublished.value = false;
        const nextPreset = presetMenus.find(p => p.name !== publishedFoodName.value) || presetMenus[1];
        applyPreset(nextPreset);
        const el = document.getElementById('input-food-name');
        if (el) {
            el.scrollIntoView({ behavior: 'smooth' });
            el.focus();
        }
    };

    const validationError = ref('');

    const submitListing = () => {
        validationError.value = '';

        if (!form.value.name.trim()) {
            validationError.value = 'Silakan isi nama makanan surplus terlebih dahulu!';
            const el = document.getElementById('input-food-name');
            if (el) el.focus();
            return;
        }

        if (!form.value.canteenLocation.trim()) {
            validationError.value = 'Silakan isi lokasi stand / kantin Anda!';
            return;
        }

        if (form.value.priceDiscount >= form.value.priceOriginal) {
            validationError.value = 'Harga diskon surplus harus lebih murah dari harga normal!';
            return;
        }

        if (form.value.stock <= 0) {
            validationError.value = 'Sisa porsi (stok) minimal 1 porsi!';
            return;
        }

        if (isSubmitting.value) return;
        isSubmitting.value = true;
        const foodPayload = {
            name: form.value.name.trim(),
            category: form.value.category,
            campus_zone_id: form.value.campusZoneId,
            campusZoneId: form.value.campusZoneId,
            canteen_location: form.value.canteenLocation.trim(),
            canteenLocation: form.value.canteenLocation.trim(),
            price_original: Number(form.value.priceOriginal),
            price_discount: Number(form.value.priceDiscount),
            estimated_cost: Number(form.value.estimatedCost),
            stock: Number(form.value.stock),
            pickup_session: form.value.pickupSession,
            pickup_time_start: form.value.pickupStart,
            pickup_time_end: form.value.pickupEnd,
            pickupStart: form.value.pickupStart,
            pickupEnd: form.value.pickupEnd,
            tags: [...form.value.tags],
            spiciness: form.value.spiciness,
            support_eco_packaging: Boolean(form.value.supportEcoPackaging),
            supportEcoPackaging: Boolean(form.value.supportEcoPackaging),
            description: form.value.kitchenNote,
            kitchenNote: form.value.kitchenNote,
            image: form.value.image
        };

        router.post(route('mitra.kelola-menu.store'), foodPayload, {
            preserveScroll: true,
            onSuccess: () => {
                // Sinkronkan langsung ke LocalStorage Mahasiswa agar seketika muncul di Beranda & Katalog
                const customItem = {
                    id: Date.now(),
                    slug: foodPayload.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '') + '-' + Date.now(),
                    name: foodPayload.name,
                    shortName: foodPayload.name,
                    category: foodPayload.category,
                    priceOriginal: foodPayload.price_original,
                    priceDiscount: foodPayload.price_discount,
                    discountPercent: discountPercent.value,
                    stock: foodPayload.stock,
                    portions: foodPayload.stock,
                    pickupTime: `${foodPayload.pickup_time_start} – ${foodPayload.pickup_time_end} WIB`,
                    pickupDeadlineInfo: `Hari ini · tutup ${foodPayload.pickup_time_end} WIB`,
                    campusZone: foodPayload.campus_zone_id || props.merchant?.campus_zone || 'unesa-ketintang',
                    merchantZoneLabel: props.merchant?.campus_name || 'UNESA Ketintang',
                    merchant: props.merchant?.name || 'Warung Bu Sari',
                    merchantName: props.merchant?.name || 'Warung Bu Sari',
                    address: foodPayload.canteen_location || props.merchant?.address || 'Kantin Vokasi Lt. 1 Stand 04',
                    image: foodPayload.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80',
                    dietaryTags: [...foodPayload.tags],
                    tags: [...foodPayload.tags],
                    isCookedToday: true,
                    isSealed: true,
                    rating: 4.9,
                    reviewsCount: 1,
                    minutesLeft: 45,
                    distance: '0,4 km',
                    distanceKm: 0.4,
                };
                saveCustomFood(customItem);

                publishedFoodName.value = foodPayload.name;
                publishedItemDetails.value = {
                    ...customItem,
                    canteenLocation: foodPayload.canteen_location,
                };
                showSuccessModal.value = true;
                isPublished.value = true;
                
                // Pre-fill preset menu berikutnya agar mitra bisa langsung mempublikasikan menu lain
                const nextPreset = presetMenus.find(p => p.name !== foodPayload.name) || presetMenus[0];
                form.value.name = nextPreset.name;
                form.value.category = nextPreset.cat;
                form.value.priceOriginal = nextPreset.orig;
                form.value.priceDiscount = nextPreset.disc;
                form.value.estimatedCost = nextPreset.cost;
                form.value.tags = [...nextPreset.tags];
                form.value.spiciness = nextPreset.spiciness;
                form.value.image = nextPreset.image;
                isSubmitting.value = false;
            },
            onError: (err) => {
                console.error('Gagal mempublikasikan menu surplus:', err);
                isSubmitting.value = false;
            },
            onFinish: () => {
                isSubmitting.value = false;
            }
        });
    };

    const openEditModal = (item) => {
        editingItem.value = item;
        editForm.value = {
            id: item.id,
            name: item.name,
            category: item.category || 'Nasi',
            priceOriginal: item.priceOriginal ?? item.price_original ?? 20000,
            priceDiscount: item.priceDiscount ?? item.price_discount ?? 10000,
            stock: item.stock ?? 1,
            pickupStart: item.pickupStart ?? item.pickup_time_start ?? '19.00',
            pickupEnd: item.pickupEnd ?? item.pickup_time_end ?? '20.30',
            tags: [...(item.tags ?? item.dietary_tags ?? ['Halal'])],
            kitchenNote: item.description ?? item.kitchenNote ?? '',
        };
        showEditModal.value = true;
    };

    const saveEdit = () => {
        if (!editForm.value.name || editForm.value.priceDiscount <= 0 || isUpdating.value) return;

        isUpdating.value = true;
        const payload = {
            name: editForm.value.name.trim(),
            category: editForm.value.category,
            price_original: Number(editForm.value.priceOriginal),
            price_discount: Number(editForm.value.priceDiscount),
            stock: Number(editForm.value.stock),
            pickup_time_start: editForm.value.pickupStart,
            pickup_time_end: editForm.value.pickupEnd,
            pickupStart: editForm.value.pickupStart,
            pickupEnd: editForm.value.pickupEnd,
            tags: [...editForm.value.tags],
            description: editForm.value.kitchenNote,
            kitchenNote: editForm.value.kitchenNote,
        };

        router.put(route('mitra.kelola-menu.update', editForm.value.id), payload, {
            preserveScroll: true,
            onSuccess: () => {
                isUpdating.value = false;
                showEditModal.value = false;

                // Perbarui listing lokal
                const idx = activeListings.value.findIndex(l => l.id === editForm.value.id);
                if (idx >= 0) {
                    const target = activeListings.value[idx];
                    target.name = payload.name;
                    target.category = payload.category;
                    target.priceOriginal = payload.price_original;
                    target.priceDiscount = payload.price_discount;
                    target.discountPercent = Math.round(((payload.price_original - payload.price_discount) / payload.price_original) * 100);
                    target.stock = payload.stock;
                    target.pickupStart = payload.pickup_time_start;
                    target.pickupEnd = payload.pickup_time_end;
                    target.tags = payload.tags;
                    target.status = payload.stock <= 0 ? 'habis' : 'aktif';
                }

                // Sinkronisasi ke LocalStorage Mahasiswa
                saveCustomFood({
                    id: editForm.value.id,
                    slug: editingItem.value?.slug || (payload.name.toLowerCase().replace(/[^a-z0-9]+/g, '-') + '-' + editForm.value.id),
                    name: payload.name,
                    shortName: payload.name,
                    category: payload.category,
                    priceOriginal: payload.price_original,
                    priceDiscount: payload.price_discount,
                    discountPercent: Math.round(((payload.price_original - payload.price_discount) / payload.price_original) * 100),
                    stock: payload.stock,
                    portions: payload.stock,
                    pickupTime: `${payload.pickup_time_start} – ${payload.pickup_time_end} WIB`,
                    pickupDeadlineInfo: `Hari ini · tutup ${payload.pickup_time_end} WIB`,
                    campusZone: props.merchant?.campus_zone || 'unesa-ketintang',
                    merchantZoneLabel: props.merchant?.campus_name || 'UNESA Ketintang',
                    merchant: props.merchant?.name || 'Warung Bu Sari',
                    merchantName: props.merchant?.name || 'Warung Bu Sari',
                    address: editingItem.value?.canteenLocation || props.merchant?.address || 'Kantin Vokasi Stand 04',
                    image: editingItem.value?.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80',
                    dietaryTags: [...payload.tags],
                    tags: [...payload.tags],
                    isCookedToday: true,
                    isSealed: true,
                    rating: editingItem.value?.rating || 4.9,
                    reviewsCount: editingItem.value?.reviewsCount || 1,
                    minutesLeft: 45,
                    distance: '0,4 km',
                    distanceKm: 0.4,
                });
            },
            onError: (err) => {
                console.error('Gagal update menu:', err);
                isUpdating.value = false;
            }
        });
    };

    const openDeleteModal = (item) => {
        itemToDelete.value = item;
        showDeleteConfirm.value = true;
    };

    const confirmDelete = () => {
        if (!itemToDelete.value || isDeleting.value) return;

        isDeleting.value = true;
        const target = itemToDelete.value;
        const targetId = target.id;
        const targetSlug = target.slug;
        const targetName = target.name;

        // Segera hapus dari activeListings dan localStorage untuk respon instan
        activeListings.value = activeListings.value.filter(l => 
            l.id !== targetId && 
            (!targetSlug || l.slug !== targetSlug) && 
            (!targetName || l.name !== targetName)
        );
        deleteCustomFood(targetId);
        if (targetSlug) deleteCustomFood(targetSlug);
        if (targetName) deleteCustomFood(targetName);

        // Jika ID bukan ID database numerik standar, cukup bersihkan client-side
        const isDbId = typeof targetId === 'number' && targetId > 0 && targetId < 1000000000;
        if (!isDbId && isNaN(Number(targetId))) {
            isDeleting.value = false;
            showDeleteConfirm.value = false;
            itemToDelete.value = null;
            return;
        }

        router.delete(route('mitra.kelola-menu.destroy', targetId), {
            preserveScroll: true,
            onSuccess: () => {
                isDeleting.value = false;
                showDeleteConfirm.value = false;
                itemToDelete.value = null;
            },
            onError: (err) => {
                console.error('Gagal menghapus menu di backend:', err);
                isDeleting.value = false;
                showDeleteConfirm.value = false;
                itemToDelete.value = null;
            },
            onFinish: () => {
                isDeleting.value = false;
                showDeleteConfirm.value = false;
                itemToDelete.value = null;
            }
        });
    };

    const toggleStatus = (item) => {
        const nextStatus = item.status === 'aktif' ? 'habis' : 'aktif';
        item.status = nextStatus;
        router.patch(route('mitra.kelola-menu.toggle', item.id), {}, {
            preserveScroll: true
        });
    };

    const adjustStock = (item, delta) => {
        const next = Math.max(0, Number(item.stock) + delta);
        item.stock = next;
        item.portions = next;
        item.status = next === 0 ? 'habis' : 'aktif';

        try {
            const custom = getCustomFoods();
            const found = custom.find(c => 
                String(c.id) === String(item.id) || 
                c.slug === item.slug || 
                (c.name && item.name && c.name.trim().toLowerCase() === item.name.trim().toLowerCase())
            );
            if (found) {
                found.stock = next;
                found.portions = next;
                found.status = item.status;
                localStorage.setItem('plusfood_custom_foods', JSON.stringify(custom));
                window.dispatchEvent(new Event('plusfood_custom_foods_updated'));
            }
        } catch (e) {}

        router.patch(route('mitra.kelola-menu.stock', item.id), {
            stock: next
        }, {
            preserveScroll: true
        });
    };

    return {
        availableCampusZones,
        isCampusDropdownOpen,
        selectedCampusZone,
        selectCampusZone,
        getTagBadgeClass,
        activeListings,
        form,
        isPublished,
        publishedFoodName,
        setPickupSession,
        discountPercent,
        capitalRecoveryRate,
        isFormValid,
        presetMenus,
        applyPreset,
        getMitraStoreInfo,
        isSubmitting,
        showSuccessModal,
        publishedItemDetails,
        showEditModal,
        editingItem,
        isUpdating,
        editForm,
        showDeleteConfirm,
        itemToDelete,
        isDeleting,
        scrollToListing,
        resetFormForNewItem,
        validationError,
        submitListing,
        openEditModal,
        saveEdit,
        openDeleteModal,
        confirmDelete,
        toggleStatus,
        adjustStock,
    };
}
