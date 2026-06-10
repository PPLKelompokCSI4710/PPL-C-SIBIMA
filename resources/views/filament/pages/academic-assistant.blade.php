<x-filament-panels::page>

    {{-- ====================================================================== --}}
    {{-- STAT CARDS ROW --}}
    {{-- ====================================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">

        {{-- Card: Today's Requests --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 to-primary-500 p-6 text-white shadow-lg shadow-primary-500/20">
            <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-white/10 blur-xl"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <x-heroicon-o-bolt class="w-5 h-5 text-white" />
                    </div>
                    <span class="text-sm font-semibold text-primary-100">Permintaan Hari Ini</span>
                </div>
                <p class="text-4xl font-bold tracking-tight">{{ $this->getTodayTotalRequests() }}</p>
                <p class="text-xs text-primary-200 mt-1">dari {{ $this->getActiveUsersToday() }} mahasiswa aktif</p>
            </div>
        </div>

        {{-- Card: Total All-time Requests --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 to-violet-500 p-6 text-white shadow-lg shadow-violet-500/20">
            <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-white/10 blur-xl"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <x-heroicon-o-chart-bar class="w-5 h-5 text-white" />
                    </div>
                    <span class="text-sm font-semibold text-violet-100">Total Semua Waktu</span>
                </div>
                <p class="text-4xl font-bold tracking-tight">{{ number_format($this->getAllTimeRequests()) }}</p>
                <p class="text-xs text-violet-200 mt-1">total permintaan terkonsumsi</p>
            </div>
        </div>

        {{-- Card: Daily Quota Setting --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-500 p-6 text-white shadow-lg shadow-emerald-500/20">
            <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-white/10 blur-xl"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <x-heroicon-o-shield-check class="w-5 h-5 text-white" />
                    </div>
                    <span class="text-sm font-semibold text-emerald-100">Kuota Harian</span>
                </div>
                <p class="text-4xl font-bold tracking-tight">{{ $dailyQuota }}</p>
                <p class="text-xs text-emerald-200 mt-1">permintaan per mahasiswa/hari</p>
            </div>
        </div>
    </div>

    {{-- ====================================================================== --}}
    {{-- MAIN CONTENT: QUOTA CONFIG + TABLE --}}
    {{-- ====================================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ================================================================ --}}
        {{-- QUOTA CONFIGURATION CARD --}}
        {{-- ================================================================ --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

                {{-- Card Header --}}
                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-gray-800/60">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                            <x-heroicon-o-cog-6-tooth class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-800 dark:text-white">Konfigurasi Kuota</h3>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Batas permintaan AI per hari</p>
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-6">
                    <form wire:submit="saveSettings" class="space-y-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Kuota Harian per Mahasiswa
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    wire:model="dailyQuota"
                                    id="daily-quota-input"
                                    min="1"
                                    max="500"
                                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white text-sm px-4 py-3 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 dark:focus:border-emerald-500 transition-all duration-200 placeholder:text-gray-400"
                                />
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">req/hari</span>
                            </div>
                            @error('dailyQuota')
                                <p class="mt-1.5 text-[11px] text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                System Prompt (Instruksi Dasar AI)
                            </label>
                            <div class="relative">
                                <textarea
                                    wire:model="systemPrompt"
                                    id="system-prompt-input"
                                    rows="6"
                                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white text-sm px-4 py-3 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 dark:focus:border-emerald-500 transition-all duration-200 placeholder:text-gray-400 resize-y"
                                ></textarea>
                            </div>
                            @error('systemPrompt')
                                <p class="mt-1.5 text-[11px] text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 p-3.5">
                            <div class="flex gap-2.5">
                                <x-heroicon-o-information-circle class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                                <p class="text-xs text-amber-700 dark:text-amber-300 leading-relaxed">
                                    Setiap mahasiswa akan mendapatkan kuota ini per hari. Kuota reset otomatis setiap tengah malam. System prompt digunakan untuk membatasi AI agar HANYA menjawab pertanyaan seputar akademik dan skripsi.
                                </p>
                            </div>
                        </div>

                        <button
                            type="submit"
                            id="save-quota-btn"
                            class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold transition-all duration-200 hover:shadow-lg hover:shadow-emerald-500/25 hover:scale-[1.02] active:scale-100 flex items-center justify-center gap-2"
                        >
                            <x-heroicon-o-check class="w-4 h-4" />
                            Simpan Konfigurasi
                        </button>
                    </form>
                </div>

                {{-- AI Model Info --}}
                <div class="px-6 pb-5">
                    <div class="rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 p-3.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center shrink-0">
                            <x-heroicon-o-cpu-chip class="w-4 h-4 text-indigo-500" />
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Model AI</p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Powered by Google Gemini</p>
                        </div>
                        <div class="ml-auto flex items-center gap-1.5">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- USAGE TABLE --}}
        {{-- ================================================================ --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-sm overflow-hidden h-full flex flex-col">

                {{-- Table Header --}}
                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-500/20 flex items-center justify-center">
                            <x-heroicon-o-users class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-800 dark:text-white">Rekap Penggunaan per Mahasiswa</h3>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Diurutkan berdasarkan jumlah permintaan terbanyak</p>
                        </div>
                    </div>

                    {{-- Date Filter --}}
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-calendar-days class="w-4 h-4 text-gray-400 shrink-0" />
                        <input
                            type="date"
                            wire:model.live="filterDate"
                            id="usage-date-filter"
                            class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs px-3 py-1.5 focus:ring-2 focus:ring-primary-500/40 focus:border-primary-400 transition-all"
                        />
                    </div>
                </div>

                {{-- Table Body --}}
                <div class="flex-1 overflow-auto">
                    @php $usageData = $this->getUsageTableData(); @endphp

                    @if ($usageData->isEmpty())
                        <div class="flex flex-col items-center justify-center h-64 text-center p-6">
                            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-white/5 flex items-center justify-center mb-4">
                                <x-heroicon-o-inbox class="w-8 h-8 text-gray-300 dark:text-gray-600" />
                            </div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Tidak Ada Data</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Belum ada mahasiswa yang menggunakan AI pada tanggal ini.</p>
                        </div>
                    @else
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-white/10">
                                    <th class="text-left px-6 py-3 text-[11px] font-bold uppercase tracking-wide text-gray-400 dark:text-gray-500">Mahasiswa</th>
                                    <th class="text-left px-6 py-3 text-[11px] font-bold uppercase tracking-wide text-gray-400 dark:text-gray-500">Tanggal</th>
                                    <th class="text-center px-6 py-3 text-[11px] font-bold uppercase tracking-wide text-gray-400 dark:text-gray-500">Permintaan</th>
                                    <th class="text-left px-6 py-3 text-[11px] font-bold uppercase tracking-wide text-gray-400 dark:text-gray-500">Penggunaan Kuota</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                                @foreach ($usageData as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/3 transition-colors group">

                                        {{-- User Info --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-violet-500 flex items-center justify-center text-white text-xs font-bold shrink-0 shadow-sm">
                                                    {{ strtoupper(substr($row['name'], 0, 2)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $row['name'] }}</p>
                                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 truncate">{{ $row['email'] }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Date --}}
                                        <td class="px-6 py-4">
                                            <span class="text-xs text-gray-600 dark:text-gray-300">{{ $row['date'] }}</span>
                                        </td>

                                        {{-- Request count --}}
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center justify-center w-9 h-7 rounded-lg text-xs font-bold
                                                {{ $row['quota_used_pct'] >= 90 ? 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400' : ($row['quota_used_pct'] >= 60 ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400' : 'bg-primary-100 dark:bg-primary-500/20 text-primary-600 dark:text-primary-400') }}">
                                                {{ $row['requests_count'] }}
                                            </span>
                                        </td>

                                        {{-- Quota progress bar --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2.5">
                                                <div class="flex-1 h-1.5 rounded-full bg-gray-100 dark:bg-white/10 overflow-hidden">
                                                    <div
                                                        class="h-full rounded-full transition-all duration-500 {{ $row['quota_used_pct'] >= 90 ? 'bg-red-500' : ($row['quota_used_pct'] >= 60 ? 'bg-amber-500' : 'bg-primary-500') }}"
                                                        style="width: {{ min($row['quota_used_pct'], 100) }}%"
                                                    ></div>
                                                </div>
                                                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 w-8 text-right">{{ $row['quota_used_pct'] }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- Table Footer --}}
                @if ($usageData->isNotEmpty())
                    <div class="px-6 py-3 border-t border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-gray-800/40 flex items-center justify-between">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Menampilkan <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $usageData->count() }}</span> mahasiswa
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Total: <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $usageData->sum('requests_count') }}</span> permintaan
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>

</x-filament-panels::page>
