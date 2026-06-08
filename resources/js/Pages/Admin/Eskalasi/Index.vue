<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';

    defineProps({
        eskalasis: Object,
    });
</script>

<template>
    <Head title="Monitoring Eskalasi Bimbingan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Monitoring Eskalasi Bimbingan
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p class="mb-4 text-sm text-gray-600">
                            Daftar mahasiswa yang tidak merespons reminder progres akademik
                            berturut-turut.
                        </p>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Mahasiswa
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Tanggal Eskalasi
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="eskalasi in eskalasis.data" :key="eskalasi.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ eskalasi.mahasiswa?.nama_lengkap }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ eskalasi.mahasiswa?.nim }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{
                                                new Date(eskalasi.created_at).toLocaleDateString(
                                                    'id-ID',
                                                )
                                            }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"
                                            >
                                                {{ eskalasi.status.toUpperCase() }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="eskalasis.data.length === 0">
                                        <td
                                            colspan="3"
                                            class="px-6 py-4 text-center text-sm text-gray-500"
                                        >
                                            Tidak ada data eskalasi aktif.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination (Simple) -->
                        <div
                            v-if="eskalasis.prev_page_url || eskalasis.next_page_url"
                            class="mt-4 flex justify-between"
                        >
                            <Link
                                v-if="eskalasis.prev_page_url"
                                :href="eskalasis.prev_page_url"
                                class="text-indigo-600 hover:text-indigo-900"
                            >
                                Sebelumnya
                            </Link>
                            <span v-else class="text-gray-400">Sebelumnya</span>

                            <Link
                                v-if="eskalasis.next_page_url"
                                :href="eskalasis.next_page_url"
                                class="text-indigo-600 hover:text-indigo-900"
                            >
                                Selanjutnya
                            </Link>
                            <span v-else class="text-gray-400">Selanjutnya</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
