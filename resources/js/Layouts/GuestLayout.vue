<script setup>
    import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="min-h-screen flex flex-col items-center justify-center bg-slate-50 relative overflow-hidden font-sans antialiased"
    >
        <!-- Background Elements -->
        <div
            class="absolute -top-24 -left-24 w-96 h-96 bg-blue-100 rounded-full blur-3xl opacity-50"
        />
        <div
            class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-100 rounded-full blur-3xl opacity-50"
        />
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-slate-100/30 rounded-full blur-3xl opacity-30"
        />

        <div class="z-10 w-full max-w-md px-6">
            <div class="flex flex-col items-center mb-8">
                <Link
                    href="/"
                    class="group flex items-center bg-white p-4 rounded-2xl shadow-xl shadow-blue-900/5 transition-all hover:scale-105 active:scale-95"
                >
                    <img src="/images/logo-sibima.svg" alt="SIBIMA Logo" class="h-10 w-auto" />
                </Link>
            </div>

            <div
                class="bg-white/80 backdrop-blur-xl rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-white p-8 md:p-10"
            >
                <slot />
            </div>

            <div class="mt-8 text-center">
                <p class="text-sm text-slate-400 font-medium tracking-wide">
                    &copy; 2026 SIBIMA &bull; Telkom University
                </p>
            </div>
        </div>
    </div>
</template>
