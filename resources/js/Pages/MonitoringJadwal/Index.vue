<script setup>
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import { Head, router, Link } from '@inertiajs/vue3';
    import { ref, watch } from 'vue';

    const props = defineProps({
        jadwalBimbingans: {
            type: Array,
            default: () => [],
        },
        filters: {
            type: Object,
            default: () => ({ status: 'all', search: '' }),
        },
    });

    // Reactive filter state diambil dari props (server)
    const selectedStatus = ref(props.filters.status);
    const searchQuery = ref(props.filters.search);

    // Daftar opsi status filter
    const statusOptions = [
        { value: 'all', label: 'Semua Status' },
        { value: 'pending', label: 'Menunggu Konfirmasi' },
        { value: 'approved', label: 'Disetujui' },
        { value: 'rejected', label: 'Ditolak' },
        { value: 'completed', label: 'Selesai' },
        { value: 'canceled', label: 'Dibatalkan' },
    ];

    // Fungsi apply filter ke server via Inertia (partial reload)
    const applyFilters = () => {
        router.get(
            route('mahasiswa.jadwal.index'),
            {
                status: selectedStatus.value !== 'all' ? selectedStatus.value : undefined,
                search: searchQuery.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    // Reset filter
    const resetFilters = () => {
        selectedStatus.value = 'all';
        searchQuery.value = '';
        applyFilters();
    };

    // Debounce search agar tidak terlalu banyak request saat mengetik
    let searchTimeout = null;
    watch(searchQuery, () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 400);
    });

    // Apply langsung saat status berubah
    watch(selectedStatus, () => {
        applyFilters();
    });

    const statusLabel = (status) => {
        const labels = {
            pending: 'MENUNGGU KONFIRMASI',
            approved: 'DISETUJUI',
            rejected: 'DITOLAK',
            completed: 'SELESAI',
            canceled: 'DIBATALKAN',
        };
        return labels[status] || status.toUpperCase();
    };

    const statusColor = (status) => {
        const colors = {
            pending: 'bg-yellow-100 text-yellow-800',
            approved: 'bg-green-100 text-green-800',
            rejected: 'bg-red-100 text-red-800',
            completed: 'bg-blue-100 text-blue-800',
            canceled: 'bg-gray-100 text-gray-800',
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    };

    const tipeLabel = (tipe) => {
        if (!tipe) return 'BELUM DITENTUKAN';
        return tipe === 'online' ? 'ONLINE' : 'OFFLINE';
    };
    const tipeColor = (tipe) => {
        if (!tipe) return 'text-yellow-600';
        return tipe === 'online' ? 'text-blue-600' : 'text-gray-600';
    };

    const cancelJadwal = (id) => {
        if (confirm('Apakah Anda yakin ingin membatalkan jadwal ini?')) {
            router.patch(route('mahasiswa.jadwal.cancel', id));
        }
    };

    // Cek apakah ada filter aktif
    const hasActiveFilter = () => selectedStatus.value !== 'all' || searchQuery.value !== '';
</script>

<template>
    <Head title="Monitoring Jadwal Bimbingan" />

    <StudentLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-5 w-5 text-green-400"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ $page.props.flash.success }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Header Section -->
                <div
                    class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-bold text-indigo-600">
                            Monitoring Jadwal Bimbingan
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Pantau dan kelola jadwal bimbingan akademik Anda.
                        </p>
                    </div>
<div class="flex items-center gap-3">
    <Link :href="route('mahasiswa.jadwal-bimbingan.create')" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all">
        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Ajukan Jadwal
    </Link>
    <a :href="route('mahasiswa.jadwal.exportPdf', { status: selectedStatus !== 'all' ? selectedStatus : undefined, search: searchQuery || undefined })" class="inline-flex items-center space-x-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        <span>Export PDF</span>
    </a>
</div>
                        <div
                            class="flex items-center space-x-2 rounded-lg border border-gray-200 bg-white px-4 py-2 shadow-sm"
                        >
                            <svg
                                class="h-5 w-5 text-indigo-500"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                                />
                            </svg>
                            <span class="text-sm font-medium text-gray-700"
                                >Total: {{ jadwalBimbingans.length }} Jadwal</span
                            >
                        </div>
                    </div>

                <!-- Filter & Search Bar -->
                <div class="mb-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <!-- Search Input -->
                            <div class="relative flex-1">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                                >
                                    <svg
                                        class="h-4 w-4 text-gray-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                                        />
                                    </svg>
                                </div>
                                <input
                                    id="search-jadwal"
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Cari topik bimbingan atau nama dosen..."
                                    class="block w-full rounded-md border-gray-300 pl-9 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <!-- Status Filter Dropdown -->
                            <div class="flex items-center gap-2">
                                <label
                                    for="filter-status"
                                    class="flex items-center gap-1.5 text-sm font-medium text-gray-600 whitespace-nowrap"
                                >
                                    <svg
                                        class="h-4 w-4 text-gray-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"
                                        />
                                    </svg>
                                    Filter Status:
                                </label>
                                <select
                                    id="filter-status"
                                    v-model="selectedStatus"
                                    class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option
                                        v-for="opt in statusOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Reset Button (muncul hanya jika ada filter aktif) -->
                            <button
                                v-if="hasActiveFilter()"
                                class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-600 shadow-sm hover:bg-gray-50 transition-colors"
                                @click="resetFilters"
                            >
                                <svg
                                    class="h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                                Reset
                            </button>
                        </div>

                        <!-- Active Filter Badge -->
                        <div v-if="hasActiveFilter()" class="mt-2 flex flex-wrap gap-2">
                            <span
                                v-if="selectedStatus !== 'all'"
                                class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800"
                            >
                                Status:
                                {{ statusOptions.find((o) => o.value === selectedStatus)?.label }}
                            </span>
                            <span
                                v-if="searchQuery"
                                class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800"
                            >
                                Kata kunci: "{{ searchQuery }}"
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Informasi Bimbingan
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Dosen Pembimbing
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Mahasiswa
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Tindakan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr
                                    v-for="jadwal in jadwalBimbingans"
                                    :key="jadwal.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <!-- Informasi Bimbingan -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center text-xs text-gray-400 mb-1">
                                            <svg
                                                class="mr-1 h-3.5 w-3.5"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                                                />
                                            </svg>
                                            {{ jadwal.tanggal }} • {{ jadwal.waktu }} WIB
                                        </div>
                                        <div class="text-sm font-bold text-gray-900 mb-1">
                                            {{ jadwal.judul_ta }}
                                        </div>
                                        <div class="text-sm font-medium text-gray-700">
                                            {{ jadwal.topik_bimbingan }}
                                        </div>
                                        <span
                                            :class="tipeColor(jadwal.tipe)"
                                            class="mt-1 inline-block text-xs font-semibold uppercase"
                                        >
                                            {{ tipeLabel(jadwal.tipe) }}
                                        </span>
                                    </td>

                                    <!-- Dosen Pembimbing -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-500 text-sm font-bold text-white"
                                            >
                                                D
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ jadwal.dosen?.nama_lengkap || 'N/A' }}
                                                </div>
                                                <div class="text-xs text-indigo-500">
                                                    Dosen Pembimbing
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Mahasiswa -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-green-500 text-sm font-bold text-white"
                                            >
                                                A
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ jadwal.mahasiswa?.nama_lengkap || 'N/A' }}
                                                </div>
                                                <div class="text-xs text-green-500">Mahasiswa</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            :class="statusColor(jadwal.status)"
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                        >
                                            {{ statusLabel(jadwal.status) }}
                                        </span>
                                    </td>

                                    <!-- Tindakan -->
                                    <td class="px-6 py-4 text-center">
                                        <template v-if="jadwal.status === 'pending'">
                                            <button
                                                class="inline-flex items-center rounded-md border border-red-300 bg-white px-3 py-1.5 text-xs font-medium text-red-700 shadow-sm hover:bg-red-50 transition-colors"
                                                @click="cancelJadwal(jadwal.id)"
                                            >
                                                Batalkan
                                            </button>
                                        </template>
                                        <template v-else>
                                            <span
                                                class="inline-flex items-center rounded-md border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-medium text-gray-400"
                                            >
                                                Terkunci
                                            </span>
                                        </template>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="jadwalBimbingans.length === 0">
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg
                                            class="mx-auto h-12 w-12 text-gray-300"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                                            />
                                        </svg>
                                        <p class="mt-2 text-sm font-medium text-gray-500">
                                            Tidak ada jadwal bimbingan ditemukan.
                                        </p>
                                        <p
                                            v-if="hasActiveFilter()"
                                            class="mt-1 text-xs text-gray-400"
                                        >
                                            Coba ubah atau reset filter pencarian Anda.
                                        </p>
                                        <p v-else class="mt-1 text-xs text-gray-400">
                                            Belum ada jadwal yang diajukan.
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
