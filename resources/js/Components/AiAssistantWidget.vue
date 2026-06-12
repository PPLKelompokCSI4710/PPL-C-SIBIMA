<script setup>
    import { ref, onMounted, nextTick, computed } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import {
        XIcon,
        BotIcon,
        SendIcon,
        Loader2Icon,
        SparklesIcon,
        Trash2Icon,
        HistoryIcon,
        PlusIcon,
        ChevronLeftIcon,
    } from 'lucide-vue-next';
    import axios from 'axios';

    const page = usePage();
    const isAuthenticated = computed(() => !!page.props.auth?.user);

    const storageKey = computed(() => {
        const userId = page.props.auth?.user?.id || 'guest';
        return `sibima_ai_chat_history_${userId}`;
    });

    const isOpen = ref(false);
    const isHovered = ref(false);
    const message = ref('');
    const messages = ref([]);
    const isLoading = ref(false);
    const chatContainer = ref(null);
    const inputRef = ref(null);

    const showConfirmModal = ref(false);
    const showSessionsPanel = ref(false);
    const quota = ref(20);
    const maxQuota = ref(20);

    // DB Session refs
    const sessionsList = ref([]);
    const activeSessionId = ref(null);

    onMounted(async () => {
        if (isAuthenticated.value) {
            await fetchSessions();
        } else {
            // Load local storage history if available (for guests)
            const saved = localStorage.getItem(storageKey.value);
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    messages.value = data.messages || [];
                    if (data.quota !== undefined) quota.value = data.quota;
                    if (data.maxQuota !== undefined) maxQuota.value = data.maxQuota;
                } catch {
                    // ignore
                }
            }

            if (messages.value.length === 0) {
                messages.value.push({
                    role: 'model',
                    content:
                        'Halo! 👋 Saya SIBIMA AI Assistant. Ada yang bisa saya bantu terkait skripsi, metodologi penelitian, atau info kampus?',
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                });
            }
        }
    });

    const toggleChat = async () => {
        isOpen.value = !isOpen.value;
        if (isOpen.value) {
            if (isAuthenticated.value) {
                await fetchSessions();
            }
            await nextTick();
            inputRef.value?.focus();
            scrollToBottom();
        }
    };

    const scrollToBottom = () => {
        if (chatContainer.value) {
            chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
        }
    };

    const saveHistory = () => {
        localStorage.setItem(
            storageKey.value,
            JSON.stringify({
                messages: messages.value,
                quota: quota.value,
                maxQuota: maxQuota.value,
            }),
        );
    };

    const confirmClearHistory = () => {
        showConfirmModal.value = true;
    };

    const clearHistory = () => {
        messages.value = [
            {
                role: 'model',
                content: 'Riwayat percakapan telah dihapus. Ada yang bisa saya bantu lagi?',
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            },
        ];
        saveHistory();
        showConfirmModal.value = false;
    };

    // DB Session Functions
    const fetchSessions = async () => {
        try {
            const response = await axios.get('/api/ai-chat/sessions');
            sessionsList.value = response.data.sessions;
            quota.value = response.data.quota;
            maxQuota.value = response.data.max_quota;

            if (sessionsList.value.length > 0) {
                if (!activeSessionId.value) {
                    await selectSession(sessionsList.value[0].id);
                }
            } else {
                await createNewSession();
            }
        } catch (error) {
            console.error('Gagal mengambil sesi chat:', error);
        }
    };

    const selectSession = async (id) => {
        activeSessionId.value = id;
        showSessionsPanel.value = false;
        isLoading.value = true;
        messages.value = [];
        try {
            const response = await axios.get(`/api/ai-chat/sessions/${id}/messages`);
            const dbMessages = response.data;
            if (dbMessages.length > 0) {
                messages.value = dbMessages.map((m) => ({
                    role: m.role,
                    content: m.content,
                    time: new Date(m.created_at).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit',
                    }),
                }));
            } else {
                messages.value = [
                    {
                        role: 'model',
                        content:
                            'Halo! 👋 Saya SIBIMA AI Assistant. Ada yang bisa saya bantu terkait skripsi, metodologi penelitian, atau info kampus?',
                        time: new Date().toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit',
                        }),
                    },
                ];
            }
            await nextTick();
            scrollToBottom();
        } catch (error) {
            console.error('Gagal memuat pesan sesi:', error);
        } finally {
            isLoading.value = false;
        }
    };

    const createNewSession = async () => {
        isLoading.value = true;
        try {
            const response = await axios.post('/api/ai-chat/sessions');
            const newSession = response.data;
            sessionsList.value.unshift(newSession);
            activeSessionId.value = newSession.id;
            showSessionsPanel.value = false;
            messages.value = [
                {
                    role: 'model',
                    content:
                        'Halo! 👋 Saya SIBIMA AI Assistant. Sesi baru telah dimulai. Ada yang bisa saya bantu terkait skripsi, metodologi penelitian, atau info kampus?',
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                },
            ];
            await nextTick();
            scrollToBottom();
        } catch (error) {
            console.error('Gagal membuat sesi baru:', error);
        } finally {
            isLoading.value = false;
        }
    };

    const deleteSession = async (id) => {
        try {
            await axios.delete(`/api/ai-chat/sessions/${id}`);
            sessionsList.value = sessionsList.value.filter((s) => s.id !== id);

            if (activeSessionId.value === id) {
                activeSessionId.value = null;
                if (sessionsList.value.length > 0) {
                    await selectSession(sessionsList.value[0].id);
                } else {
                    await createNewSession();
                }
            }
        } catch (error) {
            console.error('Gagal menghapus sesi:', error);
        }
    };

    const handleDeleteSession = (id) => {
        if (confirm('Hapus sesi percakapan ini?')) {
            deleteSession(id);
        }
    };

    const formatSessionDate = (dateStr) => {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    };

    const sendMessage = async () => {
        const text = message.value.trim();
        if (!text || isLoading.value || quota.value <= 0) return;

        // Add user message
        messages.value.push({
            role: 'user',
            content: text,
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        });

        message.value = '';
        isLoading.value = true;

        if (!isAuthenticated.value) {
            saveHistory();
        }

        await nextTick();
        scrollToBottom();

        try {
            if (isAuthenticated.value) {
                const response = await axios.post(
                    `/api/ai-chat/sessions/${activeSessionId.value}/messages`,
                    {
                        content: text,
                    },
                );

                if (response.data.quota !== undefined) {
                    quota.value = response.data.quota;
                    maxQuota.value = response.data.max_quota;
                }

                if (response.data.success) {
                    messages.value.push({
                        role: 'model',
                        content: response.data.reply,
                        time: new Date().toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit',
                        }),
                    });

                    // Update session title
                    const idx = sessionsList.value.findIndex((s) => s.id === activeSessionId.value);
                    if (idx !== -1 && response.data.session_title) {
                        sessionsList.value[idx].title = response.data.session_title;
                    }
                } else {
                    throw new Error(response.data.message || 'Unknown error');
                }
            } else {
                // Send to backend (guest endpoint)
                const historyForApi = messages.value.map((m) => ({
                    role: m.role,
                    content: m.content,
                }));
                const response = await axios.post('/api/ai-chat', { history: historyForApi });

                if (response.data.quota !== undefined) {
                    quota.value = response.data.quota;
                    maxQuota.value = response.data.max_quota;
                }

                if (response.data.success) {
                    messages.value.push({
                        role: 'model',
                        content: response.data.reply,
                        time: new Date().toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit',
                        }),
                    });
                } else {
                    throw new Error(response.data.message || 'Unknown error');
                }
            }
        } catch (error) {
            if (error.response?.data?.session_not_found) {
                activeSessionId.value = null;
                await fetchSessions();
                return;
            }

            const errorMsg =
                error.response?.data?.message ||
                '⚠️ Maaf, terjadi kesalahan saat menghubungi AI (mungkin jaringan bermasalah).';

            if (error.response?.data?.quota !== undefined) {
                quota.value = error.response.data.quota;
                maxQuota.value = error.response.data.max_quota;
            }

            messages.value.push({
                role: 'model',
                content: errorMsg,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                isError: true,
            });
        } finally {
            isLoading.value = false;
            if (!isAuthenticated.value) {
                saveHistory();
            }
            await nextTick();
            scrollToBottom();
            inputRef.value?.focus();
        }
    };

    const useSuggestion = (text) => {
        if (quota.value > 0) {
            message.value = text;
            sendMessage();
        }
    };
</script>

<template>
    <div class="fixed bottom-6 right-6 z-[9999] font-sans">
        <!-- Floating Action Button -->
        <button
            class="relative flex items-center justify-center w-16 h-16 rounded-full shadow-2xl transition-all duration-300 overflow-hidden group hover:scale-110 active:scale-95"
            :class="isOpen ? 'bg-slate-800' : 'bg-transparent'"
            @click="toggleChat"
            @mouseenter="isHovered = true"
            @mouseleave="isHovered = false"
        >
            <div
                v-if="!isOpen"
                class="absolute inset-0 rounded-full animate-spin-slow"
                style="
                    background: conic-gradient(
                        from 0deg,
                        #1f4c7a,
                        #2fa7a0,
                        #6ccbc3,
                        #f39c12,
                        #1f4c7a
                    );
                    padding: 2px;
                "
            >
                <div
                    class="w-full h-full bg-slate-900 rounded-full flex items-center justify-center relative overflow-hidden"
                >
                    <div
                        class="absolute inset-0 bg-gradient-to-tr from-brand-primary/30 to-brand-secondary/30 blur-md animate-pulse"
                    />
                    <SparklesIcon class="w-7 h-7 text-white relative z-10 drop-shadow-md" />
                </div>
            </div>

            <XIcon v-if="isOpen" class="w-6 h-6 text-white" />

            <div
                v-if="!isOpen"
                class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full"
            />
            <div
                v-if="!isOpen"
                class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full animate-ping"
            />
        </button>

        <!-- Tooltip -->
        <div
            v-if="!isOpen"
            class="absolute right-[4.5rem] top-1/2 -translate-y-1/2 bg-slate-800 text-white text-xs font-semibold px-3 py-1.5 rounded-lg whitespace-nowrap shadow-xl transition-all duration-300"
            :class="
                isHovered
                    ? 'opacity-100 translate-x-0'
                    : 'opacity-0 translate-x-2 pointer-events-none'
            "
        >
            Tanya AI Assistant!
            <div
                class="absolute top-1/2 -right-1 -translate-y-1/2 w-2 h-2 bg-slate-800 rotate-45"
            />
        </div>

        <!-- Chat Window -->
        <transition
            enter-active-class="transition duration-300 ease-out origin-bottom-right"
            enter-from-class="opacity-0 scale-50 translate-y-10"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in origin-bottom-right"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-50 translate-y-10"
        >
            <div
                v-if="isOpen"
                class="absolute bottom-20 right-0 w-[350px] sm:w-[400px] max-h-[600px] h-[85vh] bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.15)] border border-slate-100 flex flex-col overflow-hidden"
            >
                <!-- Header -->
                <div class="bg-gradient-to-r from-brand-primary to-brand-primary-dark p-4 text-white shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-white/20 backdrop-blur flex items-center justify-center"
                            >
                                <BotIcon class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h3 class="font-bold text-sm leading-tight flex items-center gap-1">
                                    SIBIMA AI <SparklesIcon class="w-3.5 h-3.5 text-yellow-300" />
                                </h3>
                                <p
                                    class="text-[10px] text-blue-100 font-medium flex items-center gap-1.5 mt-0.5"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-400" /> Online
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <!-- Toggle history panel for authenticated users -->
                            <button
                                v-if="isAuthenticated && !showSessionsPanel"
                                title="Riwayat Percakapan"
                                class="p-1.5 rounded-lg hover:bg-white/20 transition-colors text-white/80 hover:text-white"
                                @click="showSessionsPanel = true"
                            >
                                <HistoryIcon class="w-4 h-4" />
                            </button>
                            <!-- Back button if in sessions panel -->
                            <button
                                v-if="isAuthenticated && showSessionsPanel"
                                title="Kembali Ke Chat"
                                class="p-1.5 rounded-lg hover:bg-white/20 transition-colors text-white/80 hover:text-white"
                                @click="showSessionsPanel = false"
                            >
                                <ChevronLeftIcon class="w-4 h-4" />
                            </button>
                            <!-- Clear history button for guests -->
                            <button
                                v-if="!isAuthenticated"
                                title="Hapus Riwayat"
                                class="p-1.5 rounded-lg hover:bg-white/20 transition-colors text-white/80 hover:text-white"
                                @click="confirmClearHistory"
                            >
                                <Trash2Icon class="w-4 h-4" />
                            </button>
                            <button
                                class="p-1.5 rounded-lg hover:bg-white/20 transition-colors text-white/80 hover:text-white"
                                @click="toggleChat"
                            >
                                <XIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sessions List Panel (Overlay) -->
                <div v-if="showSessionsPanel" class="flex-1 overflow-y-auto bg-slate-50 flex flex-col">
                    <div class="p-4 border-b border-slate-100 bg-white flex items-center justify-between shrink-0 shadow-sm">
                        <span class="text-xs font-bold text-slate-800">Riwayat Sesi Percakapan</span>
                        <button
                            class="flex items-center gap-1 text-[10px] font-bold text-brand-primary bg-brand-primary/5 hover:bg-brand-primary/10 px-2.5 py-1.5 rounded-lg transition-colors"
                            @click="createNewSession"
                        >
                            <PlusIcon class="w-3 h-3" />
                            Sesi Baru
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-3 space-y-2">
                        <div
                            v-for="session in sessionsList"
                            :key="session.id"
                            class="group relative flex items-center justify-between p-3 rounded-xl border transition-all cursor-pointer"
                            :class="activeSessionId === session.id
                                ? 'bg-brand-primary/5 border-brand-primary/20 text-brand-primary-dark font-semibold shadow-sm'
                                : 'bg-white border-slate-100 hover:border-slate-200 text-slate-700 hover:bg-slate-50/40'"
                            @click="selectSession(session.id)"
                        >
                            <div class="min-w-0 pr-8 flex-1">
                                <p class="text-xs truncate font-medium" :class="activeSessionId === session.id ? 'text-brand-primary-dark' : 'text-slate-700'">
                                    {{ session.title }}
                                </p>
                                <span class="text-[9px] text-slate-400 font-normal mt-1 block">
                                    Aktif: {{ formatSessionDate(session.updated_at) }}
                                </span>
                            </div>

                            <!-- Delete button (visible on hover or active session) -->
                            <button
                                class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all opacity-0 group-hover:opacity-100 duration-150"
                                @click.stop="handleDeleteSession(session.id)"
                            >
                                <Trash2Icon class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                </div>

                <template v-else>
                    <!-- Messages Area -->
                    <div
                        ref="chatContainer"
                        class="flex-1 overflow-y-auto p-4 bg-slate-50 space-y-4 scroll-smooth"
                    >
                        <div
                            v-for="(msg, i) in messages"
                            :key="i"
                            class="flex flex-col"
                            :class="msg.role === 'user' ? 'items-end' : 'items-start'"
                        >
                            <div
                                class="max-w-[85%] flex gap-2"
                                :class="msg.role === 'user' ? 'flex-row-reverse' : ''"
                            >
                                <!-- Avatar -->
                                <div
                                    class="shrink-0 w-7 h-7 rounded-full flex items-center justify-center mt-1 shadow-sm"
                                    :class="
                                        msg.role === 'user'
                                            ? 'bg-brand-primary/10 text-brand-primary'
                                            : 'bg-gradient-to-br from-brand-primary to-brand-primary-dark text-white'
                                    "
                                >
                                    <span v-if="msg.role === 'user'" class="text-[10px] font-bold"
                                        >You</span
                                    >
                                    <BotIcon v-else class="w-4 h-4" />
                                </div>

                                <!-- Bubble -->
                                <div
                                    class="flex flex-col"
                                    :class="msg.role === 'user' ? 'items-end' : 'items-start'"
                                >
                                    <div
                                        class="px-3.5 py-2.5 rounded-2xl text-sm shadow-sm"
                                        :class="[
                                            msg.role === 'user'
                                                ? 'bg-brand-primary text-white rounded-tr-sm'
                                                : msg.isError
                                                  ? 'bg-red-50 text-red-700 border border-red-100 rounded-tl-sm'
                                                  : 'bg-white text-slate-700 border border-slate-100 rounded-tl-sm',
                                        ]"
                                        style="white-space: pre-wrap"
                                    >
                                        {{ msg.content }}
                                    </div>
                                    <span class="text-[9px] text-slate-400 mt-1 font-medium mx-1">{{
                                        msg.time
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Typing Indicator -->
                        <div v-if="isLoading" class="flex items-start gap-2">
                            <div
                                class="shrink-0 w-7 h-7 rounded-full bg-gradient-to-br from-brand-primary to-brand-primary-dark text-white flex items-center justify-center mt-1 shadow-sm"
                            >
                                <BotIcon class="w-4 h-4" />
                            </div>
                            <div
                                class="bg-white border border-slate-100 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm"
                            >
                                <div class="flex gap-1.5 items-center h-4">
                                    <div
                                        class="w-1.5 h-1.5 bg-brand-primary rounded-full animate-bounce"
                                        style="animation-delay: 0ms"
                                    />
                                    <div
                                        class="w-1.5 h-1.5 bg-brand-primary rounded-full animate-bounce"
                                        style="animation-delay: 150ms"
                                    />
                                    <div
                                        class="w-1.5 h-1.5 bg-brand-primary rounded-full animate-bounce"
                                        style="animation-delay: 300ms"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Suggestions (Only show if few messages) -->
                    <div
                        v-if="messages.length <= 2 && !isLoading"
                        class="px-3 py-2 bg-slate-50 border-t border-slate-100 flex flex-wrap gap-1.5 shrink-0"
                    >
                        <p class="w-full text-[10px] text-slate-400 font-medium mb-0.5 ml-1">
                            Saran pertanyaan:
                        </p>
                        <button
                            class="text-[10px] bg-white border border-slate-200 text-slate-600 px-2.5 py-1.5 rounded-full hover:bg-brand-primary/5 hover:text-brand-primary hover:border-brand-primary/20 transition-colors"
                            @click="useSuggestion('Apa itu metode penelitian kualitatif?')"
                        >
                            Metode kualitatif
                        </button>
                        <button
                            class="text-[10px] bg-white border border-slate-200 text-slate-600 px-2.5 py-1.5 rounded-full hover:bg-brand-primary/5 hover:text-brand-primary hover:border-brand-primary/20 transition-colors"
                            @click="
                                useSuggestion(
                                    'Bagaimana cara membuat latar belakang skripsi yang baik?',
                                )
                            "
                        >
                            Cara buat Latar Belakang
                        </button>
                    </div>

                    <!-- Input Area -->
                    <div class="p-3 bg-white border-t border-slate-100 shrink-0 relative">
                        <!-- Quota exhausted alert -->
                        <div v-if="quota <= 0" class="mb-2.5 p-2 bg-red-50 rounded-lg border border-red-100 flex gap-2 shrink-0">
                            <BotIcon class="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
                            <p class="text-[10px] text-red-700 leading-normal font-medium">
                                Kuota bertanya harian Anda telah habis. Input dinonaktifkan. Kuota akan di-reset otomatis pada tengah malam (00:00).
                            </p>
                        </div>

                        <form class="relative flex items-end gap-2" @submit.prevent="sendMessage">
                            <div
                                class="flex-1 bg-slate-50 rounded-xl border border-slate-200 focus-within:border-brand-primary focus-within:ring-2 focus-within:ring-brand-primary/20 transition-all flex items-center min-h-[44px] overflow-hidden"
                                :class="{ 'opacity-50 bg-slate-100': quota <= 0 }"
                            >
                                <textarea
                                    ref="inputRef"
                                    v-model="message"
                                    rows="1"
                                    :placeholder="
                                        quota <= 0 ? 'Kuota bertanya habis...' : 'Ketik pesan...'
                                    "
                                    class="w-full bg-transparent border-none text-sm text-slate-700 placeholder:text-slate-400 resize-none py-2.5 px-3 focus:ring-0 max-h-24"
                                    :disabled="isLoading || quota <= 0"
                                    @keydown.enter.prevent="!$event.shiftKey && sendMessage()"
                                    @input="
                                        $event.target.style.height = 'auto';
                                        $event.target.style.height =
                                            Math.min($event.target.scrollHeight, 96) + 'px';
                                    "
                                />
                            </div>
                            <button
                                type="submit"
                                :disabled="isLoading || !message.trim() || quota <= 0"
                                class="shrink-0 w-11 h-11 bg-brand-primary hover:bg-brand-primary-dark text-white rounded-xl flex items-center justify-center transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
                            >
                                <Loader2Icon v-if="isLoading" class="w-5 h-5 animate-spin" />
                                <SendIcon v-else class="w-5 h-5 ml-0.5" />
                            </button>
                        </form>

                        <div class="flex items-center justify-between mt-2 px-1">
                            <div
                                class="flex items-center gap-1 text-[10px] font-medium"
                                :class="quota > 5 ? 'text-slate-500' : 'text-red-500 animate-pulse'"
                            >
                                <BotIcon class="w-3 h-3" />
                                <span>Kuota: {{ quota }}/{{ maxQuota }}</span>
                            </div>
                            <span class="text-[9px] text-slate-400">Powered by Gemini AI</span>
                        </div>
                    </div>
                </template>

                <!-- Custom Confirmation Modal for Guests -->
                <transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 translate-y-4"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-4"
                >
                    <div
                        v-if="showConfirmModal"
                        class="absolute inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center p-6"
                    >
                        <div
                            class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-5 text-center w-full max-w-[280px]"
                        >
                            <div
                                class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3"
                            >
                                <Trash2Icon class="w-6 h-6" />
                            </div>
                            <h4 class="font-bold text-slate-800 text-sm mb-1">Hapus Riwayat?</h4>
                            <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                                Semua percakapan dengan SIBIMA AI akan dihapus permanen dari
                                perangkat ini.
                            </p>

                            <div class="flex gap-2">
                                <button
                                    class="flex-1 py-2 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors"
                                    @click="showConfirmModal = false"
                                >
                                    Batal
                                </button>
                                <button
                                    class="flex-1 py-2 text-xs font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm shadow-red-200"
                                    @click="clearHistory"
                                >
                                    Ya, Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </div>
</template>
