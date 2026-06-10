<script setup>
import StaffLayout from '@/Layouts/StaffLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    jadwals: { type: Array, default: () => [] }
});

// Form state
const form = useForm({
    tanggal: '',
    waktu_mulai: '',
    waktu_selesai: '',
    kuota: 1,
    tipe: 'offline', // default
});

// Disable past dates
const today = computed(() => {
    const d = new Date();
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
});

const isSubmitting = ref(false);

const submit = () => {
    if (isSubmitting.value) return;
    isSubmitting.value = true;
    
    form.post(route('dosen.ketersediaan-jadwal.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            isSubmitting.value = false;
        },
        onError: () => {
            isSubmitting.value = false;
        }
    });
};

const deleteJadwal = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
        router.delete(route('dosen.ketersediaan-jadwal.destroy', id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
};

const formatTime = (timeStr) => timeStr ? timeStr.substring(0, 5) : '-';
</script>

<template>
    <Head title="Ketersediaan Jadwal Bimbingan" />
    <StaffLayout>
        <div class="py-6">
            <div class="mx-auto max-w-6xl">
                
                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                            <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        </div>
                        <p class="text-sm font-semibold text-emerald-800">{{ $page.props.flash.success }}</p>
                    </div>
                </div>
                <div v-if="$page.props.flash?.error" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100">
                            <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                        </div>
                        <p class="text-sm font-semibold text-red-800">{{ $page.props.flash.error }}</p>
                    </div>
                </div>

                <div class="mb-8 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/25">
                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Ketersediaan Jadwal Bimbingan</h1>
                        <p class="text-sm text-slate-500">Atur jadwal kapan Anda bersedia menerima bimbingan mahasiswa</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Left: Form Input -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sticky top-6">
                            <h2 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">Buat Jadwal Baru</h2>
                            
                            <form @submit.prevent="submit" class="space-y-4">
                                <!-- Tanggal -->
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                                    <input 
                                        type="date" 
                                        v-model="form.tanggal" 
                                        :min="today"
                                        required
                                        class="block w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    />
                                    <p v-if="form.errors.tanggal" class="mt-1 text-xs text-red-500">{{ form.errors.tanggal }}</p>
                                </div>

                                <!-- Waktu Mulai & Selesai -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Waktu Mulai</label>
                                        <input 
                                            type="time" 
                                            v-model="form.waktu_mulai" 
                                            required
                                            class="block w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                        />
                                        <p v-if="form.errors.waktu_mulai" class="mt-1 text-xs text-red-500">{{ form.errors.waktu_mulai }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Waktu Selesai</label>
                                        <input 
                                            type="time" 
                                            v-model="form.waktu_selesai" 
                                            required
                                            class="block w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                        />
                                        <p v-if="form.errors.waktu_selesai" class="mt-1 text-xs text-red-500">{{ form.errors.waktu_selesai }}</p>
                                    </div>
                                </div>

                                <!-- Kuota & Tipe -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kuota Mahasiswa</label>
                                        <input 
                                            type="number" 
                                            v-model="form.kuota" 
                                            min="1"
                                            required
                                            class="block w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                        />
                                        <p v-if="form.errors.kuota" class="mt-1 text-xs text-red-500">{{ form.errors.kuota }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tipe Bimbingan</label>
                                        <select 
                                            v-model="form.tipe"
                                            required
                                            class="block w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="offline">Offline</option>
                                            <option value="online">Online</option>
                                        </select>
                                        <p v-if="form.errors.tipe" class="mt-1 text-xs text-red-500">{{ form.errors.tipe }}</p>
                                    </div>
                                </div>

                                <button 
                                    type="submit" 
                                    :disabled="isSubmitting"
                                    class="w-full mt-4 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-indigo-500/25 hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                >
                                    <svg v-if="isSubmitting" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Simpan Jadwal
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Daftar Jadwal -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                                <h2 class="text-lg font-bold text-slate-800">Daftar Jadwal Anda</h2>
                            </div>
                            
                            <div v-if="jadwals.length > 0" class="divide-y divide-slate-100">
                                <div v-for="jadwal in jadwals" :key="jadwal.id" class="p-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800">{{ formatDate(jadwal.tanggal) }}</p>
                                            <p class="text-sm font-semibold text-slate-600 mt-0.5">
                                                {{ formatTime(jadwal.waktu_mulai) }} - {{ formatTime(jadwal.waktu_selesai) }} WIB
                                            </p>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                    Kuota: {{ jadwal.kuota }} Mahasiswa
                                                </span>
                                                <span 
                                                    class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset uppercase"
                                                    :class="jadwal.tipe === 'online' ? 'bg-blue-50 text-blue-700 ring-blue-600/20' : 'bg-slate-100 text-slate-700 ring-slate-600/20'"
                                                >
                                                    {{ jadwal.tipe }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <button 
                                        @click="deleteJadwal(jadwal.id)"
                                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus Jadwal"
                                    >
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Empty State -->
                            <div v-else class="p-12 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 mb-4">
                                    <svg class="h-8 w-8 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Jadwal</h3>
                                <p class="text-sm text-slate-500">Anda belum membuat ketersediaan jadwal bimbingan. Silakan isi form di samping.</p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </StaffLayout>
</template>
