<?php

namespace App\MoonShine;

use App\Models\Client;
use App\Models\Order;
use MoonShine\Dashboard\DashboardBlock;
use MoonShine\Dashboard\DashboardScreen;
use MoonShine\Dashboard\TextBlock;
use MoonShine\Metrics\DonutChartMetric;
use MoonShine\Metrics\LineChartMetric;
use MoonShine\Metrics\ValueMetric;

class Dashboard extends DashboardScreen
{
  public function blocks(): array
  {

    return [
      DashboardBlock::make([
        TextBlock::make(
          'Статистика по продажам',
          ''
        )
      ]),

      DashboardBlock::make([
        ValueMetric::make('Заказы')
          ->value(Order::query()->count())
          ->columnSpan(3),

        ValueMetric::make('Клиенты')
          ->value(Client::query()->count())
          ->columnSpan(3),

        ValueMetric::make('Всего капсул продано')
          ->value(Order::query()->sum('given_bottle'))
          ->columnSpan(3),

        ValueMetric::make('Всего капсул возвращено')
          ->value(Order::query()->sum('taken_bottle'))
          ->progress(Order::query()->sum('given_bottle'))
          ->columnSpan(3),
      ]),

      DashboardBlock::make([
        DonutChartMetric::make('Статус капсул')
          ->columnSpan(6)
          ->values([
            'Продано' => (int) Order::query()->sum('given_bottle'),
            'Возвращено' => (int) Order::query()->sum('taken_bottle'),
          ]),

        LineChartMetric::make('Движение капсул')
          ->line([
            'Продано' => Order::query()
              ->selectRaw('SUM(given_bottle) as sum, DATE_FORMAT(date, "%d.%m.%Y") as date')
              ->groupBy('date')
              ->pluck('sum', 'date')
              ->toArray()
          ])
          ->line([
            'Возвращено' => Order::query()
              ->selectRaw('SUM(taken_bottle) as sum, DATE_FORMAT(date, "%d.%m.%Y") as date')
              ->groupBy('date')
              ->pluck('sum', 'date')
              ->toArray()
          ], '#EC4176')
          ->columnSpan(6),
      ]),
    ];
  }
}
