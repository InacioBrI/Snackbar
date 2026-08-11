<?php

namespace App\Services;

use App\Models\DailyMenu;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Resolves the published menu of the day ("cardápio do dia") once per request.
 */
class DailyMenuService
{
    private bool $resolved = false;

    private ?DailyMenu $menu = null;

    public function today(): ?DailyMenu
    {
        if ($this->resolved) {
            return $this->menu;
        }

        $this->resolved = true;

        if (! Schema::hasTable('daily_menus')) {
            return $this->menu = null;
        }

        $this->menu = DailyMenu::published()
            ->forDate(today())
            ->with(['products.category'])
            ->first();

        return $this->menu;
    }

    public function hasMenuToday(): bool
    {
        return $this->today() !== null;
    }

    /**
     * Active dishes offered today, in the order defined by the admin.
     *
     * @return Collection<int, Product>
     */
    public function productsToday(): Collection
    {
        $menu = $this->today();

        if (! $menu) {
            return new Collection;
        }

        return $menu->products->filter(fn (Product $product) => $product->is_active)->values();
    }

    /**
     * When a menu is published for today, only its dishes can be ordered.
     */
    public function isAvailableToday(Product $product): bool
    {
        $menu = $this->today();

        if (! $menu) {
            return true;
        }

        $entry = $menu->products->firstWhere('id', $product->id);

        return $entry !== null && ! $entry->pivot->is_sold_out;
    }

    public function forget(): void
    {
        $this->resolved = false;
        $this->menu = null;
    }
}
