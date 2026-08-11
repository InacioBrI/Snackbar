<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DailyMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_date',
        'title',
        'notes',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'menu_date' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['is_sold_out', 'sort_order'])
            ->withTimestamps()
            ->orderBy('daily_menu_product.sort_order')
            ->orderBy('products.name');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForDate(Builder $query, CarbonInterface|string $date): Builder
    {
        return $query->whereDate('menu_date', $date);
    }

    public function availableProducts(): BelongsToMany
    {
        return $this->products()
            ->where('products.is_active', true)
            ->wherePivot('is_sold_out', false);
    }
}
