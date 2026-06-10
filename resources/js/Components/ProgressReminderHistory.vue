<template>
    <div v-if="reminders.length > 0" class="mt-8">
        <!-- Section Header -->
        <button
            class="w-full flex items-center justify-between p-4 bg-white rounded-t-xl border border-slate-200 hover:bg-slate-50 transition-colors"
            :class="{ 'rounded-b-xl': !isExpanded }"
            @click="isExpanded = !isExpanded"
        >
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center">
                    <HistoryIcon class="w-5 h-5 text-amber-600" />
                </div>
                <div class="text-left">
                    <h3 class="text-sm font-bold text-slate-800">
                        Riwayat Reminder Progres
                    </h3>
                    <p class="text-xs text-slate-500">
                        {{ reminders.length }} reminder terakhir
                    </p>
                </div>
            </div>
            <ChevronDownIcon
                class="w-5 h-5 text-slate-400 transition-transform duration-200"
                :class="{ 'rotate-180': isExpanded }"
            />
        </button>

        <!-- Collapsible Content -->
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="max-h-0 opacity-0"
            enter-to-class="max-h-[600px] opacity-100"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="max-h-[600px] opacity-100"
            leave-to-class="max-h-0 opacity-0"
        >
            <div
                v-if="isExpanded"
                class="bg-white border border-t-0 border-slate-200 rounded-b-xl overflow-hidden"
            >
                <div class="divide-y divide-slate-100">
                    <div
                        v-for="reminder in reminders"
                        :key="reminder.id"
                        class="p-4 hover:bg-slate-50/50 transition-colors"
                    >
                        <div class="flex items-start gap-3">
                            <!-- Status dot -->
                            <div class="mt-1.5">
                                <span
                                    class="block w-2.5 h-2.5 rounded-full"
                                    :class="reminder.read_at ? 'bg-slate-300' : 'bg-blue-500 animate-pulse'"
                                ></span>
                            </div>
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ reminder.title }}
                                    </p>
                                    <span class="text-[10px] text-slate-400 whitespace-nowrap">
                                        {{ reminder.created_at }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ reminder.message }}
                                </p>
                                <!-- Progress summary chips -->
                                <div
                                    v-if="reminder.progress_summary"
                                    class="flex flex-wrap gap-1.5 mt-2"
                                >
                                    <span
                                        v-if="reminder.progress_summary.ipk"
                                        class="text-[10px] font-medium bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md"
                                    >
                                        IPK: {{ reminder.progress_summary.ipk }}
                                    </span>
                                    <span
                                        v-if="reminder.progress_summary.sks_lulus != null"
                                        class="text-[10px] font-medium bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-md"
                                    >
                                        SKS: {{ reminder.progress_summary.sks_lulus }}/{{ reminder.progress_summary.sks_total }}
                                    </span>
                                    <span
                                        v-if="reminder.progress_summary.semester"
                                        class="text-[10px] font-medium bg-purple-50 text-purple-700 px-2 py-0.5 rounded-md"
                                    >
                                        Sem. {{ reminder.progress_summary.semester }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import { HistoryIcon, ChevronDownIcon } from 'lucide-vue-next';

    defineProps({
        reminders: {
            type: Array,
            default: () => [],
        },
    });

    const isExpanded = ref(false);
</script>
