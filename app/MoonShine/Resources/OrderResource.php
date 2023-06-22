<?php

namespace App\MoonShine\Resources;

use App\Models\Client;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use MoonShine\Actions\ExportAction;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;
use MoonShine\BulkActions\BulkAction;
use MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\Date;
use MoonShine\Fields\HasMany;
use MoonShine\Fields\Number;
use MoonShine\Fields\SwitchBoolean;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;
use MoonShine\Fields\StackFields;
use MoonShine\Filters\BelongsToFilter;
use MoonShine\Filters\DateRangeFilter;
use MoonShine\Filters\TextFilter;
use MoonShine\Metrics\ValueMetric;
use MoonShine\Models\MoonshineUser;

class OrderResource extends Resource
{
   public static string $model = Order::class;
   public static string $title = 'Заказы';
   public string $titleField = 'name';
   public static int $itemsPerPage = 10;
   public static string $orderField = 'date';
   public static string $orderType = 'DESC';
   protected bool $createInModal = true;
   protected bool $editInModal = true;
   protected bool $showInModal = true;
   public static array $with = [
      'client',
      'payment',
      'user'
   ];
   public static array $activeActions = [
      'create',
      'edit',
      'delete'
   ];

   public function fields(): array
   {
      return [

         Grid::make([
            Column::make([
               Block::make('Основная информация', [
                  ID::make()->sortable()
                     ->hideOnIndex(),

                  BelongsTo::make('Клиент', 'client', function ($item) {
                     $contacts = Contact::where('client_id', $item->id)->first();
                     return $item->id . ' | ' . $contacts->tel;
                  })
                     ->valuesQuery(fn (Builder $query) => $query->where('status', '=', 1))
                     ->searchable()
                     ->sortable()
                     ->required()
                     ->hideOnIndex()
                     ->showOnExport(),

                  BelongsTo::make('Клиент', 'client', 'id')->hideOnDetail()
                     ->hideOnForm()
                     ->showOnExport(),

                  BelongsTo::make('Телефон', 'client', resource: function ($item) {
                     $contacts = Contact::where('client_id', $item->id)->first();
                     return $contacts->tel;
                  })->hideOnDetail()
                     ->hideOnForm()
                     ->showOnExport(),

                  BelongsTo::make('Адрес', 'client', resource: function ($item) {
                     $contacts = Contact::where('client_id', $item->id)->first();
                     return $contacts->address;
                  })
                     ->hideOnDetail()
                     ->hideOnForm()
                     ->showOnExport(),

                  BelongsTo::make('Экспедитор', 'user', 'name')
                     ->valuesQuery(fn (Builder $query) => $query->where('moonshine_user_role_id', 3))
                     ->searchable()
                     ->nullable()
                     ->sortable()
                     ->showOnExport(),

                  BelongsTo::make('Метод оплаты', 'payment')
                     ->searchable()
                     ->sortable()
                     ->nullable(),

               ])
            ])->columnSpan(6),

            Column::make([
               Block::make('Дополнительная информация', [

                  Date::make('Предпологаемая дата отгрузки', 'date')
                     ->sortable()
                     ->format('d-m-Y')
                     ->default(now()->format('d-m-Y'))
                     ->required()
                     ->showOnExport(),

                  Number::make('Цена', 'price')
                     ->sortable()
                     ->default(16000)
                     ->expansion('сум')
                     ->required()
                     ->showOnExport(),

                  Flex::make([
                     Number::make('Отдано капсул', 'given_bottle')
                        ->min(1)
                        ->max(100)
                        ->sortable()
                        ->expansion('капсул')
                        ->required()
                        ->showOnExport(),

                     Number::make('Получено капсул', 'taken_bottle')
                        ->min(0)
                        ->max(100)
                        ->sortable()
                        ->expansion('капсул')
                        ->showOnExport(),

                     Text::make('Сумма', resource: function ($item) {
                        return $item->price * $item->given_bottle;
                     })
                        ->hideOnDetail()
                        ->hideOnForm()
                        ->showOnExport(),
                  ]),
               ]),
            ])->columnSpan(6),

            Column::make([
               Block::make([
                  Textarea::make('Примечания', 'description')
                     ->showOnExport(),
               ]),

               HasMany::make('Клиент', 'client', new ClientResource())
                  ->hideOnIndex()
                  ->resourceMode(),

            ])->columnSpan(12),
         ]),

         SwitchBoolean::make('Статус', 'status')
            ->sortable()
            ->required()
            ->default(1)
            ->hideOnDetail()
            ->hideOnForm()
            ->hideOnIndex(),

      ];
   }

   public function rules(Model $item): array
   {
      return [];
   }

   public function search(): array
   {
      return ['client_id'];
   }

   public function metrics(): array
   {

      global $orders;
      $orders = Order::query()->selectRaw('COUNT(id) as count');

      global $clients;
      $clients = Order::query()->selectRaw('COUNT(DISTINCT client_id) as count');

      global $given_bottle;
      $given_bottle = Order::query()->selectRaw('SUM(given_bottle) as sum');

      global $taken_bottle;
      $taken_bottle = Order::query()->selectRaw('SUM(taken_bottle) as sum');

      global $total;
      $total = Order::query()->selectRaw('SUM(given_bottle*price) as total');

      if (isset($_REQUEST['filters'])) {
         foreach ($_REQUEST['filters'] as $key => $data) {
            if (!empty($data)) {
               if (is_array($data) && !empty($data['from'])) {
                  $orders->whereBetween($key, $data);
                  $clients->whereBetween($key, $data);
                  $given_bottle->whereBetween($key, $data);
                  $taken_bottle->whereBetween($key, $data);
                  $total->whereBetween($key, $data);
               } else if (is_string($data)) {
                  $orders->where($key, $data);
                  $clients->where($key, $data);
                  $given_bottle->where($key, $data);
                  $taken_bottle->where($key, $data);
                  $total->where($key, $data);
               }
            }
         }
      }

      return [
         ValueMetric::make('Заказы')
            ->value($orders->first()->count ?? 0)
            ->columnSpan(2),

         ValueMetric::make('Клиенты')
            ->value($clients->first()->count ?? 0)
            ->columnSpan(2),

         ValueMetric::make('Всего капсул продано')
            ->value($given_bottle->first()->sum ?? 0)
            ->columnSpan(2),

         ValueMetric::make('Всего капсул получено обратно')
            ->value($taken_bottle->first()->sum ?? 0)
            ->progress($given_bottle->first()->sum ?? 1)
            ->columnSpan(2),

         ValueMetric::make('Общая сумма')
            ->value($total->first()->total ?? 0)
            ->columnSpan(4),
      ];
   }

   public function filters(): array
   {
      return [

         DateRangeFilter::make('Дата отгрузки', 'date'),

         BelongsToFilter::make('Клиент', 'client', function ($item) {
            $contacts = Contact::where('client_id', $item->id)->first();
            return $item->id . ' | ' . $contacts->tel;
         })
            ->searchable()
            ->nullable(),

         BelongsToFilter::make('Экспедитор', 'user', resource: 'name')
            ->searchable()
            ->nullable(),
      ];
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

   public function bulkActions(): array
   {
      $forwarders = MoonshineUser::where('moonshine_user_role_id', 3)->get();
      foreach ($forwarders as $forwarder) {
         $return[] = BulkAction::make($forwarder->name, function (Model $item) use ($forwarder) {
            $item->update(['user_id' => $forwarder->id]);
         }, "Экспедитор $forwarder->name назвачен")
            ->showInDropdown();
      }

      return $return;
   }
}
