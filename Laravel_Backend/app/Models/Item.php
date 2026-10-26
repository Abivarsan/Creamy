<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'flavor_category',
        'price',
        'quantity',
        'unit',
        'description',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope query to search items by name or flavor category.
     *
     * @param Builder<Item> $query
     * @param string|null $term
     * @return Builder<Item>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('flavor_category', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope query to filter items by specific flavor category.
     *
     * @param Builder<Item> $query
     * @param string|null $category
     * @return Builder<Item>
     */
    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if (blank($category)) {
            return $query;
        }

        return $query->where('flavor_category', $category);
    }
}
