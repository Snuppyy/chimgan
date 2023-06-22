<?php

namespace App\MoonShine\Resources;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;
use MoonShine\Decorations\Block;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\HasOne;
use MoonShine\Fields\Phone;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;

class ContactResource extends Resource
{
  public static string $model = Contact::class;
  public static string $title = 'Контакты';
  public string $titleField = 'tel';
  public static int $itemsPerPage = 10;
  public static string $orderField = 'id';
  public static string $orderType = 'DESC';
  protected bool $createInModal = true;
  protected bool $editInModal = true;
  protected bool $showInModal = true;


  public function fields(): array
  {
    return [
      Block::make([
        ID::make()->sortable(),

        Phone::make('Номер телефона', 'tel')
          ->mask('+998(00) 000-00-00')
          ->sortable(),

        Textarea::make('Адрес', 'address')
          ->sortable(),

      ])


    ];
  }

  public function rules(Model $item): array
  {
    return [];
  }

  public function search(): array
  {
    return ['id', 'name'];
  }

  public function filters(): array
  {
    return [];
  }

  public function actions(): array
  {
    return [
      FiltersAction::make(trans('moonshine::ui.filters')),
    ];
  }
}
