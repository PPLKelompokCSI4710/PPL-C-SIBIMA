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

            <!-- AI Monitoring Section (Only visible to Admin) -->
            <div v-if="aiStats" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <SparklesIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800">Monitoring AI Academic Assistant (Hari Ini)</h4>
                            <p class="text-xs text-slate-400">Total request dan penggunaan kuota mahasiswa</p>
                        </div>
                    </div>
                    <button
                        @click="isAiConfigModalOpen = true"
                        class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 px-3.5 py-2 rounded-xl"
                    >
                        <SparklesIcon class="w-4 h-4" />
                        Konfigurasi AI
                    </button>
                </div>

                <!-- AI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-400 font-semibold uppercase">Permintaan Hari Ini</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">{{ aiStats.today_total_requests }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                            <ZapIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-400 font-semibold uppercase">Mahasiswa Aktif Hari Ini</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">{{ aiStats.active_users_today }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                            <UsersIcon class="w-5 h-5" />
                        </div>
                    </div>
                </div>

                <!-- Usage Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px]">
                                <th class="py-2.5 font-semibold">Mahasiswa</th>
                                <th class="py-2.5 text-center font-semibold">Total Request</th>
                                <th class="py-2.5 font-semibold">Penggunaan Kuota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="(row, idx) in aiStats.usage_data" :key="idx" class="hover:bg-slate-50/40 transition-colors">
                                <td class="py-3">
                                    <div class="font-bold text-slate-800">{{ row.name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ row.email }}</div>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2.5 py-1 rounded bg-indigo-50 text-indigo-600 font-bold">
                                        {{ row.requests_count }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-32 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                            <div
                                                class="h-full rounded-full transition-all"
                                                :class="row.quota_used_pct >= 90 ? 'bg-red-500' : (row.quota_used_pct >= 60 ? 'bg-amber-500' : 'bg-emerald-500')"
                                                :style="{ width: Math.min(row.quota_used_pct, 100) + '%' }"
                                            ></div>
                                        </div>
                                        <span class="text-[10px] text-slate-500 font-semibold">{{ row.quota_used_pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="aiStats.usage_data.length === 0">
                                <td colspan="3" class="py-6 text-center text-slate-400 italic">
                                    Belum ada aktivitas penggunaan AI hari ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- AI Config Modal -->
        <div v-if="isAiConfigModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="isAiConfigModalOpen = false"></div>
            
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all flex flex-col max-h-[90vh] border border-slate-100">
                <!-- Header -->
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 shadow-inner">
                            <SparklesIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Konfigurasi AI Assistant</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Atur instruksi sistem dan batas kuota harian mahasiswa</p>
                        </div>
                    </div>
                    <button @click="isAiConfigModalOpen = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition-colors">
                        <XIcon class="w-5 h-5" />
                    </button>
                </div>
                
                <!-- Body -->
                <div class="p-6 overflow-y-auto bg-slate-50/30">
                    <form @submit.prevent="submitAiConfig" class="space-y-6">
                        <!-- Quota Setting -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Batas Kuota Harian (Per Mahasiswa)</label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <ZapIcon class="h-5 w-5 text-slate-400" />
                                </div>
                                <input
                                    type="number"
                                    v-model="aiForm.ai_daily_quota"
                                    min="1"
                                    class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow"
                                    placeholder="Contoh: 20"
                                />
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Jumlah maksimum pesan yang dapat dikirim oleh setiap mahasiswa per hari.</p>
                            <p v-if="aiForm.errors.ai_daily_quota" class="mt-1 text-xs text-red-500">{{ aiForm.errors.ai_daily_quota }}</p>
                        </div>
                        
                        <!-- System Prompt -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Instruksi Sistem (System Prompt)</label>
                            <textarea
                                v-model="aiForm.ai_system_prompt"
                                rows="10"
                                class="block w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono text-sm leading-relaxed focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow resize-y"
                                placeholder="Masukkan instruksi dasar untuk AI..."
                            ></textarea>
                            <p class="mt-3 text-xs text-slate-500">Instruksi ini akan menjadi dasar kepribadian dan batasan AI dalam menjawab pertanyaan mahasiswa. Gunakan bahasa yang jelas dan tegas.</p>
                            <p v-if="aiForm.errors.ai_system_prompt" class="mt-1 text-xs text-red-500">{{ aiForm.errors.ai_system_prompt }}</p>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button
                                type="button"
                                @click="isAiConfigModalOpen = false"
                                class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-colors"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="aiForm.processing"
                                class="flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors disabled:opacity-70 disabled:cursor-not-allowed shadow-md shadow-indigo-200"
                            >
                                <SaveIcon v-if="!aiForm.processing" class="w-4 h-4" />
                                <svg v-else class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ aiForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </StaffLayout>
</template>

<script setup>
    import { ref } from 'vue';
    import StaffLayout from '@/Layouts/StaffLayout.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import {
        UsersIcon,
        ClipboardCheckIcon,
        BookOpenIcon,
        TrendingUpIcon,
        SparklesIcon,
        ChevronRightIcon,
        ZapIcon,
        XIcon,
        SaveIcon
    } from 'lucide-vue-next';

    const props = defineProps({
        stats: { type: Object, default: () => ({}) },
        role: { type: String, default: '' },
        aiStats: { type: Object, default: null },
        aiConfig: { type: Object, default: null },
    });

    const isAiConfigModalOpen = ref(false);

    const aiForm = useForm({
        ai_daily_quota: props.aiConfig?.ai_daily_quota || 20,
        ai_system_prompt: props.aiConfig?.ai_system_prompt || '',
    });

    const submitAiConfig = () => {
        aiForm.post(route('staff.dashboard.ai-config'), {
            preserveScroll: true,
            onSuccess: () => {
                isAiConfigModalOpen.value = false;
            },
        });
    };
</script>
