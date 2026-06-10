<template>
    <StudentLayout>
        <Head title="Progress Studi - SIBIMA" />

        <div class="max-w-7xl mx-auto space-y-8 pb-10">
            <!-- Header Section with Student Brief Info -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-8 md:p-10 text-white shadow-xl">
                <!-- Abstract glowing backgrounds -->
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl opacity-70 pointer-events-none" />
                <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-violet-600/10 rounded-full blur-3xl opacity-50 pointer-events-none" />
                
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <div class="flex items-center gap-2 text-indigo-300 text-xs font-semibold uppercase tracking-wider mb-2">
                            <SparklesIcon class="w-4 h-4 animate-pulse" />
                            Academic Journey
                        </div>
                        <h1 class="text-3xl font-black tracking-tight">Progress Studi & Target</h1>
                        <p class="text-indigo-200/80 mt-1 text-sm md:text-base">
                            Pantau pencapaian akademik Anda dan ukur langkah menuju kelulusan impian.
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap gap-2.5 bg-white/5 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-sm">
                        <div class="px-3 py-1.5 bg-indigo-500/20 rounded-lg text-indigo-200 font-semibold">
                            Sem {{ currentSemester }}
                        </div>
                        <div class="px-3 py-1.5 bg-white/5 rounded-lg text-slate-300 font-medium">
                            NIM: {{ auth.user?.mahasiswa?.nim || '-' }}
                        </div>
                        <div class="px-3 py-1.5 bg-white/5 rounded-lg text-slate-300 font-medium max-w-xs truncate">
                            {{ auth.user?.mahasiswa?.program_studi || 'Program Studi' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Visualizations (Left Col) -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Cards Grid: IPK and SKS -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Card A: GPA / IPK Tracker -->
                        <div class="bg-white/95 border border-slate-200/60 rounded-3xl p-8 shadow-md hover:shadow-lg transition-all duration-300 relative overflow-hidden flex flex-col justify-between group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/50 rounded-full blur-2xl opacity-70 pointer-events-none group-hover:scale-125 transition-all duration-500" />
                            
                            <div>
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Indeks Prestasi Kumulatif (IPK)</h3>
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner">
                                        <AwardIcon class="w-5 h-5" />
                                    </div>
                                </div>

                                <!-- Circular Gauge Container -->
                                <div class="flex justify-center items-center py-4 relative">
                                    <div class="relative flex items-center justify-center">
                                        <!-- SVG Circular Gauges -->
                                        <svg class="w-36 h-36 transform -rotate-90">
                                            <!-- Base Track -->
                                            <circle cx="72" cy="72" r="62" stroke="#f1f5f9" stroke-width="8" fill="transparent" />
                                            
                                            <!-- Target IPK (Outer Dotted Line) -->
                                            <circle 
                                                v-if="auth.progress?.target_ipk"
                                                cx="72" cy="72" r="62" 
                                                stroke="#f43f5e" stroke-width="2" 
                                                stroke-dasharray="4 3" fill="transparent"
                                                :stroke-dashoffset="targetIpkOffset"
                                                :stroke-dasharray="2 * Math.PI * 62"
                                                class="transition-all duration-1000 opacity-60" 
                                            />
                                            
                                            <!-- Current IPK Inner track background -->
                                            <circle cx="72" cy="72" r="50" stroke="#f8fafc" stroke-width="10" fill="transparent" />
                                            
                                            <!-- Current IPK (Solid Circle) -->
                                            <circle 
                                                cx="72" cy="72" r="50" 
                                                stroke="url(#ipkGrad)" stroke-width="10" 
                                                stroke-linecap="round" fill="transparent"
                                                :stroke-dasharray="ipkCircumference"
                                                :stroke-dashoffset="ipkOffset"
                                                class="transition-all duration-1000 drop-shadow-[0_2px_4px_rgba(99,102,241,0.2)]" 
                                            />
                                            
                                            <defs>
                                                <linearGradient id="ipkGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" stop-color="#818cf8" />
                                                    <stop offset="100%" stop-color="#4f46e5" />
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                        
                                        <!-- Central Text -->
                                        <div class="absolute text-center">
                                            <p class="text-3xl font-black text-slate-800 tracking-tight">
                                                {{ formatIpk(auth.progress?.ipk) || '0.00' }}
                                            </p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">IPK Aktif</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- IPK Info and Badges -->
                            <div class="mt-6 pt-6 border-t border-slate-100 space-y-4">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="flex items-center gap-1.5 text-slate-500 font-medium">
                                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 block"></span>
                                        IPK Aktif
                                    </span>
                                    <span class="font-bold text-slate-700">{{ formatIpk(auth.progress?.ipk) || '0.00' }} / 4.00</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="flex items-center gap-1.5 text-slate-500 font-medium">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 block"></span>
                                        Target IPK
                                    </span>
                                    <span class="font-bold text-slate-700">{{ formatIpk(auth.progress?.target_ipk) || '-' }}</span>
                                </div>

                                <!-- IPK Comparison Status Badge -->
                                <div class="pt-2">
                                    <div v-if="!auth.progress?.target_ipk" class="bg-slate-50 text-slate-500 text-xs font-semibold py-2 px-3 rounded-xl flex items-center gap-1.5 border border-slate-100 justify-center">
                                        <InfoIcon class="w-4 h-4 text-slate-400" /> Target belum dikonfigurasi
                                    </div>
                                    <div v-else-if="ipkGap <= 0" class="bg-emerald-50 text-emerald-700 text-xs font-bold py-2 px-3 rounded-xl flex items-center gap-1.5 border border-emerald-100 justify-center shadow-sm shadow-emerald-500/5">
                                        <CheckCircle2Icon class="w-4 h-4 text-emerald-600 shrink-0" /> Target IPK Tercapai! 🎉
                                    </div>
                                    <div v-else class="bg-amber-50 text-amber-700 text-xs font-bold py-2 px-3 rounded-xl flex items-center gap-1.5 border border-amber-100 justify-center">
                                        <TrendingUpIcon class="w-4 h-4 text-amber-600 shrink-0" /> Butuh +{{ ipkGap }} untuk capai target
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card B: SKS Tracker -->
                        <div class="bg-white/95 border border-slate-200/60 rounded-3xl p-8 shadow-md hover:shadow-lg transition-all duration-300 relative overflow-hidden flex flex-col justify-between group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50/50 rounded-full blur-2xl opacity-70 pointer-events-none group-hover:scale-125 transition-all duration-500" />
                            
                            <div>
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Kredit Semester (SKS)</h3>
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-inner">
                                        <BookOpenIcon class="w-5 h-5" />
                                    </div>
                                </div>

                                <!-- Current SKS visual representation -->
                                <div class="text-center py-6">
                                    <div class="inline-block relative">
                                        <span class="text-6xl font-black text-slate-800 tracking-tight">{{ auth.progress?.total_sks || '0' }}</span>
                                        <span class="text-lg font-bold text-slate-400 ml-1">/ 144 SKS</span>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total SKS Lulus</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-slate-100 space-y-4">
                                <!-- Progress Bars (Current vs Target vs Graduation) -->
                                <div class="space-y-2.5">
                                    <div class="flex justify-between text-xs font-semibold">
                                        <span class="text-slate-500">Progress Kelulusan</span>
                                        <span class="text-emerald-600 font-bold">{{ progressPercentage }}% Completed</span>
                                    </div>
                                    <div class="relative w-full h-3.5 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                                        <!-- Target SKS bar marker (light blue/cyan background highlight) -->
                                        <div 
                                            v-if="auth.progress?.target_sks"
                                            class="absolute top-0 left-0 bg-emerald-100/60 h-full rounded-full transition-all duration-1000"
                                            :style="`width: ${Math.min(Math.round((auth.progress.target_sks / 144) * 100), 100)}%`"
                                        />
                                        <!-- Current SKS progress bar (active emerald gradient) -->
                                        <div 
                                            class="absolute top-0 left-0 bg-gradient-to-r from-emerald-400 to-teal-600 h-full rounded-full transition-all duration-1000 shadow-[0_1px_3px_rgba(16,185,129,0.3)]"
                                            :style="`width: ${progressPercentage}%`"
                                        >
                                            <div class="absolute inset-0 bg-white/10 w-full h-full animate-[pulse_2s_infinite]"
                                                 style="background-image: linear-gradient(45deg, rgba(255,255,255,0.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.15) 75%, transparent 75%, transparent); background-size: 0.75rem 0.75rem;" />
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-xs font-medium text-slate-500">
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">Target SKS</p>
                                        <p class="font-bold text-slate-700 mt-0.5">{{ auth.progress?.target_sks ? `${auth.progress.target_sks} SKS` : '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">Sisa ke 144</p>
                                        <p class="font-bold text-slate-700 mt-0.5">{{ Math.max(144 - (auth.progress?.total_sks || 0), 0) }} SKS</p>
                                    </div>
                                </div>

                                <!-- SKS Gap Status Badge -->
                                <div class="pt-1">
                                    <div v-if="!auth.progress?.target_sks" class="bg-slate-50 text-slate-500 text-xs font-semibold py-2 px-3 rounded-xl flex items-center gap-1.5 border border-slate-100 justify-center">
                                        <InfoIcon class="w-4 h-4 text-slate-400" /> Target belum dikonfigurasi
                                    </div>
                                    <div v-else-if="sksGap <= 0" class="bg-emerald-50 text-emerald-700 text-xs font-bold py-2 px-3 rounded-xl flex items-center gap-1.5 border border-emerald-100 justify-center shadow-sm shadow-emerald-500/5">
                                        <CheckCircle2Icon class="w-4 h-4 text-emerald-600 shrink-0" /> Target SKS Tercapai! 🎉
                                    </div>
                                    <div v-else class="bg-amber-50 text-amber-700 text-xs font-bold py-2 px-3 rounded-xl flex items-center gap-1.5 border border-amber-100 justify-center">
                                        <CompassIcon class="w-4 h-4 text-amber-600 shrink-0" /> Kurang {{ sksGap }} SKS untuk target
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Graduation Roadmap & TAK Progress -->
                    <div class="bg-white/95 border border-slate-200/60 rounded-3xl p-8 shadow-md relative overflow-hidden">
                        <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-slate-50 rounded-full blur-2xl opacity-60 pointer-events-none" />

                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                                    <CompassIcon class="w-5 h-5 text-indigo-500" />
                                    Roadmap Target Kelulusan
                                </h3>
                                <p class="text-slate-500 text-sm mt-0.5">Jalur perjalanan studi berdasarkan target semester lulus Anda</p>
                            </div>

                            <span v-if="semesterRemaining > 0" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold rounded-xl shadow-sm">
                                <ClockIcon class="w-3.5 h-3.5 animate-pulse" /> Sisa {{ semesterRemaining }} Semester lagi
                            </span>
                            <span v-else-if="targetSemester" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold rounded-xl shadow-sm">
                                <CheckCircle2Icon class="w-3.5 h-3.5" /> Target Terlampaui / Tercapai!
                            </span>
                        </div>

                        <!-- Roadmap Steps -->
                        <div class="relative flex items-center justify-between mt-8 mb-4 px-2 md:px-6">
                            <!-- Background Connective Line -->
                            <div class="absolute left-6 right-6 h-1 bg-slate-100 top-[18px] -translate-y-1/2 rounded-full"></div>
                            <!-- Filled Line (Active timeline indicator) -->
                            <div 
                                class="absolute left-6 h-1 bg-gradient-to-r from-indigo-500 to-violet-600 top-[18px] -translate-y-1/2 rounded-full transition-all duration-1000" 
                                :style="`width: ${semesterTimelineWidth}%`"
                            ></div>
                            
                            <!-- Point 1: Mulai (Sem 1) -->
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-9 h-9 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-black ring-4 ring-white shadow">
                                    1
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2.5">Awal Studi</span>
                            </div>

                            <!-- Point 2: Sekarang (Sem X) -->
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-black ring-4 ring-indigo-100 shadow-md">
                                    {{ currentSemester }}
                                </div>
                                <span class="text-[10px] font-extrabold text-indigo-600 uppercase tracking-widest mt-2.5">Sem {{ currentSemester }} (Aktif)</span>
                            </div>

                            <!-- Point 3: Target Lulus (Sem Y) -->
                            <div class="relative z-10 flex flex-col items-center">
                                <div 
                                    class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-black shadow-md transition-all duration-300"
                                    :class="targetSemester ? (currentSemester >= targetSemester ? 'bg-emerald-500 text-white ring-4 ring-emerald-100' : 'bg-rose-500 text-white ring-4 ring-rose-100') : 'bg-slate-200 text-slate-400 ring-4 ring-white'"
                                >
                                    {{ targetSemester || '?' }}
                                </div>
                                <span 
                                    class="text-[10px] font-extrabold uppercase tracking-widest mt-2.5"
                                    :class="targetSemester ? (currentSemester >= targetSemester ? 'text-emerald-600' : 'text-rose-500') : 'text-slate-400'"
                                >
                                    {{ targetSemester ? `Target Lulus (Sem ${targetSemester})` : 'Target Lulus' }}
                                </span>
                            </div>
                        </div>

                        <!-- TAK Section for S1 Programs -->
                        <div v-if="isS1" class="mt-8 pt-8 border-t border-slate-100">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 mb-4">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Transkrip Aktivitas Kemahasiswaan (TAK)</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">Persyaratan maksimum kelulusan untuk jenjang S1 adalah 120 poin</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-3xl font-black text-amber-600">{{ auth.progress?.tak || 0 }}</span>
                                    <span class="text-slate-400 text-xs font-bold ml-1">/ 120 Poin</span>
                                </div>
                            </div>
                            
                            <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden shadow-inner relative">
                                <div 
                                    class="bg-gradient-to-r from-amber-400 to-orange-500 h-full rounded-full transition-all duration-1000 shadow-[0_1px_3px_rgba(245,158,11,0.3)]"
                                    :style="`width: ${takPercentage}%`"
                                ></div>
                            </div>
                            <div class="flex justify-between items-center text-[10px] font-bold text-slate-400 mt-2.5">
                                <span>0 Poin</span>
                                <span class="text-amber-500">{{ takPercentage }}% Completed</span>
                                <span>120 Poin</span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Graduation Predictor & Study Strategist -->
                    <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-950 border border-slate-800 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
                        <!-- Abstract glowing backgrounds -->
                        <div class="absolute -right-16 -top-16 w-60 h-60 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none" />
                        <div class="absolute -left-16 -bottom-16 w-60 h-60 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none" />

                        <div class="relative z-10">
                            <!-- Header -->
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-300 shadow-inner">
                                    <SparklesIcon class="w-5 h-5 animate-pulse" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-extrabold text-white">AI Graduation Predictor & Study Strategist</h3>
                                    <p class="text-slate-400 text-xs mt-0.5">Simulasi cerdas kelulusan Anda berdasarkan data akademik saat ini</p>
                                </div>
                            </div>

                            <!-- Predictor Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">SKS yang Tersisa</p>
                                    <p class="text-2xl font-black text-white mt-1">{{ remainingSks }} <span class="text-xs text-slate-400 font-medium">SKS</span></p>
                                </div>
                                <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Kapasitas SKS/Semester</p>
                                    <p class="text-2xl font-black text-indigo-300 mt-1">{{ maxSksCapacity }} <span class="text-xs text-slate-400 font-medium">SKS</span></p>
                                </div>
                                <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Target Feasibility</p>
                                    <div class="mt-1.5">
                                        <span v-if="gpaForecastStatus === 'safe'" class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-bold rounded-lg">
                                            Sangat Aman
                                        </span>
                                        <span v-else-if="gpaForecastStatus === 'needs_improvement'" class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/20 border border-amber-500/30 text-amber-300 text-xs font-bold rounded-lg">
                                            Butuh Peningkatan
                                        </span>
                                        <span v-else-if="gpaForecastStatus === 'unrealistic'" class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-bold rounded-lg">
                                            Tidak Realistis
                                        </span>
                                        <span v-else class="text-xs text-slate-400">-</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Graduation Scenarios -->
                            <div class="space-y-4 mb-8">
                                <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Perbandingan Skenario Kelulusan</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Scenario 1: Optimistic -->
                                    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:bg-white/10 transition-all duration-300">
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="text-xs font-bold text-indigo-400 uppercase">Skenario Optimis (SKS Maksimal)</span>
                                            <span class="px-2 py-0.5 bg-indigo-500/20 text-indigo-300 text-[10px] font-bold rounded-md uppercase">Efisien</span>
                                        </div>
                                        <p class="text-xs text-slate-400">Mengambil batas maksimal {{ maxSksCapacity }} SKS berdasarkan performa IPK saat ini.</p>
                                        <div class="mt-4 flex justify-between items-baseline">
                                            <div>
                                                <p class="text-[10px] text-slate-500 uppercase tracking-wider">Sisa Semester</p>
                                                <p class="text-lg font-extrabold text-white mt-0.5">+{{ optimisticSemestersNeeded }} Semester</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] text-slate-500 uppercase tracking-wider">Perkiraan Lulus</p>
                                                <p class="text-lg font-extrabold text-indigo-300 mt-0.5">Semester {{ optimisticGraduationSemester }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Scenario 2: Normal -->
                                    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:bg-white/10 transition-all duration-300">
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="text-xs font-bold text-slate-400 uppercase">Skenario Normal (Beban Rata-rata)</span>
                                            <span class="px-2 py-0.5 bg-slate-500/20 text-slate-400 text-[10px] font-bold rounded-md uppercase">Santai</span>
                                        </div>
                                        <p class="text-xs text-slate-400">Mengambil beban studi rata-rata 18 SKS per semester secara stabil.</p>
                                        <div class="mt-4 flex justify-between items-baseline">
                                            <div>
                                                <p class="text-[10px] text-slate-500 uppercase tracking-wider">Sisa Semester</p>
                                                <p class="text-lg font-extrabold text-white mt-0.5">+{{ normalSemestersNeeded }} Semester</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] text-slate-500 uppercase tracking-wider">Perkiraan Lulus</p>
                                                <p class="text-lg font-extrabold text-slate-300 mt-0.5">Semester {{ normalGraduationSemester }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- GPA Forecast Study Strategy -->
                            <div v-if="auth.progress?.target_ipk" class="bg-white/5 border border-white/10 rounded-2xl p-5">
                                <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                    <CompassIcon class="w-4 h-4 text-indigo-400" /> Analisis Strategi IPK (GPA Forecast)
                                </h4>
                                
                                <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
                                    <div class="space-y-1">
                                        <p class="text-xs text-slate-400 leading-relaxed">
                                            Untuk mencapai target kelulusan IPK <span class="font-bold text-white">{{ formatIpk(auth.progress.target_ipk) }}</span>, Anda membutuhkan rata-rata IPK untuk sisa <span class="font-bold text-white">{{ remainingSks }} SKS</span> sebesar:
                                        </p>
                                        
                                        <!-- Forecast alert text based on feasibility -->
                                        <p v-if="gpaForecastStatus === 'unrealistic'" class="text-xs text-rose-400 font-medium">
                                            ⚠️ <strong>Peringatan:</strong> Target IPK tidak realistis karena membutuhkan nilai sisa di atas 4.00. Sesuaikan target IPK kelulusan Anda di form sebelah kanan.
                                        </p>
                                        <p v-else-if="gpaForecastStatus === 'needs_improvement'" class="text-xs text-amber-400 font-medium">
                                            📈 Anda harus menaikkan performa akademik di atas IPK saat ini (dari {{ formatIpk(auth.progress.ipk) }} ke {{ requiredIpkRemaining }}).
                                        </p>
                                        <p v-else-if="gpaForecastStatus === 'safe'" class="text-xs text-emerald-400 font-medium">
                                            ✅ Performa IPK Anda saat ini ({{ formatIpk(auth.progress.ipk) }}) sudah memadai. Cukup pertahankan konsistensi nilai Anda.
                                        </p>
                                    </div>
                                    
                                    <div class="px-5 py-3 rounded-xl bg-slate-900 border border-white/10 text-center shrink-0 w-full md:w-auto">
                                        <p class="text-[10px] text-slate-500 uppercase tracking-wider font-bold">IPK Sisa Dibutuhkan</p>
                                        <p class="text-3xl font-black mt-1 text-white" :class="{'text-rose-400': gpaForecastStatus === 'unrealistic', 'text-amber-300': gpaForecastStatus === 'needs_improvement', 'text-emerald-400': gpaForecastStatus === 'safe'}">
                                            {{ requiredIpkRemaining > 4.00 ? '> 4.00' : requiredIpkRemaining }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Form (Right Col) -->
                <div class="bg-white/95 border border-slate-200/60 rounded-3xl shadow-lg flex flex-col overflow-hidden">
                    <!-- Form Header with subtle gradient background -->
                    <div class="p-8 bg-gradient-to-r from-indigo-50/50 to-blue-50/50 border-b border-slate-100">
                        <h3 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                            <Edit3Icon class="w-5 h-5 text-indigo-600" /> 
                            Update Progress & Target
                        </h3>
                        <p class="text-slate-500 text-xs mt-1">Perbarui data terkini untuk menyesuaikan visualisasi tracker.</p>
                    </div>

                    <form class="p-8 flex-1 flex flex-col justify-between" @submit.prevent="submitProgress">
                        <div class="space-y-6 flex-1">
                            <!-- Section: Data Aktual Saat Ini -->
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 block" /> Data Aktual Saat Ini
                                </h4>
                                
                                <div class="space-y-4">
                                    <!-- NIM & Semester Saat Ini -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="nim">
                                                NIM <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                id="nim"
                                                v-model="form.nim"
                                                type="text"
                                                required
                                                class="w-full rounded-2xl border-slate-200 border px-4 py-3 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all bg-slate-50/50 focus:bg-white text-slate-800 font-bold"
                                            />
                                            <p v-if="form.errors.nim" class="text-xs text-rose-500 mt-1.5 font-medium">{{ form.errors.nim }}</p>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="semester">
                                                Semester Saat Ini <span class="text-red-500">*</span>
                                            </label>
                                            <select
                                                id="semester"
                                                v-model="form.semester"
                                                required
                                                class="w-full rounded-2xl border-slate-200 border px-4 py-3 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all bg-slate-50/50 focus:bg-white text-slate-800 font-bold"
                                            >
                                                <option v-for="n in 14" :key="n" :value="n">
                                                    Semester {{ n }}
                                                </option>
                                            </select>
                                            <p v-if="form.errors.semester" class="text-xs text-rose-500 mt-1.5 font-medium">{{ form.errors.semester }}</p>
                                        </div>
                                    </div>

                                    <!-- Program Studi -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="program_studi">
                                            Program Studi / Jurusan <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="program_studi"
                                            v-model="form.program_studi"
                                            type="text"
                                            required
                                            class="w-full rounded-2xl border-slate-200 border px-4 py-3 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all bg-slate-50/50 focus:bg-white text-slate-800 font-bold"
                                        />
                                        <p v-if="form.errors.program_studi" class="text-xs text-rose-500 mt-1.5 font-medium">{{ form.errors.program_studi }}</p>
                                    </div>

                                    <!-- Current IPK & SKS Lulus -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="ipk">
                                                IPK Saat Ini <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input
                                                    id="ipk"
                                                    v-model="form.ipk"
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    max="4"
                                                    required
                                                    class="w-full rounded-2xl border-slate-200 border px-4 py-3 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all bg-slate-50/50 focus:bg-white text-slate-800 font-bold"
                                                />
                                            </div>
                                            <p v-if="form.errors.ipk" class="text-xs text-rose-500 mt-1.5 font-medium">{{ form.errors.ipk }}</p>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="total_sks">
                                                SKS Lulus <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                id="total_sks"
                                                v-model="form.total_sks"
                                                type="number"
                                                min="0"
                                                max="144"
                                                required
                                                class="w-full rounded-2xl border-slate-200 border px-4 py-3 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all bg-slate-50/50 focus:bg-white text-slate-800 font-bold"
                                            />
                                            <p v-if="form.errors.total_sks" class="text-xs text-rose-500 mt-1.5 font-medium">{{ form.errors.total_sks }}</p>
                                        </div>
                                    </div>

                                    <!-- Passed Courses & TAK (side-by-side on desktop) -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="passed_courses">
                                                Matkul Lulus
                                            </label>
                                            <input
                                                id="passed_courses"
                                                v-model="form.passed_courses"
                                                type="number"
                                                min="0"
                                                class="w-full rounded-2xl border-slate-200 border px-4 py-3 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all bg-slate-50/50 focus:bg-white text-slate-800 font-semibold"
                                            />
                                            <p v-if="form.errors.passed_courses" class="text-xs text-rose-500 mt-1.5 font-medium">{{ form.errors.passed_courses }}</p>
                                        </div>

                                        <div v-if="isS1">
                                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="tak">
                                                Poin TAK (Maksimal 120)
                                            </label>
                                            <input
                                                id="tak"
                                                v-model="form.tak"
                                                type="number"
                                                min="0"
                                                max="120"
                                                class="w-full rounded-2xl border-slate-200 border px-4 py-3 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all bg-slate-50/50 focus:bg-white text-slate-800 font-semibold"
                                            />
                                            <p v-if="form.errors.tak" class="text-xs text-rose-500 mt-1.5 font-medium">{{ form.errors.tak }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Target Akademik -->
                            <div class="border-t border-slate-100 pt-6 mt-6">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 block" /> Target Akademik Kelulusan
                                </h4>

                                <div class="space-y-4">
                                    <!-- Target IPK & Target SKS -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="target_ipk">
                                                Target IPK
                                            </label>
                                            <input
                                                id="target_ipk"
                                                v-model="form.target_ipk"
                                                type="number"
                                                step="0.01"
                                                :min="form.ipk"
                                                max="4"
                                                placeholder="contoh: 3.80"
                                                class="w-full rounded-2xl border-slate-200 border px-4 py-3 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all bg-slate-50/50 focus:bg-white text-slate-800 font-bold placeholder-slate-300"
                                            />
                                            <p v-if="form.errors.target_ipk" class="text-xs text-rose-500 mt-1.5 font-medium">{{ form.errors.target_ipk }}</p>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="target_sks">
                                                Target SKS
                                            </label>
                                            <input
                                                id="target_sks"
                                                v-model="form.target_sks"
                                                type="number"
                                                :min="form.total_sks"
                                                max="144"
                                                placeholder="maks: 144"
                                                class="w-full rounded-2xl border-slate-200 border px-4 py-3 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all bg-slate-50/50 focus:bg-white text-slate-800 font-bold placeholder-slate-300"
                                            />
                                            <p v-if="form.errors.target_sks" class="text-xs text-rose-500 mt-1.5 font-medium">{{ form.errors.target_sks }}</p>
                                        </div>
                                    </div>

                                    <!-- Target Semester Lulus -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5" for="target_semester">
                                            Target Semester Lulus
                                        </label>
                                        <select
                                            id="target_semester"
                                            v-model="form.target_semester"
                                            class="w-full rounded-2xl border-slate-200 border px-4 py-3 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all bg-slate-50/50 focus:bg-white text-slate-800 font-bold"
                                        >
                                            <option value="">-- Pilih target --</option>
                                            <option v-for="n in 14" :key="n" :value="n">
                                                Semester {{ n }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.target_semester" class="text-xs text-rose-500 mt-1.5 font-medium">{{ form.errors.target_semester }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="pt-8 border-t border-slate-100 mt-8 shrink-0">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-3.5 px-4 text-sm font-extrabold text-white bg-indigo-600 rounded-2xl hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-500/20 active:scale-[0.99] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-md shadow-indigo-600/10 cursor-pointer"
                            >
                                <SaveIcon class="w-4 h-4" /> Simpan Perubahan
                            </button>

                            <Transition
                                enter-active-class="transition ease-out duration-300"
                                enter-from-class="opacity-0 translate-y-2"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition ease-in duration-200"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 -translate-y-2"
                            >
                                <p
                                    v-if="form.recentlySuccessful"
                                    class="text-xs font-bold text-emerald-600 text-center mt-3.5 flex items-center justify-center gap-1.5 py-1 bg-emerald-50 rounded-lg border border-emerald-100/50 animate-bounce"
                                >
                                    <CheckCircle2Icon class="w-3.5 h-3.5" /> Berhasil disimpan!
                                </p>
                            </Transition>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
    import { computed } from 'vue';
    import { useForm, Head } from '@inertiajs/vue3';
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import {
        AwardIcon,
        Edit3Icon,
        SaveIcon,
        CheckCircle2Icon,
        TrendingUpIcon,
        BookOpenIcon,
        ClockIcon,
        SparklesIcon,
        InfoIcon,
        CompassIcon
    } from 'lucide-vue-next';

    const props = defineProps({
        auth: { type: Object, default: () => ({}) },
    });

    // Determine if student is S1 (Bachelor Degree)
    const isS1 = computed(() => {
        const prog = (props.auth.user?.mahasiswa?.program_studi || '').toLowerCase();
        if (prog.includes('s1')) return true;
        if (prog.includes('d3') || prog.includes('d4') || prog.includes('s2') || prog.includes('s3')) return false;
        return true;
    });

    // Calculate SKS completion percentage (against typical 144 SKS graduation target)
    const progressPercentage = computed(() => {
        const passedSks = props.auth.progress?.total_sks || 0;
        const p = Math.round((passedSks / 144) * 100);
        return p > 100 ? 100 : p;
    });

    // Calculate TAK points percentage (against maximum 120 target)
    const takPercentage = computed(() => {
        const takPoints = props.auth.progress?.tak || 0;
        const p = Math.round((takPoints / 120) * 100);
        return p > 100 ? 100 : p;
    });

    // Format IPK decimal places safely
    const formatIpk = (value) => {
        if (value === null || value === undefined || value === '') return '';
        const num = parseFloat(value);
        return isNaN(num) ? '' : num.toFixed(2);
    };

    // IPK Gauge Circumference and offsets
    const ipkCircumference = 2 * Math.PI * 50;
    const targetIpkCircumference = 2 * Math.PI * 62;

    const ipkOffset = computed(() => {
        const ipk = parseFloat(props.auth.progress?.ipk || 0);
        const fraction = ipk / 4.0;
        return ipkCircumference * (1 - Math.min(Math.max(fraction, 0), 1));
    });

    const targetIpkOffset = computed(() => {
        const target = parseFloat(props.auth.progress?.target_ipk || 0);
        const fraction = target / 4.0;
        return targetIpkCircumference * (1 - Math.min(Math.max(fraction, 0), 1));
    });

    // IPK gap to target
    const ipkGap = computed(() => {
        const current = parseFloat(props.auth.progress?.ipk || 0);
        const target = parseFloat(props.auth.progress?.target_ipk || 0);
        if (!target) return 0;
        const gap = target - current;
        return gap > 0 ? gap.toFixed(2) : 0;
    });

    // SKS gap to target
    const sksGap = computed(() => {
        const current = parseInt(props.auth.progress?.total_sks || 0);
        const target = parseInt(props.auth.progress?.target_sks || 0);
        if (!target) return 0;
        const gap = target - current;
        return gap > 0 ? gap : 0;
    });

    // Semester milestones
    const currentSemester = computed(() => {
        return parseInt(props.auth.user?.mahasiswa?.semester || 1);
    });

    const targetSemester = computed(() => {
        return parseInt(props.auth.progress?.target_semester || 0);
    });

    const semesterRemaining = computed(() => {
        const current = currentSemester.value;
        const target = targetSemester.value;
        if (!target || target <= current) return 0;
        return target - current;
    });

    const semesterTimelineWidth = computed(() => {
        const current = currentSemester.value;
        const target = targetSemester.value;
        if (!target) return 0;
        if (current >= target) return 100;
        const percent = ((current - 1) / (target - 1)) * 100;
        return Math.min(Math.max(percent, 0), 100);
    });

    // Remaining SKS to graduate
    const remainingSks = computed(() => {
        const currentSks = parseInt(props.auth.progress?.total_sks || 0);
        return Math.max(144 - currentSks, 0);
    });

    // Maximum SKS limit per semester based on current IPK
    const maxSksCapacity = computed(() => {
        const ipk = parseFloat(props.auth.progress?.ipk || 0);
        if (ipk >= 3.00) return 24;
        if (ipk >= 2.50) return 21;
        if (ipk >= 2.00) return 18;
        return 15;
    });

    // Semesters needed under Optimistic Scenario (max capacity)
    const optimisticSemestersNeeded = computed(() => {
        if (remainingSks.value === 0) return 0;
        return Math.ceil(remainingSks.value / maxSksCapacity.value);
    });

    // Predicted Graduation Semester under Optimistic Scenario
    const optimisticGraduationSemester = computed(() => {
        return currentSemester.value + optimisticSemestersNeeded.value;
    });

    // Semesters needed under Normal Scenario (18 SKS)
    const normalSemestersNeeded = computed(() => {
        if (remainingSks.value === 0) return 0;
        return Math.ceil(remainingSks.value / 18);
    });

    // Predicted Graduation Semester under Normal Scenario
    const normalGraduationSemester = computed(() => {
        return currentSemester.value + normalSemestersNeeded.value;
    });

    // GPA Forecast: Target IPK for remaining courses
    const targetIpkValue = computed(() => {
        return parseFloat(props.auth.progress?.target_ipk || 0);
    });

    const currentIpkValue = computed(() => {
        return parseFloat(props.auth.progress?.ipk || 0);
    });

    const currentSksValue = computed(() => {
        return parseInt(props.auth.progress?.total_sks || 0);
    });

    const requiredIpkRemaining = computed(() => {
        const targetIpk = targetIpkValue.value;
        const currentIpk = currentIpkValue.value;
        const currentSks = currentSksValue.value;
        const remSks = remainingSks.value;

        if (!targetIpk || remSks === 0) return 0;

        const totalTargetPoints = 144 * targetIpk;
        const currentPoints = currentSks * currentIpk;
        const requiredPoints = totalTargetPoints - currentPoints;
        const requiredIpk = requiredPoints / remSks;

        return parseFloat(requiredIpk.toFixed(2));
    });

    // Feasibility status of the target GPA
    const gpaForecastStatus = computed(() => {
        const targetIpk = targetIpkValue.value;
        if (!targetIpk) return 'none';
        const reqIpk = requiredIpkRemaining.value;
        if (reqIpk > 4.00) return 'unrealistic';
        if (reqIpk <= currentIpkValue.value) return 'safe';
        return 'needs_improvement';
    });

    // Initialize Form with props values
    const form = useForm({
        ipk: props.auth.progress?.ipk || 0,
        total_sks: props.auth.progress?.total_sks || 0,
        passed_courses: props.auth.progress?.passed_courses || 0,
        tak: props.auth.progress?.tak || 0,
        target_ipk: props.auth.progress?.target_ipk || '',
        target_sks: props.auth.progress?.target_sks || '',
        target_semester: props.auth.progress?.target_semester || '',
        nim: props.auth.user?.mahasiswa?.nim || '',
        semester: props.auth.user?.mahasiswa?.semester || 1,
        program_studi: props.auth.user?.mahasiswa?.program_studi || '',
    });

    // Submit form action
    const submitProgress = () => {
        form.put(route('mahasiswa.progress.update'), {
            preserveScroll: true,
        });
    };
</script>

<style scoped>
    /* Custom animations & smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
