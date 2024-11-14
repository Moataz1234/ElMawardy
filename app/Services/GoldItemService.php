<?php
namespace App\Services;

use App\Models\GoldItem;
use App\Models\GoldPrice;
use App\Models\Shop;
// use App\Models\ModelCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Services\SortAndFilterService;

class GoldItemService
{
    private PriceCalculator $priceCalculator;

    protected $sortAndFilterService;

    public function __construct(PriceCalculator $priceCalculator,SortAndFilterService $sortAndFilterService)
    {
        $this->priceCalculator = $priceCalculator;
        $this->sortAndFilterService = $sortAndFilterService;

    }


        public function getShopItems(Request $request): array
        {
            $query = $this->buildItemsQuery($request);
            $prices = $this->getPrices();

            // $goldItems = $query->paginate(20);
            $allowedFilters = [
                'search',
                'metal_purity',
                'gold_color',
                'kind',
            ];
    
            $goldItems = $this->sortAndFilterService->getFilteredAndSortedResults(
                $query,
                $request,
                $allowedFilters
            );
            $gold_color = $query->distinct('gold_color')->pluck('gold_color')->toArray();
            $kind = $query->distinct('kind')->pluck('kind')->toArray();
        
            return [
                'goldItems' => $goldItems,
                'latestPrices' => $prices['latest'],
                'latestGoldPrice' => $prices['goldPrice'],
                'totalPages' => $goldItems->lastPage() ,
                'gold_color' => $gold_color,
                'kind' => $kind,
            ];
        }

    public function getEditFormData(string $id): array
    {
        return [
            'goldItem' => GoldItem::findOrFail($id),
            'shops' => Shop::all()
        ];
    }
    private function buildItemsQuery(Request $request): Builder
    {
        return GoldItem::where('shop_name', Auth::user()->shop_name)
            ->with(['modelCategory.categoryPrice']);
    }

    private function getPrices(): array
    {
        $latestGoldPrice = GoldPrice::latest()->first();
        
        return [
            'latest' => GoldPrice::latest()->take(1)->get(),
            'goldPrice' => $latestGoldPrice
        ];
    }
    public function updateItem(string $id, array $data): GoldItem
    {
        $goldItem = GoldItem::findOrFail($id);
        $goldItem->update($data);
        return $goldItem;
    }

    public function deleteItem(string $id): void
    {
        $goldItem = GoldItem::findOrFail($id);
        $goldItem->delete();
    }

    public function getItemsByIds(array $ids): Collection
    {
        return GoldItem::whereIn('id', $ids)->get();
    }

    public function searchItems(string $query): Collection
    {
        return GoldItem::where('serial_number', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get();
    }

    public function getItemDetails(string $id): GoldItem
    {
        return GoldItem::with(['modelCategory', 'shop'])->findOrFail($id);
    }

    public function validateSerialNumber(string $serialNumber): bool
    {
        return GoldItem::where('serial_number', $serialNumber)->exists();
    }

    public function getItemsByShop(string $shopId): Collection
    {
        return GoldItem::where('shop_id', $shopId)
            ->with(['modelCategory'])
            ->get();
    }

    public function calculateTotalValue(Collection $items, float $goldPrice): float
    {
        return $items->sum(function ($item) use ($goldPrice) {
            return $this->priceCalculator->calculatePrice($item, $goldPrice);
        });
    }

    public function getItemStatistics(): array
    {
        return [
            'total_items' => GoldItem::count(),
            'total_weight' => GoldItem::sum('weight'),
            'items_by_category' => GoldItem::selectRaw('model_category, COUNT(*) as count')
                ->groupBy('model_category')
                ->get()
                ->pluck('count', 'model_category')
                ->toArray(),
            'items_by_shop' => GoldItem::selectRaw('shop_name, COUNT(*) as count')
                ->groupBy('shop_name')
                ->get()
                ->pluck('count', 'shop_name')
                ->toArray()
        ];
    }

    // public function filterItems(array $filters): Collection
    // {
    //     $query = GoldItem::query();

    //     if (isset($filters['metal_purity'])) {
    //         $query->whereIn('metal_purity', $filters['metal_purity']);
    //     }

    //     if (isset($filters['gold_color'])) {
    //         $query->whereIn('gold_color', $filters['gold_color']);
    //     }

    //     if (isset($filters['kind'])) {
    //         $query->whereIn('kind', $filters['kind']);
    //     }

    //     if (isset($filters['weight_min'])) {
    //         $query->where('weight', '>=', $filters['weight_min']);
    //     }

    //     if (isset($filters['weight_max'])) {
    //         $query->where('weight', '<=', $filters['weight_max']);
    //     }

    //     if (isset($filters['shop_id'])) {
    //         $query->where('shop_id', $filters['shop_id']);
    //     }

    //     return $query->get();
    // }

    public function bulkUpdate(array $ids, array $data): void
    {
        GoldItem::whereIn('id', $ids)->update($data);
    }

    public function getItemHistory(string $id): Collection
    {
        return GoldItem::find($id)
            ->history()
            ->orderBy('created_at', 'desc')
            ->get();
    }

}
class PriceCalculator
{
    public function calculatePrice(GoldItem $item, float $goldPrice): float
    {
        if (!$item->weight || !$goldPrice) {
            return 0;
        }

        $price = $item->weight * $goldPrice;

        return match($item->modelCategory?->category) {
            '**' => max(0, $price - (200 * $item->weight)),
            '***' => max(0, $price - (400 * $item->weight)),
            default => $price
        };
    }
}

 