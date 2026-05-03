<script>
    const mockKalender = [
        {
            id: 1,
            nama_kegiatan: 'Fall Semester Starts',
            tipe_kegiatan: 'semester',
            tanggal_mulai: '2023-10-02',
            jam_mulai: '08:00',
            tanggal_selesai: '2023-10-02',
            deskripsi: 'Awal semester baru.',
            status: 'Active',
        },
        {
            id: 2,
            nama_kegiatan: 'Midterm Exams Week',
            tipe_kegiatan: 'uts',
            tanggal_mulai: '2023-11-13',
            jam_mulai: '09:00',
            tanggal_selesai: '2023-11-17',
            deskripsi: 'Pelaksanaan UTS.',
            status: 'Active',
        },
        {
            id: 3,
            nama_kegiatan: 'Winter Break',
            tipe_kegiatan: 'libur',
            tanggal_mulai: '2023-12-18',
            jam_mulai: '00:00',
            tanggal_selesai: '2024-01-02',
            deskripsi: 'Libur semester musim dingin.',
            status: 'Pending',
        },
        {
            id: 4,
            nama_kegiatan: 'Spring Registration',
            tipe_kegiatan: 'krs',
            tanggal_mulai: '2024-01-08',
            jam_mulai: '08:30',
            tanggal_selesai: '2024-01-19',
            deskripsi: 'Pendaftaran kuliah semester berikutnya.',
            status: 'Active',
        },
        {
            id: 5,
            nama_kegiatan: 'Commencement 2024',
            tipe_kegiatan: 'wisuda',
            tanggal_mulai: '2024-05-20',
            jam_mulai: '10:00',
            tanggal_selesai: '2024-05-20',
            deskripsi: 'Upacara wisuda tahunan.',
            status: 'Active',
        },
        {
            id: 6,
            nama_kegiatan: 'Programmer Story Week',
            tipe_kegiatan: 'lainnya',
            tanggal_mulai: '2024-05-08',
            jam_mulai: '06:00',
            tanggal_selesai: '2024-05-12',
            deskripsi: 'Minggu cerita programmer.',
            status: 'Active',
        },
    ];

    const navItems = [
        { label: 'Dashboard', icon: '??', active: false },
        { label: 'Calendar', icon: '??', active: true },
        { label: 'Events', icon: '??', active: false },
        { label: 'Courses', icon: '??', active: false },
        { label: 'Faculty', icon: '?????', active: false },
        { label: 'Settings', icon: '??', active: false },
    ];

    export default { name: 'AdminKalenderAkademik' };
</script>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, useForm, router } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';

    const props = defineProps({
        kalender: { type: Array, default: () => [] },
        requests: { type: Array, default: () => [] },
        flash: { type: Object, default: () => ({}) },
    });

    const kalenderData = computed(() => (props.kalender?.length ? props.kalender : mockKalender));
    const successMessage = ref(props.flash?.success ?? '');

    const showModal = ref(false);
    const isEditing = ref(false);
    const editingId = ref(null);
    const showDeleteDialog = ref(false);
    const deletingId = ref(null);
    const searchQuery = ref('');

    const form = useForm({
        nama_kegiatan: '',
        tipe_kegiatan: 'semester',
        tanggal_mulai: '',
        jam_mulai: '',
        tanggal_selesai: '',
        deskripsi: '',
        status: 'Active',
    });

    function openCreate() {
        form.reset();
        form.clearErrors();
        isEditing.value = false;
        editingId.value = null;
        form.tipe_kegiatan = 'semester';
        showModal.value = true;
    }

    function openEdit(item) {
        form.nama_kegiatan = item.nama_kegiatan;
        form.tipe_kegiatan = item.tipe_kegiatan;
        form.tanggal_mulai = item.tanggal_mulai;
        form.jam_mulai = item.jam_mulai || '';
        form.tanggal_selesai = item.tanggal_selesai;
        form.deskripsi = item.deskripsi || '';
        form.clearErrors();
        isEditing.value = true;
        editingId.value = item.id;
        showModal.value = true;
    }

    function closeModal() {
        showModal.value = false;
        form.reset();
    }

    function submitForm() {
        // Ensure tanggal_selesai is provided
        if (!form.tanggal_selesai) {
            form.tanggal_selesai = form.tanggal_mulai;
        }

        if (isEditing.value) {
            form.put(route('admin.kalender-akademik.update', editingId.value), {
                onSuccess: () => {
                    closeModal();
                    successMessage.value = 'Kegiatan berhasil diperbarui.';
                },
            });
        } else {
            form.post(route('admin.kalender-akademik.store'), {
                onSuccess: () => {
                    closeModal();
                    successMessage.value = 'Kegiatan berhasil ditambahkan.';
                },
            });
        }
    }

    function confirmDelete(id) {
        deletingId.value = id;
        showDeleteDialog.value = true;
    }

    function doDelete() {
        router.delete(route('admin.kalender-akademik.destroy', deletingId.value), {
            onSuccess: () => {
                showDeleteDialog.value = false;
                deletingId.value = null;
                successMessage.value = 'Kegiatan berhasil dihapus.';
            },
        });
    }

    function formatDate(d) {
        if (!d) return '�';
        return new Date(d).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
        });
    }

    function getStatusStyle(status) {
        return status === 'Pending'
            ? { bg: '#FEF3C7', text: '#92400E' }
            : { bg: '#DCFCE7', text: '#166534' };
    }

    const filteredEvents = computed(() => {
        if (!searchQuery.value.trim()) return kalenderData.value;
        return kalenderData.value.filter((item) =>
            item.nama_kegiatan.toLowerCase().includes(searchQuery.value.toLowerCase()),
        );
    });

    function getTipeLabel(tipe) {
        const labels = {
            semester: 'Semester',
            uts: 'Ujian Tengah Semester',
            libur: 'Libur',
            krs: 'Pendaftaran KRS',
            wisuda: 'Wisuda',
            lainnya: 'Lainnya',
        };
        return labels[tipe] || tipe;
    }
</script>

<template>
    <Head title="Kalender Akademik � Admin" />
    <AuthenticatedLayout>
        <main class="min-h-screen bg-slate-100 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="grid lg:grid-cols-[260px_1fr] gap-6">
                    <aside
                        class="rounded-[28px] bg-slate-950 p-6 text-slate-200 shadow-xl shadow-slate-950/20"
                    >
                        <div class="mb-10 flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-3xl bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-500/20"
                            >
                                <span class="text-2xl">??</span>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">
                                    UniCalendar
                                </p>
                                <h2 class="text-xl font-semibold text-white">Admin</h2>
                            </div>
                        </div>

                        <nav class="space-y-2 text-sm font-medium">
                            <a
                                v-for="item in navItems"
                                :key="item.label"
                                href="#"
                                :class="[
                                    'flex items-center gap-3 rounded-3xl px-4 py-3 transition',
                                    item.active
                                        ? 'bg-slate-800 text-white'
                                        : 'text-slate-400 hover:bg-slate-800 hover:text-white',
                                ]"
                            >
                                <span class="text-lg">{{ item.icon }}</span>
                                {{ item.label }}
                            </a>
                        </nav>
                    </aside>

                    <section class="space-y-6">
                        <div class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5">
                            <div
                                class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
                            >
                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400"
                                    >
                                        Academic Calendar
                                    </p>
                                    <h1 class="mt-3 text-3xl font-semibold text-slate-900">
                                        Academic Calendar
                                    </h1>
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <div class="relative w-full max-w-xs">
                                        <span
                                            class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path d="M21 21l-4.35-4.35" />
                                                <circle cx="10" cy="10" r="6" />
                                            </svg>
                                        </span>
                                        <input
                                            id="search-input"
                                            v-model="searchQuery"
                                            type="text"
                                            placeholder="Search"
                                            class="w-full rounded-full border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 outline-none focus:border-slate-300 focus:ring-2 focus:ring-slate-200"
                                        />
                                    </div>

                                    <button
                                        id="btn-add-event"
                                        class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-2xl font-bold text-white shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-600"
                                        title="Add New Event"
                                        @click="openCreate"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h2 class="text-xl font-semibold text-slate-900">
                                        Daftar Permintaan Jadwal
                                    </h2>
                                    <p class="text-sm text-slate-500">
                                        Permintaan dari Mahasiswa / Dosen
                                    </p>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full border-separate border-spacing-0 text-sm">
                                    <thead class="bg-slate-50 text-slate-500">
                                        <tr>
                                            <th class="px-6 py-4 text-left font-semibold">
                                                Pemohon
                                            </th>
                                            <th class="px-6 py-4 text-left font-semibold">
                                                Kegiatan
                                            </th>
                                            <th class="px-6 py-4 text-left font-semibold">
                                                Tgl & Jam
                                            </th>
                                            <th class="px-6 py-4 text-left font-semibold">
                                                Status
                                            </th>
                                            <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="divide-y divide-slate-200 bg-white text-slate-700"
                                    >
                                        <tr v-if="requests.length === 0">
                                            <td
                                                colspan="5"
                                                class="px-6 py-8 text-center text-slate-500"
                                            >
                                                Tidak ada permintaan jadwal.
                                            </td>
                                        </tr>
                                        <tr
                                            v-for="req in requests"
                                            :key="req.id"
                                            class="hover:bg-slate-50"
                                        >
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-slate-900">
                                                    {{
                                                        req.user ? req.user.name : 'Mahasiswa/Dosen'
                                                    }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-slate-900">
                                                    {{ req.judul }}
                                                </div>
                                                <div class="text-xs text-slate-500">
                                                    {{ req.tipe_request }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ req.tanggal }} <br />
                                                <span class="text-xs text-slate-500">{{
                                                    req.jam
                                                }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="inline-flex rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider"
                                                    :class="{
                                                        'bg-amber-100 text-amber-700':
                                                            req.status === 'pending_dosen' ||
                                                            req.status === 'pending_admin',
                                                        'bg-blue-100 text-blue-700':
                                                            req.status === 'approved_dosen',
                                                        'bg-emerald-100 text-emerald-700':
                                                            req.status === 'approved_admin',
                                                        'bg-rose-100 text-rose-700':
                                                            req.status === 'rejected_dosen' ||
                                                            req.status === 'rejected_admin',
                                                    }"
                                                >
                                                    {{ req.status.replace('_', ' ') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <!-- If it's approved by dosen and waiting for admin -->
                                                <button
                                                    v-if="req.status === 'approved_dosen'"
                                                    :id="'btn-publish-' + req.id"
                                                    class="inline-flex px-4 py-2 items-center justify-center rounded-xl bg-emerald-500 text-white font-semibold transition hover:bg-emerald-600 text-xs"
                                                    @click="
                                                        $inertia.put(
                                                            route(
                                                                'jadwal-request.updateStatus',
                                                                req.id,
                                                            ),
                                                            { status: 'approved_admin' },
                                                        )
                                                    "
                                                >
                                                    Masukan ke Kalender
                                                </button>
                                                <button
                                                    v-else-if="req.status === 'rejected_dosen'"
                                                    class="inline-flex px-4 py-2 items-center justify-center rounded-xl bg-slate-100 text-slate-500 font-semibold transition hover:bg-slate-200 text-xs cursor-not-allowed"
                                                    disabled
                                                >
                                                    Ditolak Dosen
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="rounded-[32px] bg-white shadow-xl shadow-slate-900/5">
                            <div class="overflow-x-auto">
                                <table class="min-w-full border-separate border-spacing-0 text-sm">
                                    <thead class="bg-slate-50 text-slate-500">
                                        <tr>
                                            <th class="px-6 py-4 text-left font-semibold">Name</th>
                                            <th class="px-6 py-4 text-left font-semibold">
                                                Start Date
                                            </th>
                                            <th class="px-6 py-4 text-left font-semibold">
                                                Start Time
                                            </th>
                                            <th class="px-6 py-4 text-left font-semibold">
                                                End Date
                                            </th>
                                            <th class="px-6 py-4 text-left font-semibold">
                                                Status
                                            </th>
                                            <th class="px-6 py-4 text-right font-semibold">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="divide-y divide-slate-200 bg-white text-slate-700"
                                    >
                                        <tr
                                            v-for="item in filteredEvents"
                                            :key="item.id"
                                            class="hover:bg-slate-50"
                                        >
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-slate-900">
                                                    {{ item.nama_kegiatan }}
                                                </div>
                                                <div class="mt-1 text-xs text-slate-500">
                                                    {{ getTipeLabel(item.tipe_kegiatan) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ formatDate(item.tanggal_mulai) }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ item.jam_mulai || '�' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ formatDate(item.tanggal_selesai) }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                                    :style="`background:${getStatusStyle(item.status).bg}; color:${getStatusStyle(item.status).text};`"
                                                >
                                                    {{ item.status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <button
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white transition hover:bg-slate-800"
                                                    @click="openEdit(item)"
                                                >
                                                    <svg
                                                        class="h-4 w-4"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                    >
                                                        <path d="M12 20h9" />
                                                        <path
                                                            d="M16.5 3.5a2.121 2.121 0 113 3L8 18l-4 1 1-4 11.5-11.5z"
                                                        />
                                                    </svg>
                                                </button>
                                                <button
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500 text-white ml-2 transition hover:bg-rose-600"
                                                    @click="confirmDelete(item.id)"
                                                >
                                                    <svg
                                                        class="h-4 w-4"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                    >
                                                        <path d="M3 6h18" />
                                                        <path
                                                            d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"
                                                        />
                                                        <path
                                                            d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"
                                                        />
                                                        <path d="M10 11v6" />
                                                        <path d="M14 11v6" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div
                                class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div class="text-sm text-slate-500">
                                    Total Events: {{ filteredEvents.length }}
                                </div>
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 transition hover:border-slate-300"
                                    >
                                        prev
                                    </button>
                                    <span class="text-sm text-slate-500">1 of 12</span>
                                    <button
                                        class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 transition hover:border-slate-300"
                                    >
                                        next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <transition name="modal">
                <div
                    v-if="showModal"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm"
                >
                    <div class="w-full max-w-2xl rounded-[32px] bg-white p-6 shadow-2xl">
                        <div
                            class="flex items-center justify-between border-b border-slate-200 pb-4"
                        >
                            <div>
                                <h2 class="text-xl font-semibold text-slate-900">
                                    {{ isEditing ? 'Edit Event' : 'Add New Event' }}
                                </h2>
                                <p class="text-sm text-slate-500">
                                    Kelola jadwal kalender akademik institusi.
                                </p>
                            </div>
                            <button
                                class="rounded-full bg-slate-100 p-2 text-slate-600 transition hover:bg-slate-200"
                                @click="closeModal"
                            >
                                ?
                            </button>
                        </div>
                        <form class="mt-6 grid gap-4 sm:grid-cols-2" @submit.prevent="submitForm">
                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-sm font-semibold text-slate-700"
                                    >Nama Kegiatan</label
                                >
                                <input
                                    id="admin-nama-kegiatan"
                                    v-model="form.nama_kegiatan"
                                    type="text"
                                    name="nama_kegiatan"
                                    placeholder="Contoh: Ujian Tengah Semester"
                                    class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700"
                                    >Start Date</label
                                >
                                <input
                                    id="admin-tanggal-mulai"
                                    v-model="form.tanggal_mulai"
                                    type="date"
                                    name="tanggal_mulai"
                                    class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700"
                                    >Start Time</label
                                >
                                <input
                                    id="admin-jam-mulai"
                                    v-model="form.jam_mulai"
                                    type="time"
                                    name="jam_mulai"
                                    class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700"
                                    >End Date</label
                                >
                                <input
                                    id="admin-tanggal-selesai"
                                    v-model="form.tanggal_selesai"
                                    type="date"
                                    name="tanggal_selesai"
                                    class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-sm font-semibold text-slate-700"
                                    >Description</label
                                >
                                <textarea
                                    id="admin-deskripsi"
                                    v-model="form.deskripsi"
                                    name="deskripsi"
                                    rows="4"
                                    class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                />
                            </div>
                            <div
                                class="sm:col-span-2 flex flex-col gap-3 sm:flex-row sm:justify-end"
                            >
                                <button
                                    type="button"
                                    class="rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                                    @click="closeModal"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                                >
                                    {{ isEditing ? 'Save Changes' : 'Create Event' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>

            <transition name="modal">
                <div
                    v-if="showDeleteDialog"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm"
                >
                    <div class="w-full max-w-md rounded-[32px] bg-white p-6 shadow-2xl">
                        <h3 class="text-lg font-semibold text-slate-900">Hapus event?</h3>
                        <p class="mt-2 text-sm text-slate-500">
                            Apakah Anda yakin ingin menghapus kegiatan ini? Aksi ini tidak dapat
                            dibatalkan.
                        </p>
                        <div class="mt-6 flex justify-end gap-3">
                            <button
                                class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                                @click="showDeleteDialog = false"
                            >
                                Batal
                            </button>
                            <button
                                class="rounded-full bg-rose-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-600"
                                @click="doDelete"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </main>
    </AuthenticatedLayout>
</template>

<style scoped>
    .modal-enter-active,
    .modal-leave-active {
        transition:
            opacity 0.25s ease,
            transform 0.25s ease;
    }
    .modal-enter-from,
    .modal-leave-to {
        opacity: 0;
        transform: translateY(12px);
    }
</style>
