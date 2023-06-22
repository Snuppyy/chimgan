<?php

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use MoonShine\Actions\ExportAction;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Grid;
use MoonShine\Fields\HasMany;
use MoonShine\Fields\Phone;
use MoonShine\Fields\SwitchBoolean;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;

class ClientResource extends Resource
{
   public static string $model = Client::class;
   public static string $title = 'Клиенты';
   public string $titleField = 'name';
   public static int $itemsPerPage = 10;
   public static string $orderField = 'id';
   public static string $orderType = 'DESC';
   protected bool $createInModal = true;
   protected bool $editInModal = true;
   protected bool $showInModal = true;
   public static array $with = ['contacts'];

   public function fields(): array
   {
      return [


         Grid::make([
            Column::make([
               Block::make('Основная информация', [
                  ID::make()->sortable()
                     ->showOnExport(),

                  Text::make('Имя', 'name')
                     ->sortable()
                     ->required()
                     ->showOnExport(),

                  HasMany::make('Контакты', 'contacts')
                     ->fields([
                        Phone::make('Номер телефона', 'tel')
                           ->mask('+998999999999')
                           ->sortable(),

                        Textarea::make('Адрес', 'address')
                           ->sortable(),
                     ])
                     ->removable()
                     ->required(),

                  SwitchBoolean::make('Статус', 'status')
                     ->sortable()
                     ->required()
                     ->default(1)
                     ->hideOnDetail()
                     ->hideOnForm()
                     ->showOnExport(),
               ])
            ])
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
         ExportAction::make('Экспорт')
            ->disk('public')
            ->dir('/exports')
            ->showInLine(),
      ];
   }
}
