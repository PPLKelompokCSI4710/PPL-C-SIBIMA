<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/vue3';
    import Modal from '@/Components/Modal.vue';
    import { ref } from 'vue';

    defineProps({
        catatanList: {
            type: Array,
            default: () => [],
        },
        mahasiswaList: {
            type: Array,
            default: () => [],
        },
    });

    const showModal = ref(false);

    const form = useForm({
        mahasiswa_id: '',
        tanggal: '',
        topik: '',
        catatan: '',
    });

    const openModal = () => {
        form.reset();
        form.clearErrors();
        showModal.value = true;
    };

    const closeModal = () => {
        showModal.value = false;
    };

    const submit = () => {
        form.post(route('dosen.catatan-konsultasi.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    };

    const hapusCatatan = (id) => {
        if (confirm('Yakin ingin menghapus catatan ini?')) {
            form.delete(route('dosen.catatan-konsultasi.destroy', id), {
                preserveScroll: true,
            });
        }
    };

    const formatTanggal = (tanggal) => {
        if (!tanggal) return '-';
        return new Date(tanggal).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    };
</script>

<template>
    <Head title="Catatan Konsultasi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-primary-dark">
                        Catatan Hasil Konsultasi
                    </h2>
                    <p class="mt-2 text-sm font-medium text-neutral-medium">
                        Kelola dan dokumentasikan hasil konsultasi bimbingan mahasiswa Anda.
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <button
                        id="btn-buat-catatan"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-dark text-white font-bold shadow-md transition-all duration-200 text-sm"
                        @click="openModal"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                        Buat Catatan Baru
                    </button>
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
                        <svg
                            class="w-6 h-6 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
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

                <!-- Pesan jika belum ada mahasiswa bimbingan -->
                <div
                    v-if="mahasiswaList.length === 0"
                    class="mb-6 rounded-xl bg-amber-50 border border-amber-200 px-6 py-4 flex items-center gap-3"
                >
                    <svg
                        class="w-5 h-5 text-amber-500 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                        />
                    </svg>
                    <p class="text-sm font-semibold text-amber-700">
                        Belum ada mahasiswa bimbingan aktif. Tambahkan mahasiswa bimbingan terlebih
                        dahulu untuk membuat catatan.
                    </p>
                </div>

                <!-- Tabel Daftar Catatan -->
                <div
                    class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl overflow-hidden border border-white/60"
                >
                    <div
                        class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between"
                    >
                        <h3 class="text-lg font-bold text-primary-dark">Daftar Catatan</h3>
                        <span class="text-sm font-semibold text-slate-500"
                            >{{ catatanList.length }} catatan tercatat</span
                        >
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
                                        Mahasiswa
                                    </th>
                                    <th
                                        class="py-3 px-4 text-left text-xs font-extrabold text-slate-500 uppercase tracking-widest"
                                    >
                                        Topik
                                    </th>
                                    <th
                                        class="py-3 px-4 text-left text-xs font-extrabold text-slate-500 uppercase tracking-widest"
                                    >
                                        Catatan
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
                                    v-for="catatan in catatanList"
                                    :key="catatan.id"
                                    class="hover:bg-slate-50/50 transition-colors"
                                >
                                    <td
                                        class="py-4 px-4 whitespace-nowrap text-sm font-semibold text-slate-600"
                                    >
                                        {{ formatTanggal(catatan.tanggal) }}
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="h-8 w-8 rounded-lg bg-secondary text-white flex items-center justify-center font-bold text-xs"
                                            >
                                                {{
                                                    catatan.mahasiswa
                                                        ? catatan.mahasiswa.nama_lengkap.charAt(0)
                                                        : 'M'
                                                }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-sm text-slate-800">
                                                    {{ catatan.mahasiswa?.nama_lengkap ?? '-' }}
                                                </p>
                                                <p class="text-[11px] text-slate-500">
                                                    {{ catatan.mahasiswa?.nim ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 max-w-[200px]">
                                        <span class="font-bold text-sm text-primary-dark">{{
                                            catatan.topik
                                        }}</span>
                                    </td>
                                    <td class="py-4 px-4 max-w-[300px]">
                                        <p class="text-sm text-slate-600 line-clamp-2">
                                            {{ catatan.catatan }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-4 text-right whitespace-nowrap">
                                        <button
                                            class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-3 py-1.5 rounded-lg transition-colors text-xs font-bold"
                                            @click="hapusCatatan(catatan.id)"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="catatanList.length === 0">
                                    <td colspan="5" class="py-16 text-center">
                                        <div
                                            class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 mb-4"
                                        >
                                            <svg
                                                class="h-8 w-8 text-blue-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                />
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-slate-700">
                                            Belum ada catatan
                                        </h3>
                                        <p class="text-sm text-slate-400 mt-1">
                                            Klik "Buat Catatan Baru" untuk menambahkan catatan hasil
                                            konsultasi.
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Form Buat Catatan -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-xl font-bold text-primary-dark mb-1">Buat Catatan Konsultasi</h2>
                <p class="text-sm text-slate-500 mb-5">
                    Isi detail hasil konsultasi bimbingan mahasiswa.
                </p>

                <form class="space-y-4" @submit.prevent="submit">
                    <!-- Pilih Mahasiswa -->
                    <div>
                        <label
                            for="mahasiswa_id"
                            class="block text-sm font-bold text-slate-700 mb-1"
                            >Mahasiswa</label
                        >
                        <select
                            id="mahasiswa_id"
                            v-model="form.mahasiswa_id"
                            class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary shadow-sm text-sm"
                            required
                        >
                            <option value="" disabled>-- Pilih Mahasiswa --</option>
                            <option v-for="mhs in mahasiswaList" :key="mhs.id" :value="mhs.id">
                                {{ mhs.nama_lengkap }} ({{ mhs.nim }})
                            </option>
                        </select>
                        <p v-if="form.errors.mahasiswa_id" class="text-red-500 text-xs mt-1">
                            {{ form.errors.mahasiswa_id }}
                        </p>
                    </div>

                    <!-- Tanggal Konsultasi -->
                    <div>
                        <label for="tanggal" class="block text-sm font-bold text-slate-700 mb-1"
                            >Tanggal Konsultasi</label
                        >
                        <input
                            id="tanggal"
                            v-model="form.tanggal"
                            type="date"
                            class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary shadow-sm text-sm"
                            required
                        />
                        <p v-if="form.errors.tanggal" class="text-red-500 text-xs mt-1">
                            {{ form.errors.tanggal }}
                        </p>
                    </div>

                    <!-- Topik -->
                    <div>
                        <label for="topik" class="block text-sm font-bold text-slate-700 mb-1"
                            >Topik / Judul Catatan</label
                        >
                        <input
                            id="topik"
                            v-model="form.topik"
                            type="text"
                            class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary shadow-sm text-sm"
                            placeholder="Contoh: Revisi BAB 3 - Metodologi Penelitian"
                            required
                        />
                        <p v-if="form.errors.topik" class="text-red-500 text-xs mt-1">
                            {{ form.errors.topik }}
                        </p>
                    </div>

                    <!-- Isi Catatan -->
                    <div>
                        <label for="catatan" class="block text-sm font-bold text-slate-700 mb-1"
                            >Catatan Hasil Konsultasi</label
                        >
                        <textarea
                            id="catatan"
                            v-model="form.catatan"
                            rows="5"
                            class="w-full rounded-xl border-slate-300 focus:border-primary focus:ring-primary shadow-sm text-sm"
                            placeholder="Tuliskan detail hasil konsultasi, progress mahasiswa, dan tindak lanjut yang perlu dilakukan..."
                            required
                        />
                        <p v-if="form.errors.catatan" class="text-red-500 text-xs mt-1">
                            {{ form.errors.catatan }}
                        </p>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end gap-3 pt-2">
                        <button
                            type="button"
                            class="px-5 py-2.5 text-sm font-bold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors"
                            @click="closeModal"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-5 py-2.5 text-sm font-bold text-white bg-primary rounded-xl hover:bg-primary-dark transition-colors disabled:opacity-50 shadow-md"
                        >
                            Simpan Catatan
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
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
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
