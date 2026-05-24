<x-filament-panels::page>
    <div class="academic-assistant-container" x-data="{ autoScroll() { $nextTick(() => { const el = document.getElementById('chat-messages'); if(el) el.scrollTop = el.scrollHeight; }); } }" x-init="autoScroll()">

        {{-- ============================================================ --}}
        {{-- MAIN TWO-COLUMN LAYOUT --}}
        {{-- ============================================================ --}}
        <div class="flex gap-4 h-[calc(100vh-12rem)]">

            {{-- ======================================================== --}}
            {{-- SIDEBAR — Session History --}}
            {{-- ======================================================== --}}
            <div class="w-80 shrink-0 flex flex-col rounded-2xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

                {{-- Sidebar Header --}}
                <div class="p-4 border-b border-gray-200 dark:border-white/10 bg-gradient-to-r from-primary-600 to-primary-500">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <x-heroicon-o-chat-bubble-left-right class="w-4 h-4 text-white" />
                            </div>
                            <h3 class="font-bold text-white text-sm">Riwayat Sesi</h3>
                        </div>
                        <button wire:click="startNewSession"
                            class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white text-xs font-semibold transition-all duration-200 hover:scale-105">
                            <x-heroicon-o-plus class="w-3.5 h-3.5" />
                            Sesi Baru
                        </button>
                    </div>
                </div>

                {{-- Session List --}}
                <div class="flex-1 overflow-y-auto p-2 space-y-1">
                    @forelse ($this->getSessions() as $session)
                        <div wire:key="session-{{ $session->id }}"
                            class="group relative flex items-center gap-2 px-3 py-2.5 rounded-xl cursor-pointer transition-all duration-200
                            {{ $currentSessionId === $session->id
                                ? 'bg-primary-50 dark:bg-primary-500/10 border border-primary-200 dark:border-primary-500/30 shadow-sm'
                                : 'hover:bg-gray-50 dark:hover:bg-white/5 border border-transparent' }}"
                            wire:click="selectSession({{ $session->id }})">

                            <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center
                                {{ $currentSessionId === $session->id
                                    ? 'bg-primary-100 dark:bg-primary-500/20 text-primary-600 dark:text-primary-400'
                                    : 'bg-gray-100 dark:bg-white/10 text-gray-400 dark:text-gray-500' }}">
                                <x-heroicon-o-chat-bubble-bottom-center-text class="w-4 h-4" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate {{ $currentSessionId === $session->id ? 'text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $session->title }}
                                </p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                                    {{ $session->updated_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Delete Button --}}
                            <button wire:click.stop="deleteSession({{ $session->id }})"
                                class="opacity-0 group-hover:opacity-100 p-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 text-gray-400 hover:text-red-500 transition-all duration-200">
                                <x-heroicon-o-trash class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-40 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-white/5 flex items-center justify-center mb-3">
                                <x-heroicon-o-inbox class="w-6 h-6 text-gray-300 dark:text-gray-600" />
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Belum ada sesi chat</p>
                            <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-1">Mulai sesi baru untuk bertanya</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- MAIN CHAT AREA --}}
            {{-- ======================================================== --}}
            <div class="flex-1 flex flex-col rounded-2xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

                {{-- Chat Header --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10 bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-info-500 flex items-center justify-center shadow-lg shadow-primary-500/20">
                            <x-heroicon-o-academic-cap class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-800 dark:text-white text-sm">SIBIMA Academic Assistant</h2>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Powered by Gemini AI — Asisten Skripsi & Penelitian</p>
                        </div>
                        <div class="ml-auto flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">Online</span>
                        </div>
                    </div>
                </div>

                {{-- Messages Container --}}
                <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-4" x-ref="chatMessages"
                    wire:poll.visible="$refresh"
                    x-effect="autoScroll()">

                    @if (!$currentSessionId)
                        {{-- Welcome Screen --}}
                        <div class="flex flex-col items-center justify-center h-full text-center">
                            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-primary-500 to-info-500 flex items-center justify-center mb-6 shadow-2xl shadow-primary-500/30 animate-bounce" style="animation-duration: 3s;">
                                <x-heroicon-o-academic-cap class="w-10 h-10 text-white" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Selamat Datang! 👋</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mb-8">
                                Saya adalah asisten AI yang siap membantu Anda dalam proses penyusunan skripsi, metodologi penelitian, dan penulisan akademik.
                            </p>

                            {{-- Suggestion Cards --}}
                            <div class="grid grid-cols-2 gap-3 max-w-lg">
                                <button wire:click="useSuggestion('Bantu saya menentukan judul skripsi yang baik')"
                                    class="group p-4 rounded-xl border border-gray-200 dark:border-white/10 hover:border-primary-300 dark:hover:border-primary-500/30 bg-white dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-500/5 transition-all duration-200 text-left hover:shadow-md hover:-translate-y-0.5">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center mb-2 group-hover:bg-blue-100 dark:group-hover:bg-blue-500/20 transition-colors">
                                        <x-heroicon-o-light-bulb class="w-4 h-4 text-blue-500" />
                                    </div>
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Tentukan Judul Skripsi</p>
                                    <p class="text-[10px] text-gray-400 mt-1">Panduan memilih topik yang tepat</p>
                                </button>

                                <button wire:click="useSuggestion('Bagaimana cara merumuskan rumusan masalah dan hipotesis penelitian?')"
                                    class="group p-4 rounded-xl border border-gray-200 dark:border-white/10 hover:border-primary-300 dark:hover:border-primary-500/30 bg-white dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-500/5 transition-all duration-200 text-left hover:shadow-md hover:-translate-y-0.5">
                                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center mb-2 group-hover:bg-amber-100 dark:group-hover:bg-amber-500/20 transition-colors">
                                        <x-heroicon-o-question-mark-circle class="w-4 h-4 text-amber-500" />
                                    </div>
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Rumusan Masalah</p>
                                    <p class="text-[10px] text-gray-400 mt-1">Buat hipotesis yang kuat</p>
                                </button>

                                <button wire:click="useSuggestion('Jelaskan perbedaan metodologi penelitian kualitatif dan kuantitatif')"
                                    class="group p-4 rounded-xl border border-gray-200 dark:border-white/10 hover:border-primary-300 dark:hover:border-primary-500/30 bg-white dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-500/5 transition-all duration-200 text-left hover:shadow-md hover:-translate-y-0.5">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center mb-2 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-500/20 transition-colors">
                                        <x-heroicon-o-book-open class="w-4 h-4 text-emerald-500" />
                                    </div>
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Metodologi Penelitian</p>
                                    <p class="text-[10px] text-gray-400 mt-1">Kualitatif vs Kuantitatif</p>
                                </button>

                                <button wire:click="useSuggestion('Bagaimana teknik penulisan tinjauan pustaka yang efektif?')"
                                    class="group p-4 rounded-xl border border-gray-200 dark:border-white/10 hover:border-primary-300 dark:hover:border-primary-500/30 bg-white dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-500/5 transition-all duration-200 text-left hover:shadow-md hover:-translate-y-0.5">
                                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center mb-2 group-hover:bg-purple-100 dark:group-hover:bg-purple-500/20 transition-colors">
                                        <x-heroicon-o-document-text class="w-4 h-4 text-purple-500" />
                                    </div>
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Tinjauan Pustaka</p>
                                    <p class="text-[10px] text-gray-400 mt-1">Teknik penulisan literatur</p>
                                </button>
                            </div>
                        </div>

                    @else
                        @forelse ($this->getMessages() as $message)
                            <div wire:key="msg-{{ $message->id }}"
                                class="flex {{ $message->role === 'user' ? 'justify-end' : 'justify-start' }} animate-in slide-in-from-bottom-2">

                                @if ($message->role === 'model')
                                    {{-- AI Avatar --}}
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-500 to-info-500 flex items-center justify-center shrink-0 mr-3 mt-1 shadow-md shadow-primary-500/20">
                                        <x-heroicon-o-academic-cap class="w-4 h-4 text-white" />
                                    </div>
                                @endif

                                <div class="max-w-[75%] {{ $message->role === 'user'
                                    ? 'bg-gradient-to-br from-primary-600 to-primary-700 text-white rounded-2xl rounded-tr-md shadow-lg shadow-primary-500/20'
                                    : 'bg-gray-50 dark:bg-white/5 text-gray-800 dark:text-gray-200 rounded-2xl rounded-tl-md border border-gray-200 dark:border-white/10' }} px-5 py-3.5">

                                    @if ($message->role === 'model')
                                        <div class="prose prose-sm dark:prose-invert max-w-none prose-p:my-1 prose-ul:my-1 prose-ol:my-1 prose-li:my-0.5 prose-headings:my-2">
                                            {!! \Illuminate\Support\Str::markdown($message->content) !!}
                                        </div>
                                    @else
                                        <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $message->content }}</p>
                                    @endif

                                    <p class="text-[9px] mt-2 {{ $message->role === 'user' ? 'text-primary-200' : 'text-gray-400 dark:text-gray-500' }}">
                                        {{ $message->created_at->format('H:i') }}
                                    </p>
                                </div>

                                @if ($message->role === 'user')
                                    {{-- User Avatar --}}
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-gray-600 to-gray-700 flex items-center justify-center shrink-0 ml-3 mt-1 shadow-md text-white text-xs font-bold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center h-full text-center">
                                <p class="text-sm text-gray-400 dark:text-gray-500">Ketik pesan untuk memulai percakapan...</p>
                            </div>
                        @endforelse

                        {{-- Loading Indicator --}}
                        @if ($isLoading)
                            <div class="flex justify-start">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-500 to-info-500 flex items-center justify-center shrink-0 mr-3 mt-1 shadow-md shadow-primary-500/20">
                                    <x-heroicon-o-academic-cap class="w-4 h-4 text-white" />
                                </div>
                                <div class="bg-gray-50 dark:bg-white/5 rounded-2xl rounded-tl-md border border-gray-200 dark:border-white/10 px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex gap-1">
                                            <div class="w-2 h-2 rounded-full bg-primary-400 animate-bounce" style="animation-delay: 0ms;"></div>
                                            <div class="w-2 h-2 rounded-full bg-primary-400 animate-bounce" style="animation-delay: 150ms;"></div>
                                            <div class="w-2 h-2 rounded-full bg-primary-400 animate-bounce" style="animation-delay: 300ms;"></div>
                                        </div>
                                        <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">Sedang menyusun jawaban...</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Error Message --}}
                @if ($errorMessage)
                    <div class="mx-6 mb-2 p-3 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 text-xs flex items-center gap-2">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4 shrink-0" />
                        <span>{{ $errorMessage }}</span>
                    </div>
                @endif

                {{-- Message Input --}}
                <div class="p-4 border-t border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-gray-800/50">
                    <form wire:submit="sendMessage" class="flex items-end gap-3">
                        <div class="flex-1 relative">
                            <textarea
                                wire:model="messageText"
                                id="chat-input"
                                rows="1"
                                placeholder="Tanyakan seputar skripsi, metodologi, atau penulisan akademik..."
                                class="w-full resize-none rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-200 px-4 py-3 pr-4 focus:ring-2 focus:ring-primary-500/50 focus:border-primary-300 dark:focus:border-primary-500/50 transition-all duration-200 placeholder:text-gray-400 dark:placeholder:text-gray-600"
                                style="max-height: 120px;"
                                x-data
                                x-on:input="$el.style.height = '42px'; $el.style.height = Math.min($el.scrollHeight, 120) + 'px';"
                                x-on:keydown.enter.prevent="if (!$event.shiftKey) { $wire.sendMessage(); $el.style.height = '42px'; }"
                                @disabled($isLoading)
                            ></textarea>
                        </div>
                        <button type="submit"
                            class="shrink-0 w-11 h-11 rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-500 hover:to-primary-400 text-white flex items-center justify-center transition-all duration-200 hover:shadow-lg hover:shadow-primary-500/30 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                            @disabled($isLoading || empty(trim($messageText)))>
                            @if ($isLoading)
                                <x-heroicon-o-arrow-path class="w-4 h-4 animate-spin" />
                            @else
                                <x-heroicon-o-paper-airplane class="w-4 h-4" />
                            @endif
                        </button>
                    </form>
                    <p class="text-[10px] text-gray-400 dark:text-gray-600 mt-2 text-center">
                        Tekan <kbd class="px-1 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-mono text-[9px]">Enter</kbd> untuk kirim,
                        <kbd class="px-1 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-mono text-[9px]">Shift+Enter</kbd> untuk baris baru
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
