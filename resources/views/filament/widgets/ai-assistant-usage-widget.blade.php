<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-cpu-chip class="w-5 h-5 text-indigo-500" />
                <span>Monitoring AI Academic Assistant (Hari Ini)</span>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-xl border border-gray-100 dark:border-white/10 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase">Total Permintaan Hari Ini</p>
                    <p class="text-2xl font-bold mt-1 text-primary-600 dark:text-primary-400">{{ $this->getTodayTotalRequests() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-500/20 flex items-center justify-center">
                    <x-heroicon-o-bolt class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-xl border border-gray-100 dark:border-white/10 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase">Mahasiswa Aktif Hari Ini</p>
                    <p class="text-2xl font-bold mt-1 text-indigo-600 dark:text-indigo-400">{{ $this->getActiveUsersToday() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                    <x-heroicon-o-users class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-white/10 text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="py-2.5 font-semibold">Mahasiswa</th>
                        <th class="py-2.5 text-center font-semibold">Total Request</th>
                        <th class="py-2.5 font-semibold">Penggunaan Kuota</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                    @forelse ($this->getUsageData() as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/3 transition-colors">
                            <td class="py-3 font-medium text-gray-800 dark:text-white">
                                <div class="font-semibold">{{ $row['name'] }}</div>
                                <div class="text-[10px] text-gray-400 font-normal">{{ $row['email'] }}</div>
                            </td>
                            <td class="py-3 text-center">
                                <span class="px-2.5 py-1 rounded bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold">
                                    {{ $row['requests_count'] }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-24 sm:w-32 h-1.5 rounded-full bg-gray-100 dark:bg-white/10 overflow-hidden">
                                        <div
                                            class="h-full rounded-full transition-all {{ $row['quota_used_pct'] >= 90 ? 'bg-red-500' : ($row['quota_used_pct'] >= 60 ? 'bg-amber-500' : 'bg-primary-500') }}"
                                            style="width: {{ min($row['quota_used_pct'], 100) }}%"
                                        ></div>
                                    </div>
                                    <span class="text-[10px] text-gray-500 font-semibold">{{ $row['quota_used_pct'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-400 italic">
                                Belum ada aktivitas penggunaan AI hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3.5 text-right">
            <a href="{{ route('filament.admin.pages.academic-assistant') }}" class="text-[11px] font-semibold text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 transition-colors flex items-center justify-end gap-1">
                Lihat Selengkapnya & Konfigurasi Kuota
                <x-heroicon-o-chevron-right class="w-3.5 h-3.5" />
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
