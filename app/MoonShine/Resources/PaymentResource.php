<?php

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;
use MoonShine\Actions\ExportAction;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;
use MoonShine\Decorations\Block;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\Phone;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;

class PaymentResource extends Resource
{
  public static string $model = Payment::class;

  public static string $title = 'Способы оплаты';

  public string $titleField = 'name';

  public static int $itemsPerPage = 10;

  public static string $orderField = 'id';

  public static string $orderType = 'DESC';

  protected bool $createInModal = true;

  protected bool $editInModal = true;

  protected bool $showInModal = true;

  public function fields(): array
  {
    return [
      Block::make('Основная информация', [
        ID::make()
          ->sortable()
          ->showOnExport(),

        Text::make('Название', 'name')
          ->required()
          ->sortable()
          ->showOnExport(),
      ])
    ];
  }

  public function rules(Model $item): array
  {
    return [];
  }

  public function search(): array
  {
    return ['id'];
  }

  public function filters(): array
  {
    return [];
  }

  public function actions(): array
  {
    return [
      FiltersAction::make(trans('moonshine::ui.filters')),
      ExportAction::make('Экспорт')
        ->disk('public')
        ->dir('/exports')
        ->showInLine(),
    ];
  }
}
