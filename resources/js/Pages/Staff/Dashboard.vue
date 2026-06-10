<template>
    <StaffLayout>
        <Head title="Staff Dashboard" />

        <div class="space-y-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600"
                        >
                            <UsersIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Total Mahasiswa</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">
                        {{ stats?.total_mahasiswa || 0 }}
                    </p>
                </div>



                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600"
                        >
                            <BookOpenIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Total Mata Kuliah</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">
                        {{ stats?.total_courses || 0 }}
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600"
                        >
                            <TrendingUpIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Rata-rata IPK</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">
                        {{ stats?.avg_ipk ? parseFloat(stats.avg_ipk).toFixed(2) : '0.00' }}
                    </p>
                </div>
            </div>

            <!-- Welcome Section -->
            <div
                class="bg-indigo-600 rounded-3xl p-8 text-white relative overflow-hidden shadow-lg shadow-indigo-200"
            >
                <div class="relative z-10">
                    <h3 class="text-2xl font-bold mb-2">Selamat Datang di Portal SIBIMA</h3>
                    <p class="text-indigo-100 max-w-lg">
                        Anda masuk sebagai
                        <span
                            class="font-bold uppercase text-white bg-white/20 px-2 py-0.5 rounded ml-1"
                            >{{ role }}</span
                        >. Gunakan menu di sebelah kiri untuk mengelola data akademik mahasiswa,
                        memantau progres studi, atau mengelola data kursus.
                    </p>
                </div>
                <!-- Abstract patterns -->
                <div
                    class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"
                />
                <div
                    class="absolute right-20 -bottom-20 w-40 h-40 bg-indigo-400/20 rounded-full blur-2xl"
                />
            </div>
        </div>
    </StaffLayout>
</template>

<script setup>
    import StaffLayout from '@/Layouts/StaffLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';
    import { UsersIcon, ClipboardCheckIcon, BookOpenIcon, TrendingUpIcon } from 'lucide-vue-next';

    defineProps({
        stats: { type: Object, default: () => ({}) },
        role: { type: String, default: '' },
    });
</script>
