<script setup>
    import { Head, Link, usePage } from '@inertiajs/vue3';

    defineProps({
        canLogin: {
            type: Boolean,
        },
        canRegister: {
            type: Boolean,
        },
        laravelVersion: {
            type: String,
            required: true,
        },
        phpVersion: {
            type: String,
            required: true,
        },
    });

    const page = usePage();
</script>

<template>
    <Head title="SIBIMA — Sistem Bimbingan Mahasiswa">
        <meta
            name="description"
            content="SIBIMA membantu mahasiswa dan dosen untuk mengatur jadwal bimbingan, memantau progres akademik, dan mencapai target kelulusan secara lebih efisien."
        />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="sibima-root">
        <!-- ═══════════════════════════════════════════════
             NAVBAR
        ═══════════════════════════════════════════════ -->
        <header class="navbar">
            <div class="navbar-inner">
                <!-- Logo -->
                <Link href="/" class="navbar-brand">
                    <img src="/images/logo-sibima.svg" alt="SIBIMA Logo" class="navbar-logo" />
                    <!-- <span class="navbar-wordmark">SIBIMA</span> -->
                </Link>

                <!-- Auth Links -->
                <nav v-if="canLogin" class="navbar-nav" aria-label="Autentikasi">
                    <template v-if="page.props.auth.user">
                        <Link
                            id="nav-dashboard-btn"
                            :href="route('dashboard')"
                            class="btn btn-primary"
                        >
                            Dashboard
                            <!-- arrow icon -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="btn-icon"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </Link>
                    </template>

                    <template v-else>
                        <Link id="nav-login-btn" :href="route('login')" class="btn btn-ghost">
                            Masuk
                        </Link>
                        <Link
                            v-if="canRegister"
                            id="nav-register-btn"
                            :href="route('register')"
                            class="btn btn-primary"
                        >
                            Daftar Sekarang
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- ═══════════════════════════════════════════════
             HERO SECTION
        ═══════════════════════════════════════════════ -->
        <main>
            <section class="hero" aria-labelledby="hero-headline">
                <!-- decorative blobs -->
                <div class="hero-blob hero-blob--top-right" aria-hidden="true" />
                <div class="hero-blob hero-blob--bottom-left" aria-hidden="true" />

                <div class="hero-inner">
                    <!-- Badge -->
                    <div class="hero-badge">
                        <span class="hero-badge-dot" />
                        Telkom University · 2026
                    </div>

                    <!-- Headline -->
                    <h1 id="hero-headline" class="hero-headline">
                        Sistem Bimbingan Mahasiswa<br />
                        yang
                        <span class="hero-headline-accent">Lebih Mudah</span>
                        &amp;
                        <span class="hero-headline-accent">Terstruktur</span>
                    </h1>

                    <!-- Sub-headline -->
                    <p class="hero-subheadline">
                        Atur jadwal bimbingan, pantau progres akademik, dan capai target kelulusanmu
                        dengan lebih efisien bersama dosen pembimbing.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="hero-actions">
                        <Link
                            id="hero-cta-btn"
                            :href="route('login')"
                            class="btn btn-primary btn-lg"
                        >
                            Mulai Bimbingan
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="btn-icon"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </Link>
                        <a id="hero-learn-btn" href="#features" class="btn btn-outline btn-lg">
                            Pelajari Fitur
                        </a>
                    </div>

                    <!-- Social proof mini stats -->
                    <div class="hero-stats" aria-label="Statistik singkat">
                        <div class="hero-stat">
                            <span class="hero-stat-number">500+</span>
                            <span class="hero-stat-label">Mahasiswa Aktif</span>
                        </div>
                        <div class="hero-stat-divider" aria-hidden="true" />
                        <div class="hero-stat">
                            <span class="hero-stat-number">120+</span>
                            <span class="hero-stat-label">Dosen Pembimbing</span>
                        </div>
                        <div class="hero-stat-divider" aria-hidden="true" />
                        <div class="hero-stat">
                            <span class="hero-stat-number">98%</span>
                            <span class="hero-stat-label">Kepuasan Pengguna</span>
                        </div>
                    </div>
                </div>

                <!-- Floating card decoration -->
                <div class="hero-card-preview" aria-hidden="true">
                    <div class="preview-card">
                        <div class="preview-card-header">
                            <div class="preview-avatar" />
                            <div class="preview-info">
                                <div class="preview-name">Jadwal Bimbingan</div>
                                <div class="preview-sub">Senin, 21 Apr 2026</div>
                            </div>
                            <span class="preview-badge-status">Terkonfirmasi</span>
                        </div>
                        <div class="preview-progress-section">
                            <div class="preview-progress-label">
                                <span>Progres Skripsi</span>
                                <span class="preview-progress-pct">72%</span>
                            </div>
                            <div class="preview-progress-bar">
                                <div class="preview-progress-fill" style="width: 72%" />
                            </div>
                        </div>
                        <div class="preview-chips">
                            <span class="preview-chip">BAB III ✓</span>
                            <span class="preview-chip">BAB IV ✓</span>
                            <span class="preview-chip preview-chip--muted">BAB V</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════
                 FEATURES SECTION
            ═══════════════════════════════════════════════ -->
            <section id="features" class="features" aria-labelledby="features-title">
                <div class="features-inner">
                    <div class="section-label">Fitur Unggulan</div>
                    <h2 id="features-title" class="section-title">
                        Semua yang kamu butuhkan,<br />dalam satu platform
                    </h2>
                    <p class="section-subtitle">
                        SIBIMA dirancang agar proses bimbingan terasa ringan — dari booking jadwal
                        hingga notifikasi otomatis.
                    </p>

                    <div class="features-grid">
                        <!-- Feature 1 -->
                        <div id="feature-booking" class="feature-card">
                            <div class="feature-icon-wrap feature-icon-wrap--blue">
                                <!-- Heroicons: calendar-days -->
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="feature-icon"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"
                                    />
                                </svg>
                            </div>
                            <h3 class="feature-title">Booking Jadwal</h3>
                            <p class="feature-desc">
                                Mudah mengatur jadwal bimbingan dengan dosen pembimbing tanpa perlu
                                bolak-balik konfirmasi manual.
                            </p>
                            <ul class="feature-bullets">
                                <li>Pilih slot waktu tersedia dosen</li>
                                <li>Konfirmasi otomatis &amp; instan</li>
                                <li>Riwayat sesi lengkap</li>
                            </ul>
                        </div>

                        <!-- Feature 2 -->
                        <div id="feature-progress" class="feature-card feature-card--highlight">
                            <div class="feature-badge-popular">⭐ Terpopuler</div>
                            <div class="feature-icon-wrap feature-icon-wrap--indigo">
                                <!-- Heroicons: chart-bar-square -->
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="feature-icon"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z"
                                    />
                                </svg>
                            </div>
                            <h3 class="feature-title">Pantau Progres</h3>
                            <p class="feature-desc">
                                Tracking target akademik dan kelulusan secara visual — kamu &amp;
                                dosen selalu tahu sejauh mana perjalananmu.
                            </p>
                            <ul class="feature-bullets">
                                <li>Dashboard progres per bab</li>
                                <li>Target kelulusan real-time</li>
                                <li>Laporan PDF siap cetak</li>
                            </ul>
                        </div>

                        <!-- Feature 3 -->
                        <div id="feature-notif" class="feature-card">
                            <div class="feature-icon-wrap feature-icon-wrap--violet">
                                <!-- Heroicons: bell-alert -->
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="feature-icon"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5"
                                    />
                                </svg>
                            </div>
                            <h3 class="feature-title">Notifikasi Otomatis</h3>
                            <p class="feature-desc">
                                Reminder dan persetujuan bimbingan secara real-time agar kamu tidak
                                pernah melewatkan sesi penting.
                            </p>
                            <ul class="feature-bullets">
                                <li>Push &amp; email reminder</li>
                                <li>Status persetujuan live</li>
                                <li>Pengingat deadline revisi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════
                 CTA BANNER
            ═══════════════════════════════════════════════ -->
            <section class="cta-banner" aria-labelledby="cta-banner-title">
                <div class="cta-banner-inner">
                    <h2 id="cta-banner-title" class="cta-banner-title">
                        Siap memulai perjalanan akademikmu?
                    </h2>
                    <p class="cta-banner-subtitle">
                        Bergabung bersama ribuan mahasiswa Telkom University yang sudah lebih
                        produktif dengan SIBIMA.
                    </p>
                    <Link id="cta-banner-btn" :href="route('login')" class="btn btn-white btn-lg">
                        Mulai Gratis Sekarang
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="btn-icon"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </Link>
                </div>
            </section>
        </main>

        <!-- ═══════════════════════════════════════════════
             FOOTER
        ═══════════════════════════════════════════════ -->
        <footer class="site-footer">
            <div class="site-footer-inner">
                <div class="footer-brand">
                    <img src="/images/logo-sibima.svg" alt="SIBIMA Logo" class="footer-logo" />
                    <span class="footer-wordmark">SIBIMA</span>
                </div>
                <p class="footer-copy">
                    &copy; 2026 SIBIMA — Telkom University. All rights reserved.
                </p>
                <p class="footer-tech">
                    Laravel v{{ laravelVersion }} &middot; PHP v{{ phpVersion }}
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
    /* ─────────────────────────────────────────────────────────
   DESIGN TOKENS
───────────────────────────────────────────────────────── */
    :root {
        --color-primary: #4f46e5; /* indigo-600 */
        --color-primary-dark: #3730a3; /* indigo-800 */
        --color-primary-light: #e0e7ff; /* indigo-100 */
        --color-accent: #2563eb; /* blue-600 */
        --color-text: #0f172a; /* slate-900 */
        --color-muted: #64748b; /* slate-500 */
        --color-border: #e2e8f0; /* slate-200 */
        --color-surface: #f8fafc; /* slate-50 */
        --radius-xl: 1rem;
        --radius-2xl: 1.5rem;
        --shadow-card:
            0 4px 24px -4px rgba(15, 23, 42, 0.08), 0 1px 2px -1px rgba(15, 23, 42, 0.04);
        --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* ─────────────────────────────────────────────────────────
   BASE
───────────────────────────────────────────────────────── */
    .sibima-root {
        font-family: var(--font-sans);
        color: var(--color-text);
        background-color: #ffffff;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* ─────────────────────────────────────────────────────────
   BUTTONS
───────────────────────────────────────────────────────── */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 1.25rem;
        border-radius: 0.625rem;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
        border: 2px solid transparent;
        line-height: 1.5;
    }

    .btn-lg {
        padding: 0.75rem 1.75rem;
        font-size: 1rem;
        border-radius: 0.75rem;
    }

    .btn-primary {
        background: var(--color-primary);
        color: #ffffff;
        box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
    }
    .btn-primary:hover {
        background: var(--color-primary-dark);
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4);
        transform: translateY(-1px);
    }
    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-ghost {
        background: transparent;
        color: var(--color-text);
    }
    .btn-ghost:hover {
        background: var(--color-surface);
        color: var(--color-primary);
    }

    .btn-outline {
        background: transparent;
        color: var(--color-primary);
        border-color: #c7d2fe;
    }
    .btn-outline:hover {
        background: var(--color-primary-light);
        border-color: var(--color-primary);
    }

    .btn-white {
        background: #ffffff;
        color: var(--color-primary);
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
    }
    .btn-white:hover {
        box-shadow: 0 4px 20px rgba(255, 255, 255, 0.25);
        transform: translateY(-1px);
    }

    .btn-icon {
        width: 1.1em;
        height: 1.1em;
        flex-shrink: 0;
    }

    /* ─────────────────────────────────────────────────────────
   NAVBAR
───────────────────────────────────────────────────────── */
    .navbar {
        position: sticky;
        top: 0;
        z-index: 50;
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-bottom: 1px solid var(--color-border);
    }

    .navbar-inner {
        max-width: 75rem;
        margin: 0 auto;
        padding: 0 1.5rem;
        height: 4rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        text-decoration: none;
    }

    .navbar-logo {
        height: 2rem;
        width: auto;
    }

    .navbar-wordmark {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--color-primary);
        letter-spacing: -0.03em;
    }

    .navbar-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* ─────────────────────────────────────────────────────────
   HERO
───────────────────────────────────────────────────────── */
    .hero {
        position: relative;
        overflow: hidden;
        padding: 5rem 1.5rem 4rem;
        background: linear-gradient(160deg, #f0f4ff 0%, #ffffff 55%, #fdf4ff 100%);
    }

    .hero-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.35;
        pointer-events: none;
    }
    .hero-blob--top-right {
        width: 42rem;
        height: 42rem;
        background: radial-gradient(circle, #818cf8 0%, #6366f1 60%, transparent 100%);
        top: -14rem;
        right: -12rem;
    }
    .hero-blob--bottom-left {
        width: 32rem;
        height: 32rem;
        background: radial-gradient(circle, #a78bfa 0%, #7c3aed 60%, transparent 100%);
        bottom: -10rem;
        left: -8rem;
    }

    .hero-inner {
        position: relative;
        z-index: 1;
        max-width: 58rem;
        margin: 0 auto;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #eef2ff;
        color: var(--color-primary);
        border: 1px solid #c7d2fe;
        border-radius: 999px;
        padding: 0.3rem 0.9rem;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    .hero-badge-dot {
        width: 0.5rem;
        height: 0.5rem;
        background: var(--color-primary);
        border-radius: 50%;
        animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {
        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.6;
            transform: scale(0.75);
        }
    }

    .hero-headline {
        font-size: clamp(2rem, 5vw, 3.5rem);
        font-weight: 900;
        line-height: 1.12;
        letter-spacing: -0.04em;
        color: var(--color-text);
        margin: 0;
    }

    .hero-headline-accent {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #2563eb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-subheadline {
        font-size: clamp(1rem, 2vw, 1.2rem);
        color: var(--color-muted);
        line-height: 1.7;
        max-width: 44rem;
        margin: 0;
    }

    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: center;
    }

    .hero-stats {
        display: flex;
        align-items: center;
        gap: 2rem;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 0.5rem;
    }

    .hero-stat {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.2rem;
    }

    .hero-stat-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--color-primary);
        line-height: 1;
    }

    .hero-stat-label {
        font-size: 0.75rem;
        color: var(--color-muted);
        font-weight: 500;
    }

    .hero-stat-divider {
        width: 1px;
        height: 2.5rem;
        background: var(--color-border);
    }

    /* Floating preview card */
    .hero-card-preview {
        position: relative;
        z-index: 1;
        margin-top: 1rem;
        width: 100%;
        max-width: 26rem;
        animation: float-card 4s ease-in-out infinite;
    }

    @keyframes float-card {
        0%,
        100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    .preview-card {
        background: #ffffff;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-2xl);
        padding: 1.25rem 1.5rem;
        box-shadow:
            var(--shadow-card),
            0 0 0 1px rgba(79, 70, 229, 0.05);
        display: flex;
        flex-direction: column;
        gap: 1rem;
        text-align: left;
    }

    .preview-card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .preview-avatar {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #818cf8, #4f46e5);
        flex-shrink: 0;
    }

    .preview-info {
        flex: 1;
    }

    .preview-name {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--color-text);
        line-height: 1.3;
    }

    .preview-sub {
        font-size: 0.75rem;
        color: var(--color-muted);
    }

    .preview-badge-status {
        background: #dcfce7;
        color: #166534;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        white-space: nowrap;
    }

    .preview-progress-section {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .preview-progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: var(--color-muted);
        font-weight: 500;
    }

    .preview-progress-pct {
        font-weight: 700;
        color: var(--color-primary);
    }

    .preview-progress-bar {
        height: 0.5rem;
        background: var(--color-primary-light);
        border-radius: 999px;
        overflow: hidden;
    }

    .preview-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #6366f1, #4f46e5);
        border-radius: 999px;
        transition: width 1s ease;
    }

    .preview-chips {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .preview-chip {
        background: var(--color-primary-light);
        color: var(--color-primary);
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.2rem 0.6rem;
        border-radius: 0.4rem;
    }

    .preview-chip--muted {
        background: #f1f5f9;
        color: var(--color-muted);
    }

    /* ─────────────────────────────────────────────────────────
   FEATURES SECTION
───────────────────────────────────────────────────────── */
    .features {
        padding: 5rem 1.5rem;
        background: var(--color-surface);
    }

    .features-inner {
        max-width: 75rem;
        margin: 0 auto;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.25rem;
    }

    .section-label {
        display: inline-block;
        background: var(--color-primary-light);
        color: var(--color-primary);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.3rem 0.85rem;
        border-radius: 999px;
    }

    .section-title {
        font-size: clamp(1.75rem, 4vw, 2.75rem);
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--color-text);
        line-height: 1.2;
        margin: 0;
    }

    .section-subtitle {
        font-size: 1.05rem;
        color: var(--color-muted);
        line-height: 1.7;
        max-width: 38rem;
        margin: 0;
    }

    .features-grid {
        width: 100%;
        display: grid;
        gap: 1.5rem;
        grid-template-columns: 1fr;
        margin-top: 1rem;
    }

    @media (min-width: 768px) {
        .features-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .feature-card {
        position: relative;
        background: #ffffff;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-2xl);
        padding: 2rem 1.75rem;
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
        box-shadow: var(--shadow-card);
        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease,
            border-color 0.25s ease;
        overflow: hidden;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow:
            0 16px 40px -8px rgba(79, 70, 229, 0.15),
            0 4px 6px -2px rgba(15, 23, 42, 0.04);
        border-color: #c7d2fe;
    }

    .feature-card--highlight {
        background: linear-gradient(160deg, #eef2ff 0%, #ffffff 100%);
        border-color: #c7d2fe;
        box-shadow:
            var(--shadow-card),
            0 0 0 2px rgba(79, 70, 229, 0.1);
    }

    .feature-badge-popular {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        letter-spacing: 0.02em;
    }

    .feature-icon-wrap {
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .feature-icon-wrap--blue {
        background: #dbeafe;
        color: #2563eb;
    }
    .feature-icon-wrap--indigo {
        background: #e0e7ff;
        color: #4f46e5;
    }
    .feature-icon-wrap--violet {
        background: #ede9fe;
        color: #7c3aed;
    }

    .feature-icon {
        width: 1.75rem;
        height: 1.75rem;
    }

    .feature-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--color-text);
        margin: 0;
    }

    .feature-desc {
        font-size: 0.925rem;
        color: var(--color-muted);
        line-height: 1.65;
        margin: 0;
    }

    .feature-bullets {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }
    .feature-bullets li {
        font-size: 0.85rem;
        color: var(--color-muted);
        padding-left: 1.25rem;
        position: relative;
        font-weight: 500;
    }
    .feature-bullets li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: var(--color-primary);
        font-weight: 700;
        font-size: 0.8rem;
    }

    /* ─────────────────────────────────────────────────────────
   CTA BANNER
───────────────────────────────────────────────────────── */
    .cta-banner {
        padding: 4.5rem 1.5rem;
        background: linear-gradient(135deg, #4338ca 0%, #4f46e5 40%, #6d28d9 100%);
        position: relative;
        overflow: hidden;
    }
    .cta-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23fff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }

    .cta-banner-inner {
        position: relative;
        max-width: 50rem;
        margin: 0 auto;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.25rem;
    }

    .cta-banner-title {
        color: #ffffff;
        font-size: clamp(1.6rem, 4vw, 2.5rem);
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.2;
        margin: 0;
    }

    .cta-banner-subtitle {
        color: rgba(255, 255, 255, 0.75);
        font-size: 1.05rem;
        line-height: 1.65;
        margin: 0;
    }

    /* ─────────────────────────────────────────────────────────
   FOOTER
───────────────────────────────────────────────────────── */
    .site-footer {
        background: #0f172a;
        padding: 2.25rem 1.5rem;
        margin-top: auto;
    }

    .site-footer-inner {
        max-width: 75rem;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        text-align: center;
    }

    .footer-brand {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .footer-logo {
        height: 1.6rem;
        width: auto;
        filter: brightness(0) invert(1);
        opacity: 0.85;
    }

    .footer-wordmark {
        font-size: 1.1rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.02em;
    }

    .footer-copy {
        font-size: 0.85rem;
        color: #94a3b8;
        margin: 0;
    }

    .footer-tech {
        font-size: 0.75rem;
        color: #475569;
        margin: 0;
    }

    /* ─────────────────────────────────────────────────────────
   RESPONSIVE TWEAKS
───────────────────────────────────────────────────────── */
    @media (min-width: 1024px) {
        .hero {
            padding: 7rem 1.5rem 6rem;
        }

        .hero-inner {
            gap: 2rem;
        }

        .hero-stats {
            gap: 3rem;
        }

        .site-footer-inner {
            flex-direction: row;
            justify-content: space-between;
            text-align: left;
        }
    }

    @media (max-width: 480px) {
        .navbar-wordmark {
            font-size: 1.1rem;
        }
        .hero-stat-divider {
            display: none;
        }
        .hero-stats {
            gap: 1rem;
        }
    }
</style>
