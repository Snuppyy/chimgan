<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Payment;
use App\MoonShine\Resources\ClientResource;
use App\MoonShine\Resources\ContactResource;
use App\MoonShine\Resources\OrderResource;
use App\MoonShine\Resources\PaymentResource;
use Illuminate\Support\ServiceProvider;
use MoonShine\MoonShine;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Resources\MoonShineUserResource;
use MoonShine\Resources\MoonShineUserRoleResource;

class MoonShineServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    app(MoonShine::class)->menu([
      MenuGroup::make('Упревление заказами', [
        MenuItem::make('Заказы', new OrderResource())
          ->icon('heroicons.shopping-bag')
          ->badge(fn () => Order::count()),
        MenuItem::make('Клиенты', new ClientResource())
          ->icon('heroicons.users')
          ->badge(fn () => Client::count()),
        MenuItem::make('Способы оплаты', new PaymentResource())
          ->icon('heroicons.credit-card')
          ->badge(fn () => Payment::count()),
      ])->icon('heroicons.briefcase'),

      MenuGroup::make('Настройки', [
        MenuItem::make('Пользователи', new MoonShineUserResource())
          ->icon('users'),
        MenuItem::make('Роли', new MoonShineUserRoleResource())
          ->icon('bookmark'),
      ])->icon('heroicons.wrench-screwdriver'),
    ]);
  }
}
