<?php

declare(strict_types=1);

namespace App\Filament\Resources\QuestionResource\Widgets;

use App\Models\Question;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class QuestionOverview extends BaseWidget
{
    /**
     * Whether the widget should be lazy loaded.
     */
    protected static bool $isLazy = false;

    /**
     * Get the widget's stats.
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $counts = Question::query()
            ->selectRaw('COUNT(*) AS total, SUM(is_reported) AS reported, SUM(is_ignored) AS ignored')
            ->first();

        $counts ??= [
            'total' => 0,
            'reported' => 0,
            'ignored' => 0,
        ];

        return [
            Stat::make('Total Questions', type($counts['total'])->asInt()),
            Stat::make('Reported Questions', type($counts['reported'])->asInt()),
            Stat::make('Ignored Questions', type($counts['ignored'])->asInt()),
        ];
    }
}
