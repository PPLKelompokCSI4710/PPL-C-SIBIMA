<template>
    <StudentLayout>
        <Head title="Manajemen Draft Skripsi" />

        <div class="max-w-6xl mx-auto space-y-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">
                        Manajemen Draft Skripsi
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Upload dan kelola dokumen draft skripsi Anda beserta catatan perbaikannya.
                    </p>
                </div>
                <button
                    @click="openUploadModal"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-sm active:scale-95 whitespace-nowrap"
                >
                    <UploadCloudIcon class="w-5 h-5" />
                    <span>Upload Draft Baru</span>
                </button>
            </div>

            <!-- Upload Modal -->
            <Teleport to="body">
                <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div v-if="isUploadModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeUploadModal"></div>
                    
                    <div class="relative bg-white rounded-3xl shadow-xl border border-slate-100 w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center shrink-0">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                <FileTextIcon class="w-5 h-5 text-blue-600" />
                                {{ isEditing ? 'Edit Draft' : 'Upload Draft' }}
                            </h3>
                            <button @click="closeUploadModal" class="p-1 hover:bg-slate-200 rounded-lg text-slate-400 transition-colors">
                                <XIcon class="w-5 h-5" />
                            </button>
                        </div>
                        
                        <div class="p-6 overflow-y-auto">
                            <form @submit.prevent="submitUpload" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Judul Draft</label>
                                    <input 
                                        v-model="uploadForm.judul" 
                                        type="text" 
                                        placeholder="Contoh: Bab 1 Revisi 2" 
                                        required
                                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                    />
                                    <div v-if="uploadForm.errors.judul" class="text-xs text-red-500 mt-1">{{ uploadForm.errors.judul }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bab</label>
                                    <select 
                                        v-model="uploadForm.bab" 
                                        required
                                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all bg-white"
                                    >
                                        <option value="" disabled>Pilih Bab</option>
                                        <option value="Bab 1">Bab 1 - Pendahuluan</option>
                                        <option value="Bab 2">Bab 2 - Tinjauan Pustaka</option>
                                        <option value="Bab 3">Bab 3 - Metodologi</option>
                                        <option value="Bab 4">Bab 4 - Hasil & Pembahasan</option>
                                        <option value="Bab 5">Bab 5 - Kesimpulan</option>
                                        <option value="Full Draft">Full Draft (Semua Bab)</option>
                                    </select>
                                    <div v-if="uploadForm.errors.bab" class="text-xs text-red-500 mt-1">{{ uploadForm.errors.bab }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">File Draft (PDF/Word) <span v-if="isEditing" class="text-slate-400 font-normal text-xs">(Biarkan kosong jika tidak ingin mengubah file)</span></label>
                                    <input 
                                        type="file" 
                                        @input="uploadForm.file = $event.target.files[0]" 
                                        accept=".pdf,.doc,.docx"
                                        :required="!isEditing"
                                        class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    />
                                    <div v-if="uploadForm.errors.file" class="text-xs text-red-500 mt-1">{{ uploadForm.errors.file }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Catatan Awal (Opsional)</label>
                                    <textarea 
                                        v-model="uploadForm.catatan" 
                                        rows="3" 
                                        placeholder="Tambahkan catatan untuk draft ini..." 
                                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                    ></textarea>
                                </div>

                                <div class="pt-4 flex gap-3 justify-end border-t border-slate-100">
                                    <button 
                                        type="button" 
                                        @click="closeUploadModal" 
                                        class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors"
                                    >
                                        Batal
                                    </button>
                                    <button 
                                        type="submit" 
                                        :disabled="uploadForm.processing"
                                        class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors disabled:opacity-50"
                                    >
                                        {{ uploadForm.processing ? 'Menyimpan...' : (isEditing ? 'Simpan Perubahan' : 'Upload Draft') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </Transition>
            </Teleport>

            <!-- Catatan Modal -->
            <Teleport to="body">
                <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div v-if="isCatatanModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeCatatanModal"></div>
                    
                    <div class="relative bg-white rounded-3xl shadow-xl border border-slate-100 w-full max-w-lg overflow-hidden flex flex-col">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                <PenSquareIcon class="w-5 h-5 text-amber-500" />
                                Catatan Pribadi
                            </h3>
                            <button @click="closeCatatanModal" class="p-1 hover:bg-slate-200 rounded-lg text-slate-400 transition-colors">
                                <XIcon class="w-5 h-5" />
                            </button>
                        </div>
                        
                        <div class="p-6">
                            <p class="text-xs text-slate-500 mb-4 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="font-bold text-slate-700">Draft:</span> {{ selectedDraft?.judul }}
                            </p>

                            <form @submit.prevent="submitCatatan" class="space-y-4">
                                <div>
                                    <textarea 
                                        v-model="catatanForm.catatan" 
                                        rows="5" 
                                        placeholder="Tuliskan hal-hal yang perlu diperbaiki, ide, atau pengingat..." 
                                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                    ></textarea>
                                </div>

                                <div class="flex gap-3 justify-end">
                                    <button 
                                        type="button" 
                                        @click="closeCatatanModal" 
                                        class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors"
                                    >
                                        Batal
                                    </button>
                                    <button 
                                        type="submit" 
                                        :disabled="catatanForm.processing"
                                        class="px-5 py-2.5 text-sm font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl transition-colors disabled:opacity-50"
                                    >
                                        {{ catatanForm.processing ? 'Menyimpan...' : 'Simpan Catatan' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </Transition>
            </Teleport>

            <!-- Delete Confirmation Modal -->
            <Teleport to="body">
                <Transition name="modal">
                    <div v-if="showDeleteDialog" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm" @click.self="showDeleteDialog = false">
                        <div class="w-full max-w-sm rounded-[32px] bg-white p-6 shadow-2xl text-center">
                            <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-500 mx-auto flex items-center justify-center mb-4 text-3xl">🗑️</div>
                            <h3 class="text-lg font-bold text-slate-900">Hapus Draft Skripsi?</h3>
                            <p class="mt-2 text-sm text-slate-500 mb-6">Apakah Anda yakin ingin menghapus draft ini? Aksi ini tidak dapat dibatalkan.</p>
                            <div class="flex justify-center gap-3">
                                <button 
                                    class="rounded-full bg-slate-100 px-6 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors" 
                                    @click="showDeleteDialog = false"
                                >
                                    Batal
                                </button>
                                <button 
                                    class="rounded-full bg-rose-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-rose-600 shadow-lg shadow-rose-900/20 transition-colors" 
                                    @click="doDelete"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </Teleport>

            <!-- List of Drafts -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-if="drafts.length === 0" class="col-span-full py-16 flex flex-col items-center justify-center text-center bg-white rounded-3xl border border-slate-200 border-dashed">
                    <FileQuestionIcon class="w-16 h-16 text-slate-300 mb-4 stroke-[1.5]" />
                    <h3 class="text-lg font-bold text-slate-700">Belum ada draft skripsi</h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm">Mulai upload draft bab skripsi Anda untuk menyimpannya dengan aman di sistem.</p>
                </div>

                <div 
                    v-for="draft in drafts" 
                    :key="draft.id"
                    class="bg-white rounded-3xl border border-slate-200 p-6 flex flex-col transition-all hover:shadow-lg hover:border-blue-200 group relative"
                >
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <FileTextIcon class="w-6 h-6" />
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    {{ draft.bab }}
                                </span>
                                <h3 class="font-bold text-slate-800 text-base leading-tight mt-0.5 line-clamp-2" :title="draft.judul">
                                    {{ draft.judul }}
                                </h3>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <button 
                                @click="openEditModal(draft)"
                                class="text-slate-300 hover:text-blue-500 p-1.5 hover:bg-blue-50 rounded-lg transition-colors"
                                title="Edit Draft"
                            >
                                <EditIcon class="w-4 h-4" />
                            </button>
                            <button 
                                @click="confirmDelete(draft.id)"
                                class="text-slate-300 hover:text-red-500 p-1.5 hover:bg-red-50 rounded-lg transition-colors"
                                title="Hapus Draft"
                            >
                                <Trash2Icon class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 space-y-4">
                        <!-- Date Info -->
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <CalendarIcon class="w-4 h-4 text-slate-400" />
                            <span>Diunggah pada {{ formatDate(draft.created_at) }}</span>
                        </div>

                        <!-- Catatan Preview -->
                        <div class="bg-amber-50/50 border border-amber-100 rounded-2xl p-4 cursor-pointer hover:bg-amber-50 transition-colors" @click="openCatatanModal(draft)">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-amber-700 flex items-center gap-1.5">
                                    <MessageSquareIcon class="w-3.5 h-3.5" />
                                    Catatan Pribadi
                                </span>
                                <PenSquareIcon class="w-3.5 h-3.5 text-amber-400" />
                            </div>
                            <p class="text-sm text-slate-600 line-clamp-3 italic" v-if="draft.catatan">
                                "{{ draft.catatan }}"
                            </p>
                            <p class="text-sm text-slate-400 italic" v-else>
                                Belum ada catatan. Klik untuk menambahkan.
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-5 pt-5 border-t border-slate-100 flex items-center justify-between gap-3">
                        <a 
                            :href="`/storage/${draft.file_path}`" 
                            target="_blank"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-semibold text-sm rounded-xl transition-colors border border-slate-200 hover:border-blue-200"
                        >
                            <ExternalLinkIcon class="w-4 h-4" />
                            <span>Buka File</span>
                        </a>
                        <a 
                            :href="`/storage/${draft.file_path}`" 
                            :download="getDownloadFilename(draft)"
                            class="p-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 font-semibold text-sm rounded-xl transition-colors border border-slate-200"
                            title="Download File"
                        >
                            <DownloadIcon class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { 
    UploadCloudIcon, FileTextIcon, FileQuestionIcon, CalendarIcon, 
    Trash2Icon, MessageSquareIcon, PenSquareIcon, XIcon, ExternalLinkIcon, DownloadIcon, EditIcon
} from 'lucide-vue-next';

const props = defineProps({
    drafts: {
        type: Array,
        default: () => []
    }
});

// Modal States
const isUploadModalOpen = ref(false);
const isCatatanModalOpen = ref(false);
const showDeleteDialog = ref(false);
const draftToDelete = ref(null);
const isEditing = ref(false);
const editId = ref(null);
const selectedDraft = ref(null);

// Forms
const uploadForm = useForm({
    judul: '',
    bab: '',
    file: null,
    catatan: ''
});

const catatanForm = useForm({
    catatan: ''
});

// Upload & Edit Handlers
const openUploadModal = () => {
    isEditing.value = false;
    editId.value = null;
    uploadForm.reset();
    uploadForm.clearErrors();
    isUploadModalOpen.value = true;
};

const openEditModal = (draft) => {
    isEditing.value = true;
    editId.value = draft.id;
    uploadForm.judul = draft.judul;
    uploadForm.bab = draft.bab;
    uploadForm.catatan = draft.catatan || '';
    uploadForm.file = null;
    selectedDraft.value = draft;
    uploadForm.clearErrors();
    isUploadModalOpen.value = true;
};

const closeUploadModal = () => {
    isUploadModalOpen.value = false;
    selectedDraft.value = null;
    uploadForm.reset();
    uploadForm.clearErrors();
};

const submitUpload = () => {
    if (isEditing.value) {
        uploadForm.post(route('mahasiswa.draft-skripsi.update', editId.value), {
            preserveScroll: true,
            onSuccess: () => {
                closeUploadModal();
            }
        });
    } else {
        uploadForm.post(route('mahasiswa.draft-skripsi.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeUploadModal();
            }
        });
    }
};

// Catatan Handlers
const openCatatanModal = (draft) => {
    selectedDraft.value = draft;
    catatanForm.catatan = draft.catatan || '';
    isCatatanModalOpen.value = true;
};

const closeCatatanModal = () => {
    isCatatanModalOpen.value = false;
    selectedDraft.value = null;
    catatanForm.reset();
    catatanForm.clearErrors();
};

const submitCatatan = () => {
    if (!selectedDraft.value) return;
    
    catatanForm.put(route('mahasiswa.draft-skripsi.updateCatatan', selectedDraft.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeCatatanModal();
        }
    });
};

// Delete Handlers
const confirmDelete = (id) => {
    draftToDelete.value = id;
    showDeleteDialog.value = true;
};

const doDelete = () => {
    if (!draftToDelete.value) return;
    
    useForm({}).delete(route('mahasiswa.draft-skripsi.destroy', draftToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteDialog.value = false;
            draftToDelete.value = null;
        }
    });
};

// Helpers
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const getDownloadFilename = (draft) => {
    if (!draft || !draft.file_path) return 'Draft_Skripsi.pdf';
    const ext = draft.file_path.split('.').pop() || 'pdf';
    const cleanJudul = (draft.judul || 'Untitled').replace(/[^a-zA-Z0-9]/g, '_').substring(0, 30);
    const cleanBab = (draft.bab || 'Bab').replace(/\s+/g, '_');
    return `Draft_Skripsi_${cleanBab}_${cleanJudul}.${ext}`;
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>
