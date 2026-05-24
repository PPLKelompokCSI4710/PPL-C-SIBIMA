<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { computed } from 'vue';
    import { Head, useForm } from '@inertiajs/vue3';

    defineProps({
        ketersediaan: {
            type: Array,
            default: () => [],
        },
    });

    const form = useForm({
        tanggal: '',
        waktu_mulai: '',
        waktu_selesai: '',
        kuota: 1,
        tipe_bimbingan: 'offline',
    });

    const today = computed(() => {
        const d = new Date();
        return d.toISOString().split('T')[0];
    });

    const submit = () => {
        form.post(route('dosen.ketersediaan.store'), {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    const deleteJadwal = (id) => {
        if (confirm('Yakin ingin menghapus ketersediaan jadwal ini?')) {
            form.delete(route('dosen.ketersediaan.destroy', id), {
                preserveScroll: true,
            });
        }
    };

    const formatDate = (dateString) => {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    };
</script>

<template>
    <Head title="Manajemen Kuota & Jadwal" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-primary-dark">
                        Kelola Ketersediaan Jadwal Bimbingan
                    </h2>
                    <p class="mt-2 text-sm font-medium text-neutral-medium">
                        Atur kuota, tanggal, dan rentang waktu ketersediaan Anda agar dapat dipilih
                        oleh mahasiswa.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-6 relative min-h-[calc(100vh-100px)] overflow-hidden bg-[#F5F7FA]">
            <!-- Ornamen Background -->
            <div
                class="absolute top-[-10%] left-[-5%] w-[500px] h-[500px] rounded-full bg-[#1F4C7A]/30 blur-[90px] pointer-events-none"
            />
            <div
                class="absolute bottom-[-10%] right-[-5%] w-[600px] h-[600px] rounded-full bg-[#6CCBC3]/40 blur-[90px] pointer-events-none"
            />

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">
                <!-- Notifikasi -->
                <transition name="slide-fade">
                    <div
                        v-if="$page.props.flash?.success"
                        class="mb-6 rounded-xl bg-emerald-500 px-6 py-4 text-white shadow-lg flex items-center gap-3"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                        <p class="font-bold tracking-wide">
                            {{ $page.props.flash.success }}
                        </p>
                    </div>
                </transition>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Form Input Ketersediaan -->
                    <div class="md:col-span-1">
                        <div
                            class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl p-6 border border-white/60"
                        >
                            <h3 class="text-lg font-bold text-primary-dark mb-4 border-b pb-2">
                                Buat Jadwal Baru
                            </h3>
                            <form class="space-y-4" @submit.prevent="submit">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1"
                                        >Tanggal</label
                                    >
                                    <input
                                        v-model="form.tanggal"
                                        type="date"
                                        :min="today"
                                        class="w-full rounded-lg border-slate-300 focus:border-primary focus:ring-primary shadow-sm text-sm"
                                        required
                                    />
                                    <p v-if="form.errors.tanggal" class="text-red-500 text-xs mt-1">
                                        {{ form.errors.tanggal }}
                                    </p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-1"
                                            >Waktu Mulai</label
                                        >
                                        <input
                                            v-model="form.waktu_mulai"
                                            type="time"
                                            class="w-full rounded-lg border-slate-300 focus:border-primary focus:ring-primary shadow-sm text-sm"
                                            required
                                        />
                                        <p
                                            v-if="form.errors.waktu_mulai"
                                            class="text-red-500 text-xs mt-1"
                                        >
                                            {{ form.errors.waktu_mulai }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-1"
                                            >Waktu Selesai</label
                                        >
                                        <input
                                            v-model="form.waktu_selesai"
                                            type="time"
                                            class="w-full rounded-lg border-slate-300 focus:border-primary focus:ring-primary shadow-sm text-sm"
                                            required
                                        />
                                        <p
                                            v-if="form.errors.waktu_selesai"
                                            class="text-red-500 text-xs mt-1"
                                        >
                                            {{ form.errors.waktu_selesai }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1"
                                        >Kuota Mahasiswa</label
                                    >
                                    <input
                                        v-model="form.kuota"
                                        type="number"
                                        min="1"
                                        class="w-full rounded-lg border-slate-300 focus:border-primary focus:ring-primary shadow-sm text-sm"
                                        required
                                    />
                                    <p v-if="form.errors.kuota" class="text-red-500 text-xs mt-1">
                                        {{ form.errors.kuota }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1"
                                        >Tipe Bimbingan</label
                                    >
                                    <select
                                        v-model="form.tipe_bimbingan"
                                        class="w-full rounded-lg border-slate-300 focus:border-primary focus:ring-primary shadow-sm text-sm"
                                        required
                                    >
                                        <option value="offline">Offline</option>
                                        <option value="online">Online</option>
                                    </select>
                                    <p
                                        v-if="form.errors.tipe_bimbingan"
                                        class="text-red-500 text-xs mt-1"
                                    >
                                        {{ form.errors.tipe_bimbingan }}
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all duration-200 disabled:opacity-50"
                                    >
                                        Tambahkan Jadwal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- List Ketersediaan -->
                    <div class="md:col-span-2">
                        <div
                            class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl overflow-hidden border border-white/60"
                        >
                            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                                <h3 class="text-lg font-bold text-primary-dark">
                                    Daftar Ketersediaan Jadwal
                                </h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-100">
                                    <thead class="bg-slate-50/80">
                                        <tr>
                                            <th
                                                class="py-3 px-4 text-left text-xs font-extrabold text-slate-500 uppercase tracking-widest"
                                            >
                                                Tanggal
                                            </th>
                                            <th
                                                class="py-3 px-4 text-left text-xs font-extrabold text-slate-500 uppercase tracking-widest"
                                            >
                                                Tipe
                                            </th>
                                            <th
                                                class="py-3 px-4 text-left text-xs font-extrabold text-slate-500 uppercase tracking-widest"
                                            >
                                                Waktu
                                            </th>
                                            <th
                                                class="py-3 px-4 text-center text-xs font-extrabold text-slate-500 uppercase tracking-widest"
                                            >
                                                Kuota
                                            </th>
                                            <th
                                                class="py-3 px-4 text-right text-xs font-extrabold text-slate-500 uppercase tracking-widest"
                                            >
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-transparent">
                                        <tr
                                            v-for="item in ketersediaan"
                                            :key="item.id"
                                            class="hover:bg-slate-50/50 transition-colors"
                                        >
                                            <td class="py-3 px-4 font-bold text-slate-700 text-sm">
                                                {{ formatDate(item.tanggal) }}
                                            </td>
                                            <td class="py-3 px-4">
                                                <span
                                                    :class="
                                                        item.tipe_bimbingan === 'online'
                                                            ? 'bg-purple-50 text-purple-700 ring-purple-700/10'
                                                            : 'bg-amber-50 text-amber-700 ring-amber-700/10'
                                                    "
                                                    class="inline-flex items-center rounded-md px-2 py-1 text-xs font-bold ring-1 ring-inset capitalize"
                                                >
                                                    {{ item.tipe_bimbingan }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span
                                                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-bold text-blue-700 ring-1 ring-inset ring-blue-700/10"
                                                >
                                                    {{ item.waktu_mulai.substring(0, 5) }} -
                                                    {{ item.waktu_selesai.substring(0, 5) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <span
                                                    class="font-extrabold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full text-sm"
                                                >
                                                    {{ item.kuota }} Orang
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                <button
                                                    class="inline-flex items-center gap-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-3 py-1.5 rounded-lg transition-colors font-bold text-xs border border-transparent hover:border-rose-200"
                                                    :disabled="form.processing"
                                                    @click="deleteJadwal(item.id)"
                                                >
                                                    <svg
                                                        class="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                        />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="ketersediaan.length === 0">
                                            <td
                                                colspan="4"
                                                class="py-12 text-center text-slate-500"
                                            >
                                                <svg
                                                    class="mx-auto h-12 w-12 text-slate-300 mb-3"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                    />
                                                </svg>
                                                Belum ada jadwal ketersediaan yang Anda atur.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
    .slide-fade-enter-active {
        transition: all 0.4s ease-out;
    }
    .slide-fade-leave-active {
        transition: all 0.4s cubic-bezier(1, 0.5, 0.8, 1);
    }
    .slide-fade-enter-from,
    .slide-fade-leave-to {
        transform: translateY(-20px) scale(0.98);
        opacity: 0;
    }
</style>
