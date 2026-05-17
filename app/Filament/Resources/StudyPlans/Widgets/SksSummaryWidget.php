<?php

namespace App\Filament\Resources\StudyPlans\Widgets;

use App\Models\StudyPlan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SksSummaryWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        $query = StudyPlan::query();

        if ($user->hasRole('Mahasiswa')) {
            $query->where('mahasiswa_id', $user->mahasiswa?->id);
        }

        $totalSks = $query->join('courses', 'study_plans.course_id', '=', 'courses.id')
            ->sum('courses.credits');

        $warning = $totalSks > 24 ? 'Warning: Exceeds maximum 24 SKS limit!' : 'Within limit';
        $color = $totalSks > 24 ? 'danger' : 'success';

        return [
            Stat::make('Total Credits (SKS)', $totalSks)
                ->description($warning)
                ->descriptionIcon($totalSks > 24 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($color),
        ];
    }
}
