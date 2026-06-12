<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()

            // ─── SIBIMA Brand Identity ────────────────────────────────────
            ->brandName('SIBIMA')
            ->brandLogo(asset('images/logo-sibima.svg'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/logo-sibima.svg'))

            // ─── SIBIMA Color Scheme ──────────────────────────────────────
            ->colors([
                'primary' => Color::hex('#1F4C7A'),   // Deep Blue — Navbar, header, primary button
                'gray' => Color::hex('#1B3F66'),    // Navy Blue — Hover state, footer, sidebar
                'info' => Color::hex('#2FA7A0'),    // Teal      — Links, icons, accent UI
                'success' => Color::hex('#6DBE45'),    // Leaf Green — Success state, progress
                'warning' => Color::hex('#F39C12'),    // Orange    — CTA button, alerts
                'danger' => Color::hex('#E74C3C'),    // Red       — Danger / destructive actions
            ])

            // ─── Typography & UI Preferences ─────────────────────────────
            ->font('Inter')
            ->darkMode()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->sidebarCollapsibleOnDesktop()

            // ─── Resource & Page Discovery ────────────────────────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
                \App\Filament\Widgets\AiAssistantUsageWidget::class,
            ])

            // ─── Middleware ──────────────────────────────────────────────
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
