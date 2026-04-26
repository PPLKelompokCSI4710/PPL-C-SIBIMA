<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';

    const weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const weekDates = ['13 May', '14 May', '15 May', '16 May', '17 May', '18 May', '19 May'];
    const timeSlots = [
        '8:00 AM',
        '9:00 AM',
        '10:00 AM',
        '11:00 AM',
        '12:00 PM',
        '1:00 PM',
        '2:00 PM',
        '3:00 PM',
        '4:00 PM',
        '5:00 PM',
        '6:00 PM',
    ];

    const scheduleEvents = [
        {
            id: 1,
            title: 'EN0202 Essay Deadline',
            subtitle: 'Due 13 May 08:00',
            day: 1,
            row: 3,
            span: 2,
            color: '#1F4C7A',
        },
        {
            id: 2,
            title: 'CS101 Lecture',
            subtitle: 'Wed 15 May • Hall A',
            day: 3,
            row: 4,
            span: 2,
            color: '#2FA7A0',
        },
        {
            id: 3,
            title: 'ENG202 Lecture',
            subtitle: 'Thu 16 May • Room 3B',
            day: 4,
            row: 5,
            span: 2,
            color: '#E74C3C',
        },
        {
            id: 4,
            title: 'Faculty Meeting',
            subtitle: 'Fri 17 May • 11:00',
            day: 5,
            row: 8,
            span: 2,
            color: '#F39C12',
        },
        {
            id: 5,
            title: 'Research Review',
            subtitle: 'Sat 18 May • 14:00',
            day: 6,
            row: 6,
            span: 2,
            color: '#8E44AD',
        },
        {
            id: 6,
            title: 'CS101 Midterm',
            subtitle: 'Sun 19 May • 17:00',
            day: 7,
            row: 10,
            span: 2,
            color: '#1F4C7A',
        },
    ];

    const agendaItems = [
        { title: 'CS101 Grading', date: 'May 17', time: '17:00', color: '#1F4C7A' },
        { title: 'Faculty Meeting', date: 'May 16', time: '11:00', color: '#F39C12' },
        { title: 'ENG202 Lecture', date: 'May 15', time: '14:00', color: '#2FA7A0' },
    ];

    const monthDays = Array.from({ length: 31 }, (_, i) => ({
        day: i + 1,
        type: i + 1 === 15 ? 'blue' : i + 1 === 16 ? 'amber' : i + 1 === 17 ? 'red' : 'none',
    }));

    function tileStyles(type) {
        if (type === 'blue') return 'bg-blue-500';
        if (type === 'amber') return 'bg-amber-400';
        if (type === 'red') return 'bg-red-500';
        return 'bg-transparent';
    }
</script>

<template>
    <Head title="Kalender Akademik – Dosen" />
    <AuthenticatedLayout>
        <main class="min-h-screen bg-slate-100 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5 mb-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400"
                            >
                                Academic Calendar
                            </p>
                            <h1 class="mt-3 text-3xl font-semibold text-slate-900">
                                Academic Calendar
                            </h1>
                            <p class="mt-2 text-sm text-slate-500">Welcome, Prof. Eleanor Vance</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <input
                                type="text"
                                placeholder="Search"
                                class="hidden md:block rounded-full border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none focus:border-slate-300 focus:ring-2 focus:ring-slate-200"
                            />
                            <button
                                class="rounded-full border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 transition hover:bg-slate-100"
                            >
                                Month
                            </button>
                            <button
                                class="rounded-full border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 transition hover:bg-slate-100"
                            >
                                Week
                            </button>
                            <button
                                class="rounded-full border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 transition hover:bg-slate-100"
                            >
                                Day
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid lg:grid-cols-[320px_1fr] gap-6">
                    <aside
                        class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5 border border-slate-200"
                    >
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Agenda</h2>
                                <p class="text-sm text-slate-500">Upcoming tasks for this week</p>
                            </div>
                            <span
                                class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"
                                >3 items</span
                            >
                        </div>
                        <div class="space-y-4">
                            <div
                                v-for="item in agendaItems"
                                :key="item.title"
                                class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                            >
                                <div class="flex items-center gap-3">
                                    <span
                                        class="h-3.5 w-3.5 rounded-full"
                                        :style="`background:${item.color}`"
                                    />
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ item.title }}
                                    </p>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">
                                    {{ item.date }} · {{ item.time }}
                                </p>
                            </div>
                        </div>
                    </aside>

                    <section class="space-y-6">
                        <div
                            class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5 border border-slate-200"
                        >
                            <div
                                class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
                            >
                                <div>
                                    <h3 class="text-xl font-semibold text-slate-900">
                                        Weekly Schedule View
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-500">May 13 - 19, 2024</p>
                                </div>
                                <div
                                    class="flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700"
                                >
                                    <span class="font-semibold">Week</span>
                                </div>
                            </div>

                            <div class="mt-6 overflow-x-auto">
                                <div
                                    class="grid min-w-[900px] grid-cols-[80px_repeat(7,minmax(0,1fr))] text-slate-500 text-xs"
                                >
                                    <div class="border-b border-slate-200 bg-slate-50 p-3" />
                                    <template v-for="(label, index) in weekDates" :key="label">
                                        <div
                                            class="border-b border-slate-200 bg-slate-50 p-3 font-semibold text-slate-700"
                                        >
                                            <div>{{ weekDays[index] }}</div>
                                            <div class="mt-1 text-sm text-slate-900">
                                                {{ label }}
                                            </div>
                                        </div>
                                    </template>

                                    <template v-for="slot in timeSlots" :key="slot">
                                        <div
                                            class="border-r border-b border-slate-200 bg-slate-50 p-3 text-right pr-4"
                                        >
                                            {{ slot }}
                                        </div>
                                        <template
                                            v-for="dayIndex in 7"
                                            :key="`${slot}-${dayIndex}`"
                                        >
                                            <div class="border-b border-slate-200 bg-white" />
                                        </template>
                                    </template>
                                </div>

                                <div class="relative -mt-[calc(11*70px)] min-w-[900px]">
                                    <div
                                        class="grid grid-cols-[80px_repeat(7,minmax(0,1fr))] gap-0"
                                    >
                                        <div class="h-[calc(11*70px)]" />
                                        <template v-for="dayIndex in 7" :key="dayIndex">
                                            <div class="h-[calc(11*70px)]" />
                                        </template>
                                    </div>
                                    <div
                                        class="absolute inset-0 grid grid-cols-[80px_repeat(7,minmax(0,1fr))] gap-0"
                                    >
                                        <div />
                                        <template v-for="dayIndex in 7" :key="dayIndex">
                                            <div class="border-l border-slate-200" />
                                        </template>
                                        <template v-for="rowIndex in 11" :key="rowIndex">
                                            <div />
                                            <template
                                                v-for="dayIndex in 7"
                                                :key="`line-${rowIndex}-${dayIndex}`"
                                            >
                                                <div class="border-t border-slate-200" />
                                            </template>
                                        </template>
                                    </div>
                                    <template v-for="event in scheduleEvents" :key="event.id">
                                        <div
                                            class="absolute rounded-3xl p-4 text-white shadow-lg"
                                            :style="{
                                                left: `calc(80px + ((100% - 80px) / 7) * ${event.day - 1} + 8px)`,
                                                top: `calc(${(event.row - 1) * 70}px + 8px)`,
                                                width: `calc((100% - 80px) / 7 - 16px)`,
                                                minHeight: `${event.span * 70 - 16}px`,
                                                background: event.color,
                                            }"
                                        >
                                            <p class="font-bold text-sm">
                                                {{ event.title }}
                                            </p>
                                            <p class="mt-2 text-xs opacity-90">
                                                {{ event.subtitle }}
                                            </p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div
                            class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5 border border-slate-200"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">
                                        Monthly Preview
                                    </h3>
                                    <p class="text-sm text-slate-500">May 2024</p>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <button
                                        class="rounded-full border border-slate-200 bg-slate-50 px-3 py-2"
                                    >
                                        ◀
                                    </button>
                                    <button
                                        class="rounded-full border border-slate-200 bg-slate-50 px-3 py-2"
                                    >
                                        ▶
                                    </button>
                                </div>
                            </div>

                            <div
                                class="mt-6 grid grid-cols-7 gap-2 text-center text-xs text-slate-500"
                            >
                                <div>Mo</div>
                                <div>Tu</div>
                                <div>We</div>
                                <div>Th</div>
                                <div>Fr</div>
                                <div>Sa</div>
                                <div>Su</div>
                            </div>
                            <div class="mt-3 grid grid-cols-7 gap-2 text-sm text-slate-700">
                                <template v-for="day in monthDays" :key="day.day">
                                    <div
                                        class="rounded-3xl border border-slate-200 p-3 relative bg-slate-50"
                                    >
                                        <div class="font-semibold">
                                            {{ day.day }}
                                        </div>
                                        <span
                                            v-if="day.type !== 'none'"
                                            :class="[
                                                'absolute bottom-3 right-3 h-2.5 w-2.5 rounded-full',
                                                tileStyles(day.type),
                                            ]"
                                        />
                                    </div>
                                </template>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </AuthenticatedLayout>
</template>

<style scoped></style>
