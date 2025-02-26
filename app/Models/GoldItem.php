<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GoldItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'model',
        'serial_number',
        'kind',
        'shop_name',
        'shop_id',
        'weight',
        'gold_color',
        'metal_type',
        'metal_purity',
        'quantity',
        'stones',
        'talab',
        'status',
        'rest_since',
        'created_at',
        'updated_at',
    ];
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'shop_name', 'name');
    }

    public function transferRequests()
    {
        return $this->hasMany(TransferRequest::class);
    }
    public function outers()
    {
        return $this->hasMany(Outer::class, 'gold_serial_number', 'serial_number');
    }
    public function talabatCategory()
    {
        return $this->belongsTo(Talabat::class, 'talabat', 'model');
    }

    public function modelCategory()
    {
        return $this->belongsTo(Models::class, 'model', 'model');
    }

    public function modelDetails()
    {
        return $this->hasOne(GoldItemDetail::class, 'model', 'model');
    }

    // New methods for dashboard analysis

    public function sale_request()
    {
        return $this->hasOne(SaleRequest::class, 'item_serial_number', 'serial_number');
    }
    public function poundInventory()
    {
        return $this->hasOne(GoldPoundInventory::class, 'related_item_serial', 'serial_number');
    }

    public function scopeByKind($query, $kind)
    {
        return $query->where('kind', $kind);
    }

    public function scopeSoldItems($query)
    {
        return $query->where('status', 'sold');
    }

    public function scopeMostSold($query, $limit = 5)
    {
        return $query->soldItems()
            ->select('kind', DB::raw('COUNT(*) as total_sold'))
            ->groupBy('kind')
            ->orderByDesc('total_sold')
            ->limit($limit);
    }

    public function scopeSalesTrendByKind($query, $kind)
    {
        return $query->soldItems()
            ->byKind($kind)
            ->selectRaw('YEAR(updated_at) as year, MONTH(updated_at) as month, COUNT(*) as total_sold')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month');
    }

    public static function getShopStatistics()
    {
        return self::select(
            'shop_name',
            'kind',
            DB::raw('COUNT(*) as total_items'),
            DB::raw('SUM(weight) as total_weight')
        )
        ->whereNotIn('status', ['sold', 'deleted']) // Exclude sold and deleted items
        ->groupBy('shop_name', 'kind')
        ->orderBy('shop_name')
        ->orderBy('kind')
        ->get()
        ->groupBy('shop_name');
    }
}
