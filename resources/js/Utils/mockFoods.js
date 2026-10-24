export const campusZones = [
    // --- KOTA SURABAYA (AKTIF BEROPERASI) ---
    { id: 'all', city: 'Surabaya', name: 'Semua Surabaya', hint: 'Menampilkan seluruh UMKM surplus aktif di berbagai zona Surabaya', isComingSoon: false },
    { id: 'unesa-ketintang', city: 'Surabaya', name: 'UNESA Ketintang', hint: '≈ 5–15 mnt dari Kampus UNESA Ketintang & Wonokromo', isComingSoon: false },
    { id: 'unesa-lidah', city: 'Surabaya', name: 'UNESA Lidah Wetan', hint: '≈ 5–15 mnt dari Kampus UNESA Lidah Wetan & Wiyung', isComingSoon: false },
    { id: 'its-sukolilo', city: 'Surabaya', name: 'ITS Sukolilo', hint: '≈ 5–15 mnt dari Kampus ITS, Gebang & Keputih', isComingSoon: false },
    { id: 'unair-b', city: 'Surabaya', name: 'UNAIR (Dharmawangsa/MERR)', hint: '≈ 5–15 mnt dari Kampus B Dharmawangsa & Kampus C MERR', isComingSoon: false },
    { id: 'upn-rungkut', city: 'Surabaya', name: 'UPN Veteran Jatim', hint: '≈ 5–15 mnt dari Kampus UPN Rungkut & Medokan', isComingSoon: false },
    { id: 'petra-siwalankerto', city: 'Surabaya', name: 'UK Petra Siwalankerto', hint: '≈ 5–15 mnt dari Kampus UK Petra & Kutisari', isComingSoon: false },

    // --- KOTA EKSPANSI (COMING SOON & TAHAP REKRUTMEN MITRA) ---
    { 
        id: 'malang-coming-soon', 
        city: 'Malang', 
        name: 'Malang (UB, UM, UMM)', 
        hint: 'Tahap pendaftaran mitra UMKM & waitlist mahasiswa Malang', 
        isComingSoon: true,
        merchantTarget: 20,
        merchantCurrent: 14,
        votesCount: 428,
        featuredCampuses: 'Universitas Brawijaya (UB), Univ. Negeri Malang (UM), UMM',
        keyAreas: 'Jl. Soekarno-Hatta (Suhat), Sigura-gura, Dinoyo, Bendungan Sutami'
    },
    { 
        id: 'jogja-coming-soon', 
        city: 'Yogyakarta', 
        name: 'Yogyakarta (UGM, UNY)', 
        hint: 'Tahap pendaftaran mitra UMKM & waitlist mahasiswa Yogyakarta', 
        isComingSoon: true,
        merchantTarget: 25,
        merchantCurrent: 17,
        votesCount: 512,
        featuredCampuses: 'UGM, UNY, UPN Veteran Yogya, UIN Sunan Kalijaga',
        keyAreas: 'Jl. Kaliurang (Jakal), Gejayan, Seturan, Babarsari'
    },
    { 
        id: 'bandung-coming-soon', 
        city: 'Bandung', 
        name: 'Bandung (ITB, UNPAD)', 
        hint: 'Tahap pendaftaran mitra UMKM & waitlist mahasiswa Bandung', 
        isComingSoon: true,
        merchantTarget: 20,
        merchantCurrent: 9,
        votesCount: 384,
        featuredCampuses: 'ITB, UNPAD, UPI, Telkom University',
        keyAreas: 'Dipatiukur, Dago, Jatinangor, Buahbatu'
    }
];

export const sampleFoods = [
    {
        id: 1,
        slug: 'nasi-ayam-bakar',
        name: 'Nasi Ayam Bakar Bumbu Rujak Spesial',
        shortName: 'Nasi Ayam Bakar',
        merchant: 'Warung Bu Sari',
        merchantInitial: 'BS',
        merchantActiveSince: 'Maret 2023',
        merchantRescuedCount: '1.420+',
        rating: 4.8,
        reviewsCount: 148,
        discountPercent: 52,
        priceDiscount: 12000,
        priceOriginal: 25000,
        stock: 3,
        distance: '2 km',
        distanceMins: '12 mnt dari Kampus UNESA Ketintang',
        category: 'Nasi',
        campusZone: 'unesa-ketintang',
        merchantZoneLabel: 'Ketintang (Dekat UNESA)',
        pickupTime: '19.30 – 20.30 WIB',
        pickupDeadlineInfo: 'Hari ini · tutup 21.00 WIB',
        address: 'Jl. Ketintang Baru No. 12',
        image: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=1200&q=80',
        batchCooked: '16.30 WIB',
        warmerGrade: 'Food Warmer Grade A',
        co2Saved: '0,85 kg CO2e',
        description: 'Ayam bakar bumbu rujak gurih-manis, dimasak pukul 16.30 dan disimpan hangat di food warmer. Siap disantap malam ini.',
        packages: [
            { icon: 'rice', title: '1 Porsi Nasi Putih Pulen' },
            { icon: 'chicken', title: '1 Potong Paha/Dada Bakar Rujak' },
            { icon: 'sambal', title: 'Sambal Terasi Segar Matang' },
            { icon: 'salad', title: 'Lalapan Timun & Kemangi' },
            { icon: 'tempeh', title: 'Tempe Goreng Crispy' }
        ],
        tags: ['Higienis & Halal', 'Menu Siap Santap Malam'],
        dietaryTags: ['Halal', 'Tidak Pedas', 'Bebas Kacang'],
        allergens: ['Kedelai', 'Bawang'],
        minutesLeft: 35
    },
    {
        id: 2,
        slug: 'nasi-campur-bali',
        name: 'Nasi Campur Bali Ayam Suwir Pedas',
        shortName: 'Nasi Campur Bali',
        merchant: 'Dapur Mbak Ayu',
        merchantInitial: 'MA',
        merchantActiveSince: 'Januari 2024',
        merchantRescuedCount: '890+',
        rating: 4.9,
        reviewsCount: 92,
        discountPercent: 54,
        priceDiscount: 13000,
        priceOriginal: 28000,
        stock: 5,
        distance: '1,2 km',
        distanceMins: '8 mnt dari Kampus UNESA Ketintang',
        category: 'Nasi',
        campusZone: 'unesa-ketintang',
        merchantZoneLabel: 'Ketintang (Dekat UNESA)',
        pickupTime: '18.30 – 20.00 WIB',
        pickupDeadlineInfo: 'Hari ini · tutup 20.30 WIB',
        address: 'Jl. Ketintang Madya No. 45',
        image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=1200&q=80',
        batchCooked: '17.00 WIB',
        warmerGrade: 'Food Warmer Grade A',
        co2Saved: '0,92 kg CO2e',
        description: 'Nasi campur khas Bali dengan lauk ayam suwir bumbu betutu, sate lilit mini, sambal matah harum dan kacang goreng renyah.',
        packages: [
            { icon: 'rice', title: '1 Porsi Nasi Gurih' },
            { icon: 'chicken', title: 'Ayam Suwir Betutu' },
            { icon: 'sambal', title: 'Sambal Matah Segar' },
            { icon: 'salad', title: 'Urab Kacang Panjang' }
        ],
        tags: ['Higienis & Halal', 'Pilihan Favorit'],
        dietaryTags: ['Halal', 'Pedas'],
        allergens: ['Kacang Tanah', 'Terasi / Seafood'],
        minutesLeft: 25
    },
    {
        id: 3,
        slug: 'soto-ayam-lamongan',
        name: 'Soto Ayam Lamongan Koya Gurih',
        shortName: 'Soto Ayam Lamongan',
        merchant: 'Soto Cak Har Semolowaru',
        merchantInitial: 'CH',
        merchantActiveSince: 'Oktober 2022',
        merchantRescuedCount: '2.100+',
        rating: 4.7,
        reviewsCount: 215,
        discountPercent: 45,
        priceDiscount: 11000,
        priceOriginal: 20000,
        stock: 2,
        distance: '1,1 km',
        distanceMins: '6 mnt dari Kampus ITS & UNAIR C',
        category: 'Soto',
        campusZone: 'its-sukolilo',
        merchantZoneLabel: 'Semolowaru (Dekat ITS)',
        pickupTime: '19.00 – 20.30 WIB',
        pickupDeadlineInfo: 'Hari ini · tutup 21.00 WIB',
        address: 'Jl. Semolowaru No. 8',
        image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=1200&q=80',
        batchCooked: '16.00 WIB',
        warmerGrade: 'Dandang Termal Grade A',
        co2Saved: '0,75 kg CO2e',
        description: 'Kuah soto kuning gurih berkaldu ayam kampung dengan taburan koya udang renyah, bihun, kubis dan telur rebus.',
        packages: [
            { icon: 'soup', title: 'Kuah Soto Panas Berkaldu' },
            { icon: 'chicken', title: 'Ayam Suwir Lembut' },
            { icon: 'cracker', title: 'Koya Udang Asli Gurih' },
            { icon: 'sambal', title: 'Sambal Cabe Rawit Hijau' }
        ],
        tags: ['Higienis & Halal', 'Kuah Hangat'],
        dietaryTags: ['Halal', 'Tidak Pedas', 'Bebas Kacang'],
        allergens: ['Ebi / Udang', 'Telur'],
        minutesLeft: 18
    },
    {
        id: 4,
        slug: 'ayam-geprek-sambal-korek',
        name: 'Ayam Geprek Crispy Sambal Korek Bawang',
        shortName: 'Ayam Geprek Gebang',
        merchant: 'Geprek Mas Bro Sukolilo',
        merchantInitial: 'MB',
        merchantActiveSince: 'April 2023',
        merchantRescuedCount: '1.750+',
        rating: 4.9,
        reviewsCount: 180,
        discountPercent: 50,
        priceDiscount: 10000,
        priceOriginal: 20000,
        stock: 4,
        distance: '0,6 km',
        distanceMins: '4 mnt jalan kaki dari Gerbang ITS',
        category: 'Nasi',
        campusZone: 'its-sukolilo',
        merchantZoneLabel: 'Gebang (Dekat ITS)',
        pickupTime: '18.30 – 21.00 WIB',
        pickupDeadlineInfo: 'Hari ini · tutup 21.30 WIB',
        address: 'Jl. Gebang Putih No. 22',
        image: 'https://images.unsplash.com/photo-1562967914-608f82629710?auto=format&fit=crop&w=1200&q=80',
        batchCooked: '17.15 WIB',
        warmerGrade: 'Food Warmer Grade A',
        co2Saved: '0,80 kg CO2e',
        description: 'Ayam goreng crispy digeprek dengan sambal korek bawang pedas gurih, disajikan dengan nasi hangat dan lalapan timun.',
        packages: [
            { icon: 'rice', title: '1 Porsi Nasi Putih Hangat' },
            { icon: 'chicken', title: 'Ayam Crispy Geprek Paha/Dada' },
            { icon: 'sambal', title: 'Sambal Korek Bawang Pedas' },
            { icon: 'salad', title: 'Timun Segar' }
        ],
        tags: ['Pedas Mantap', 'Favorit Mahasiswa ITS'],
        dietaryTags: ['Halal', 'Pedas', 'Bebas Kacang'],
        allergens: ['Ayam', 'Bawang'],
        minutesLeft: 45
    },
    {
        id: 5,
        slug: 'bebek-goreng-dharmawangsa',
        name: 'Bebek Goreng Rempah Sambal Pencit',
        shortName: 'Bebek Goreng Rempah',
        merchant: 'Bebek Bu Endang UNAIR',
        merchantInitial: 'BE',
        merchantActiveSince: 'November 2022',
        merchantRescuedCount: '2.400+',
        rating: 4.8,
        reviewsCount: 165,
        discountPercent: 46,
        priceDiscount: 14000,
        priceOriginal: 26000,
        stock: 3,
        distance: '0,7 km',
        distanceMins: '5 mnt dari Kampus B UNAIR',
        category: 'Nasi',
        campusZone: 'unair-b',
        merchantZoneLabel: 'Dharmawangsa (Dekat UNAIR B)',
        pickupTime: '19.00 – 21.00 WIB',
        pickupDeadlineInfo: 'Hari ini · tutup 21.30 WIB',
        address: 'Jl. Dharmawangsa No. 34',
        image: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=1200&q=80',
        batchCooked: '16.45 WIB',
        warmerGrade: 'Food Warmer Grade A',
        co2Saved: '0,90 kg CO2e',
        description: 'Bebek goreng empuk berbumbu rempah serundeng gurih khas Madura, lengkap dengan sambal mangga muda (pencit).',
        packages: [
            { icon: 'rice', title: '1 Porsi Nasi Putih' },
            { icon: 'chicken', title: 'Bebek Goreng Serundeng' },
            { icon: 'sambal', title: 'Sambal Pencit Asam Pedas' },
            { icon: 'salad', title: 'Kemangi & Timun' }
        ],
        tags: ['Gurih Renyah', 'Favorit Mahasiswa UNAIR'],
        dietaryTags: ['Halal', 'Pedas', 'Bebas Kacang'],
        allergens: ['Bebek'],
        minutesLeft: 30
    },
    {
        id: 6,
        slug: 'mie-goreng-jawa',
        name: 'Mie Goreng Jawa Spesial Telur Orak-Arik',
        shortName: 'Mie Goreng Jawa',
        merchant: 'Bakmi Pak No',
        merchantInitial: 'PN',
        merchantActiveSince: 'Mei 2023',
        merchantRescuedCount: '980+',
        rating: 4.6,
        reviewsCount: 84,
        discountPercent: 42,
        priceDiscount: 14000,
        priceOriginal: 24000,
        stock: 4,
        distance: '1,5 km',
        distanceMins: '8 mnt dari Kampus UNESA Lidah Wetan',
        category: 'Mie',
        campusZone: 'unesa-lidah',
        merchantZoneLabel: 'Lidah Wetan (Dekat UNESA)',
        pickupTime: '20.00 – 21.30 WIB',
        pickupDeadlineInfo: 'Hari ini · tutup 22.00 WIB',
        address: 'Jl. Lidah Wetan Raya No. 19',
        image: 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=1200&q=80',
        batchCooked: '17.30 WIB',
        warmerGrade: 'Food Warmer Grade A',
        co2Saved: '0,65 kg CO2e',
        description: 'Mie telur kenyal dimasak dengan bumbu kecap manis gurih, ebi, sawi segar, suwiran ayam dan acar timun segar.',
        packages: [
            { icon: 'noodles', title: '1 Porsi Mie Goreng Jumbo' },
            { icon: 'egg', title: 'Telur & Suwir Ayam' },
            { icon: 'salad', title: 'Acar Mentimun Segar' },
            { icon: 'cracker', title: 'Kerupuk Bawang' }
        ],
        tags: ['Porsi Kenyang', 'Favorit Malam'],
        dietaryTags: ['Halal', 'Tidak Pedas', 'Bebas Kacang'],
        allergens: ['Gluten / Gandum', 'Telur', 'Ebi'],
        minutesLeft: 50
    },
    {
        id: 7,
        slug: 'paket-gorengan-10pc',
        name: 'Paket Gorengan Crispy Campur 10 Pcs',
        shortName: 'Paket Gorengan 10pc',
        merchant: 'Gorengan Bu Tin Rungkut',
        merchantInitial: 'BT',
        merchantActiveSince: 'Agustus 2023',
        merchantRescuedCount: '1.800+',
        rating: 4.8,
        reviewsCount: 160,
        discountPercent: 53,
        priceDiscount: 7000,
        priceOriginal: 15000,
        stock: 8,
        distance: '0,8 km',
        distanceMins: '5 mnt jalan kaki dari UPN',
        category: 'Camilan',
        campusZone: 'upn-rungkut',
        merchantZoneLabel: 'Rungkut (Dekat UPN)',
        pickupTime: '17.30 – 19.00 WIB',
        pickupDeadlineInfo: 'Hari ini · tutup 19.30 WIB',
        address: 'Jl. Rungkut Madya No. 15',
        image: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=1200&q=80',
        batchCooked: '16.15 WIB',
        warmerGrade: 'Etalase Hangat',
        co2Saved: '0,40 kg CO2e',
        description: 'Aneka gorengan tahu isi sayur, tempe mendoan, bakwan jagung dan pisang coklat crispy lengkap dengan cabe rawit.',
        packages: [
            { icon: 'snack', title: '10 Pcs Gorengan Hangat' },
            { icon: 'sambal', title: 'Cabai Rawit Hijau Segar' },
            { icon: 'sauce', title: 'Bumbu Petis Khas Jatim' }
        ],
        tags: ['Garing & Hangat', 'Teman Nugas Mahasiswa UPN'],
        dietaryTags: ['Halal', 'Vegetarian', 'Tidak Pedas'],
        allergens: ['Gluten / Gandum', 'Kedelai', 'Petis Udang'],
        minutesLeft: 22
    },
    {
        id: 8,
        slug: 'nasi-kulit-siwalankerto',
        name: 'Nasi Kulit Crispy Sambal Bawang Spesial',
        shortName: 'Nasi Kulit Crispy',
        merchant: 'Kedai Siwalankerto Petra',
        merchantInitial: 'KS',
        merchantActiveSince: 'Januari 2023',
        merchantRescuedCount: '1.600+',
        rating: 4.8,
        reviewsCount: 142,
        discountPercent: 48,
        priceDiscount: 11000,
        priceOriginal: 21000,
        stock: 5,
        distance: '0,5 km',
        distanceMins: '3 mnt jalan kaki dari UK Petra',
        category: 'Nasi',
        campusZone: 'petra-siwalankerto',
        merchantZoneLabel: 'Siwalankerto (Dekat Petra)',
        pickupTime: '18.00 – 20.30 WIB',
        pickupDeadlineInfo: 'Hari ini · tutup 21.00 WIB',
        address: 'Jl. Siwalankerto VIII No. 10',
        image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=1200&q=80',
        batchCooked: '16.30 WIB',
        warmerGrade: 'Food Warmer Grade A',
        co2Saved: '0,70 kg CO2e',
        description: 'Kulit ayam goreng super renyah dibumbui gurih, serundeng kelapa, telur ceplok dan sambal bawang pedas nampol.',
        packages: [
            { icon: 'rice', title: '1 Porsi Nasi Pulen' },
            { icon: 'chicken', title: 'Kulit Ayam Super Crispy' },
            { icon: 'egg', title: 'Telur Ceplok Setengah Matang' },
            { icon: 'sambal', title: 'Sambal Bawang Pedas Gurih' }
        ],
        tags: ['Super Crispy', 'Favorit Mahasiswa Petra'],
        dietaryTags: ['Halal', 'Pedas', 'Bebas Kacang'],
        allergens: ['Telur', 'Bawang'],
        minutesLeft: 40
    },
    {
        id: 9,
        slug: 'roti-sobek-isi-6',
        name: 'Roti Sobek Lembut Isi Cokelat & Keju',
        shortName: 'Roti Sobek Isi 6',
        merchant: 'Roti Ketintang',
        merchantInitial: 'RK',
        merchantActiveSince: 'Februari 2023',
        merchantRescuedCount: '3.400+',
        rating: 4.9,
        reviewsCount: 310,
        discountPercent: 59,
        priceDiscount: 9000,
        priceOriginal: 22000,
        stock: 6,
        distance: '1,5 km',
        distanceMins: '10 mnt dari Kampus UNESA Ketintang',
        category: 'Roti',
        campusZone: 'unesa-ketintang',
        merchantZoneLabel: 'Ketintang (Dekat UNESA)',
        pickupTime: '17.00 – 19.30 WIB',
        pickupDeadlineInfo: 'Hari ini · tutup 20.00 WIB',
        address: 'Jl. Ketintang Baru Selatan No. 3',
        image: 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=1200&q=80',
        batchCooked: '11.00 WIB',
        warmerGrade: 'Display Sealed Box',
        co2Saved: '0,55 kg CO2e',
        description: 'Roti sobek butter super lembut panggang pagi hari dengan isian dark chocolate lumer dan cheddar cheese gurih.',
        packages: [
            { icon: 'bread', title: '1 Kotak Roti Sobek 6 Porsi' },
            { icon: 'cheese', title: 'Isian Cokelat Belgia & Keju' }
        ],
        tags: ['Tanpa Pengawet', 'Camilan Manis'],
        dietaryTags: ['Halal', 'Vegetarian', 'Tidak Pedas', 'Bebas Kacang'],
        allergens: ['Gluten / Gandum', 'Susu / Mentega', 'Keju'],
        minutesLeft: 42
    }
];

// Inisialisasi Mock Pesanan (Dikosongkan untuk testing alur bersih)
export const initialOrders = [];

export function getSavedOrders(currentUserId = null, currentUserEmail = null) {
    if (typeof window === 'undefined') return [];
    if (localStorage.getItem('plusfood_orders_cleared') === 'true') return [];
    try {
        const data = localStorage.getItem('plusfood_orders');
        let orders = data ? JSON.parse(data) : [];
        if (!Array.isArray(orders)) return [];

        if (currentUserId || currentUserEmail) {
            orders = orders.filter(o => {
                const oUserId = o.user_id || o.userId;
                const oEmail = o.customer_email || o.email || o.userEmail;
                if (oUserId && currentUserId && Number(oUserId) !== Number(currentUserId)) return false;
                if (oEmail && currentUserEmail && String(oEmail).trim().toLowerCase() !== String(currentUserEmail).trim().toLowerCase()) return false;
                return true;
            });
        }

        return orders;
    } catch (e) {
        return [];
    }
}

let isSavingOrders = false;
let isSyncingOrders = false;

export function saveOrders(orders) {
    if (typeof window === 'undefined' || isSavingOrders) return;
    isSavingOrders = true;
    try {
        localStorage.setItem('plusfood_orders', JSON.stringify(orders));
        // Kirim event secara asinkron agar tidak memblokir render / navigasi Inertia
        setTimeout(() => {
            if (typeof window !== 'undefined') {
                window.dispatchEvent(new CustomEvent('plusfood_order_status_updated'));
            }
        }, 0);
    } catch (e) {
        console.warn('Gagal menyimpan pesanan ke localStorage:', e);
    } finally {
        isSavingOrders = false;
    }
}

export function addOrder(newOrder) {
    if (typeof window !== 'undefined') {
        localStorage.removeItem('plusfood_orders_cleared');
    }
    const orderWithDate = {
        ...newOrder,
        created_at: newOrder.created_at || new Date().toISOString(),
        createdAt: newOrder.createdAt || newOrder.created_at || new Date().toISOString()
    };
    let allOrders = [];
    try {
        const data = typeof window !== 'undefined' ? localStorage.getItem('plusfood_orders') : null;
        allOrders = data ? JSON.parse(data) : [];
        if (!Array.isArray(allOrders)) allOrders = [];
    } catch (e) {
        allOrders = [];
    }
    const updated = [orderWithDate, ...allOrders.filter(o => o.id !== orderWithDate.id)];
    saveOrders(updated);
    return updated;
}

export function syncLocalOrdersWithDb(dbOrders = [], currentUserId = null, currentUserEmail = null) {
    if (typeof window !== 'undefined' && localStorage.getItem('plusfood_orders_cleared') === 'true') {
        return [];
    }
    if (isSyncingOrders) return getSavedOrders(currentUserId, currentUserEmail);
    isSyncingOrders = true;
    try {
        const local = getSavedOrders(currentUserId, currentUserEmail);
        if (!Array.isArray(local) || local.length === 0) return [];

        const extractDigits = (val) => {
            if (!val) return '';
            const match = String(val).match(/\d{4,6}/);
            return match ? match[0] : String(val).toLowerCase().replace(/[^a-z0-9]/g, '').replace(/^pf/, '');
        };

        let changed = false;
        local.forEach(loc => {
            const lDigits = extractDigits(loc.id || loc.order_number || loc.manualCode || loc.qrCode);
            const lClean = String(loc.id || loc.order_number || '').toLowerCase().replace(/[#\s]/g, '');

            const matchDb = (dbOrders || []).find(db => {
                const dbDigits = extractDigits(db.order_number || db.id || db.qr_token || db.qrCode);
                const dbClean = String(db.order_number || db.id || '').toLowerCase().replace(/[#\s]/g, '');
                return (lDigits && dbDigits && lDigits === dbDigits) || (lClean && dbClean && lClean === dbClean);
            });

            if (matchDb) {
                const rawDbStatus = matchDb.status;
                const normalizedDbStatus = (rawDbStatus === 'batal' || rawDbStatus === 'dibatalkan') ? 'dibatalkan' : rawDbStatus;
                if (normalizedDbStatus && loc.status !== normalizedDbStatus) {
                    loc.status = normalizedDbStatus;
                    loc.statusLabel = normalizedDbStatus === 'selesai' ? 'Selesai Diambil' : (normalizedDbStatus === 'dibatalkan' ? 'Dibatalkan' : 'Menunggu Pickup');
                    changed = true;
                }
            } else if ((dbOrders || []).some(d => d.status === 'selesai')) {
                const completedDb = (dbOrders || []).find(d => d.status === 'selesai');
                if (completedDb && (loc.status === 'menunggu' || loc.status === 'siap_ambil')) {
                    loc.status = 'selesai';
                    loc.statusLabel = 'Selesai Diambil';
                    loc.order_number = completedDb.order_number || loc.order_number;
                    changed = true;
                }
            }
        });

        if (changed) {
            saveOrders(local);
        }

        return local;
    } finally {
        isSyncingOrders = false;
    }
}

export function updateOrderStatus(orderId, status) {
    const orders = getSavedOrders();
    const extractDigits = (val) => {
        if (!val) return '';
        const match = String(val).match(/\d{4,6}/);
        return match ? match[0] : String(val).toLowerCase().replace(/[^a-z0-9]/g, '').replace(/^pf/, '');
    };

    const targetDigits = extractDigits(orderId);
    let matched = false;
    let foundOrder = null;

    orders.forEach(o => {
        const oDigits = extractDigits(o.id || o.order_number || o.manualCode || o.qrCode);
        if ((targetDigits && oDigits && targetDigits === oDigits) || 
            String(o.id || '').toLowerCase() === String(orderId || '').toLowerCase() || 
            String(o.order_number || '').toLowerCase() === String(orderId || '').toLowerCase()) {
            o.status = status === 'batal' ? 'dibatalkan' : status;
            o.statusLabel = status === 'selesai' ? 'Selesai Diambil' : (status === 'batal' || status === 'dibatalkan' ? 'Dibatalkan' : 'Menunggu Pickup');
            matched = true;
            foundOrder = o;
        }
    });

    if (matched) {
        saveOrders(orders);
        return { success: true, order: foundOrder };
    }
    return { success: false, message: 'Tiket tidak ditemukan / salah kode' };
}

// Sistem Ulasan Berhadiah (+15 Green Points) sesuai Desain Sistem POV Mahasiswa
export function addOrderReview(orderId, reviewData) {
    const orders = getSavedOrders();
    const cleanDigits = (val) => String(val || '').replace(/[^0-9]/g, '');
    const targetDigits = cleanDigits(orderId);
    const targetStr = String(orderId || '').toLowerCase().trim();

    let found = orders.find(o => {
        const oDigits = cleanDigits(o.id || o.order_number || o.manualCode);
        const oStr = String(o.id || o.order_number || o.manualCode || '').toLowerCase().trim();
        return (targetDigits && oDigits && targetDigits === oDigits) || oStr === targetStr;
    });

    const reviewPayload = {
        rating: reviewData.rating || 5,
        tags: reviewData.tags || [],
        comment: reviewData.comment || '',
        date: 'Hari ini',
        pointsEarned: 15
    };

    if (found) {
        found.isReviewed = true;
        found.hasReview = true;
        found.review = reviewPayload;
    } else {
        found = {
            id: orderId,
            order_number: orderId,
            status: 'selesai',
            isReviewed: true,
            hasReview: true,
            review: reviewPayload
        };
        orders.push(found);
    }
    saveOrders(orders);

    if (typeof window !== 'undefined') {
        const delta = parseInt(localStorage.getItem('plusfood_merchant_review_delta') || '0', 10);
        localStorage.setItem('plusfood_merchant_review_delta', String(delta + 1));
        window.dispatchEvent(new CustomEvent('plusfood_review_added', { detail: { orderId, review: reviewPayload } }));
        window.dispatchEvent(new Event('storage'));
    }

    return { success: true, pointsEarned: 15 };
}

// Mapper untuk mengubah data relasi Order Eloquent Laravel ke objek Pesanan Frontend
export function mapDbOrder(db) {
    if (!db) return null;
    const firstItem = db.items && db.items.length > 0 ? db.items[0] : null;
    const foodItem = firstItem?.food || {};
    const merchantName = db.merchant?.name || (typeof foodItem.merchant === 'string' ? foodItem.merchant : foodItem.merchant?.name) || 'Mitra Kampus';
    return {
        id: db.order_number,
        customerName: db.customer_name,
        nim: db.customer_nim,
        whatsapp: db.customer_phone,
        email: db.customer_email,
        address: db.customer_address,
        note: db.notes || '',
        food: {
            id: foodItem.id || firstItem?.food_id,
            name: firstItem?.food_name || foodItem.name || 'Makanan Surplus',
            merchant: merchantName,
            merchantName: merchantName,
            image: foodItem.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80',
            priceOriginal: foodItem.price_original || 25000,
            priceDiscount: firstItem?.price || 12000,
            price: foodItem.price_original || 25000,
            pickupTime: db.pickup_time,
            pickupDeadlineInfo: db.pickup_deadline,
            address: foodItem.address || db.merchant?.address || 'Surabaya',
        },
        quantity: firstItem?.quantity || 1,
        ecoPackaging: firstItem?.eco_packaging || 'bawa_sendiri',
        ecoPackagingLabel: firstItem?.eco_packaging === 'bawa_sendiri' ? 'Bawa Wadah Sendiri' : firstItem?.eco_packaging === 'kotak_kertas' ? 'Kotak Kertas' : 'Thinwall',
        subtotal: Number(db.total_items_price || 0),
        packagingFee: Number(db.total_packaging_fee || 0),
        total: Number(db.total_price || db.total || 0),
        total_price: Number(db.total_price || db.total || 0),
        pickupTime: db.pickup_time,
        pickupDeadline: db.pickup_deadline,
        date: db.created_at ? new Date(db.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Hari ini',
        created_at: db.created_at || new Date().toISOString(),
        createdAt: db.created_at || new Date().toISOString(),
        paymentMethod: db.payment?.payment_method || 'qris',
        paymentGateway: db.payment?.payment_gateway || 'sandbox_simulator',
        paymentReference: db.payment?.payment_reference || ('SBX-QRIS-' + db.order_number),
        paymentStatus: db.payment?.status || (db.status === 'menunggu' ? 'lunas' : 'menunggu_bayar'),
        paidAt: db.payment?.paid_at,
        status: db.status === 'batal' ? 'dibatalkan' : db.status,
        statusLabel: db.status === 'menunggu' ? 'Menunggu Pickup' : db.status === 'siap_ambil' ? 'Siap Diambil' : db.status === 'selesai' ? 'Selesai' : 'Dibatalkan',
        qrCode: db.qr_token || (db.order_number + '-PF'),
        manualCode: db.order_number,
        mapsUrl: 'https://maps.google.com/?q=-7.3115,112.7275',
        hasReview: Boolean(db.has_review),
        isReviewed: Boolean(db.has_review),
    };
}

// --- KELOLA MENU SURPLUS CUSTOM (LOCALSTORAGE SINKRONISASI MAHASISWA) ---
export function getCustomFoods() {
    if (typeof window === 'undefined') return [];
    try {
        const data = localStorage.getItem('plusfood_custom_foods');
        return data ? JSON.parse(data) : [];
    } catch (e) {
        return [];
    }
}

export function saveCustomFood(foodItem) {
    if (typeof window === 'undefined') return [];
    try {
        const current = getCustomFoods();
        // Cegah duplikasi berdasarkan id atau slug
        const filtered = current.filter(item => String(item.id) !== String(foodItem.id) && item.slug !== foodItem.slug);
        const updated = [foodItem, ...filtered];
        localStorage.setItem('plusfood_custom_foods', JSON.stringify(updated));
        
        // Dispatch storage event manual untuk tab yang sama jika dibutuhkan
        window.dispatchEvent(new Event('plusfood_custom_foods_updated'));
        window.dispatchEvent(new Event('storage'));
        return updated;
    } catch (e) {
        console.error('Gagal menyimpan menu surplus custom:', e);
        return [];
    }
}

export function deleteCustomFood(foodId) {
    if (typeof window === 'undefined') return [];
    try {
        const current = getCustomFoods();
        const targetStr = String(foodId).toLowerCase().trim();
        const updated = current.filter(item => 
            String(item.id).toLowerCase().trim() !== targetStr && 
            String(item.slug || '').toLowerCase().trim() !== targetStr &&
            String(item.name || '').toLowerCase().trim() !== targetStr
        );
        localStorage.setItem('plusfood_custom_foods', JSON.stringify(updated));
        window.dispatchEvent(new Event('plusfood_custom_foods_updated'));
        window.dispatchEvent(new Event('storage'));
        return updated;
    } catch (e) {
        return [];
    }
}

export function decrementCustomFoodStock(targetIdentifier, qty = 1) {
    if (typeof window === 'undefined') return [];
    try {
        const current = getCustomFoods();
        const targetStr = String(targetIdentifier).toLowerCase();
        let changed = false;
        current.forEach(item => {
            if (
                String(item.id).toLowerCase() === targetStr ||
                String(item.slug || '').toLowerCase() === targetStr ||
                String(item.name || '').trim().toLowerCase() === targetStr
            ) {
                const currentStock = Number(item.stock ?? item.portions ?? 1);
                const newStock = Math.max(0, currentStock - qty);
                item.stock = newStock;
                item.portions = newStock;
                if (newStock <= 0) {
                    item.status = 'habis';
                }
                changed = true;
            }
        });
        if (changed) {
            localStorage.setItem('plusfood_custom_foods', JSON.stringify(current));
            window.dispatchEvent(new Event('plusfood_custom_foods_updated'));
            window.dispatchEvent(new Event('storage'));
        }
        return current;
    } catch (e) {
        return [];
    }
}

export function getAllFoods() {
    return [...getCustomFoods(), ...sampleFoods];
}

// --- SISTEM PENCAIRAN SALDO MITRA (PAYOUT / WITHDRAWAL) ---
export const initialPayoutHistory = [
    {
        id: 'WD-849102',
        amount: 150000,
        bankName: 'Bank Mandiri',
        bankAccount: '1420-0192-3841',
        bankHolder: 'Sari Wulandari',
        status: 'selesai',
        statusLabel: 'Berhasil Masuk',
        date: 'Kemarin, 21.15 WIB',
        referenceId: 'TRX-MDR-99281'
    },
    {
        id: 'WD-739201',
        amount: 220000,
        bankName: 'Bank Mandiri',
        bankAccount: '1420-0192-3841',
        bankHolder: 'Sari Wulandari',
        status: 'selesai',
        statusLabel: 'Berhasil Masuk',
        date: '2 hari lalu, 20.40 WIB',
        referenceId: 'TRX-MDR-88192'
    }
];

export function getPayoutHistory(merchantId = null) {
    if (typeof window === 'undefined') return [];
    try {
        const key = merchantId ? `plusfood_payout_history_${merchantId}` : 'plusfood_payout_history';
        const data = localStorage.getItem(key);
        if (!data) {
            if (String(merchantId) === '1' || merchantId === 'warung-bu-sari') {
                localStorage.setItem(key, JSON.stringify(initialPayoutHistory));
                return initialPayoutHistory;
            }
            return [];
        }
        return JSON.parse(data);
    } catch (e) {
        return [];
    }
}

export function addPayoutRecord(record, merchantId = null) {
    if (typeof window === 'undefined') return [];
    try {
        const key = merchantId ? `plusfood_payout_history_${merchantId}` : 'plusfood_payout_history';
        const history = getPayoutHistory(merchantId);
        const updated = [record, ...history];
        localStorage.setItem(key, JSON.stringify(updated));
        return updated;
    } catch (e) {
        return [];
    }
}

// --- SISTEM SOUND NOTIFIKASI WEB AUDIO API (ORDER CHIME TING-TING) ---
export function getSoundPreference() {
    if (typeof window === 'undefined') return true;
    try {
        const val = localStorage.getItem('plusfood_sound_enabled');
        return val !== null ? JSON.parse(val) : true;
    } catch (e) {
        return true;
    }
}

export function saveSoundPreference(enabled) {
    if (typeof window !== 'undefined') {
        try {
            localStorage.setItem('plusfood_sound_enabled', JSON.stringify(enabled));
        } catch (e) {}
    }
}

export function playOrderChime() {
    if (typeof window === 'undefined') return;
    try {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) return;
        const ctx = new AudioContextClass();
        if (ctx.state === 'suspended') {
            ctx.resume();
        }

        const playTone = (freq, delay, duration, gainLevel = 0.28) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'triangle'; // triangle gives a warm chime bell feel
            osc.frequency.setValueAtTime(freq, ctx.currentTime + delay);

            gain.gain.setValueAtTime(gainLevel, ctx.currentTime + delay);
            gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + delay + duration);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(ctx.currentTime + delay);
            osc.stop(ctx.currentTime + delay + duration);
        };

        // Chime "ting-ting" melodis 2 nada ceria (E6 -> A6)
        playTone(1318.51, 0, 0.45, 0.28);
        playTone(1760.00, 0.13, 0.65, 0.32);
    } catch (e) {
        console.warn('AudioContext gagal memutar order chime:', e);
    }
}
