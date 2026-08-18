<?php
namespace App\Core;

use App\Models\Product;

/**
 * The visitor's quote list - the equivalent of a cart, but it carries no
 * pricing commitment. Stored in the session as:
 *
 *   [ "<productId>|<variant>" => ['product_id'=>int,'variant'=>string,'quantity'=>int] ]
 */
class QuoteList
{
    private const KEY = '_quote_list';
    private const MAX_ITEMS = 60;
    private const MAX_QTY   = 999;

    private static function items(): array
    {
        $items = Session::get(self::KEY, []);
        return is_array($items) ? $items : [];
    }

    private static function save(array $items): void
    {
        Session::set(self::KEY, $items);
    }

    private static function key(int $productId, string $variant): string
    {
        return $productId . '|' . $variant;
    }

    public static function add(int $productId, int $quantity = 1, string $variant = ''): bool
    {
        $quantity = max(1, min(self::MAX_QTY, $quantity));
        $variant  = mb_substr(trim($variant), 0, 160);
        $items    = self::items();
        $key      = self::key($productId, $variant);

        if (!isset($items[$key]) && count($items) >= self::MAX_ITEMS) {
            return false;
        }

        if (isset($items[$key])) {
            $items[$key]['quantity'] = min(self::MAX_QTY, $items[$key]['quantity'] + $quantity);
        } else {
            $items[$key] = [
                'product_id' => $productId,
                'variant'    => $variant,
                'quantity'   => $quantity,
            ];
        }

        self::save($items);
        return true;
    }

    public static function update(string $key, int $quantity): void
    {
        $items = self::items();

        if (!isset($items[$key])) {
            return;
        }

        if ($quantity < 1) {
            unset($items[$key]);
        } else {
            $items[$key]['quantity'] = min(self::MAX_QTY, $quantity);
        }

        self::save($items);
    }

    public static function remove(string $key): void
    {
        $items = self::items();
        unset($items[$key]);
        self::save($items);
    }

    public static function clear(): void
    {
        Session::forget(self::KEY);
    }

    /** Total number of units on the list (used for the header badge). */
    public static function count(): int
    {
        return array_sum(array_column(self::items(), 'quantity'));
    }

    public static function isEmpty(): bool
    {
        return self::items() === [];
    }

    /**
     * Hydrate the list against the products table. Items whose product has
     * been deleted or deactivated are dropped silently.
     *
     * @return array<int, array{key:string,product:array,variant:string,quantity:int}>
     */
    public static function detailed(): array
    {
        $items = self::items();
        if ($items === []) {
            return [];
        }

        $ids      = array_values(array_unique(array_column($items, 'product_id')));
        $products = (new Product())->findManyActive($ids);

        $detailed = [];
        $pruned   = $items;

        foreach ($items as $key => $item) {
            $product = $products[$item['product_id']] ?? null;

            if ($product === null) {
                unset($pruned[$key]);
                continue;
            }

            $detailed[] = [
                'key'      => $key,
                'product'  => $product,
                'variant'  => $item['variant'],
                'quantity' => (int) $item['quantity'],
            ];
        }

        if (count($pruned) !== count($items)) {
            self::save($pruned);
        }

        return $detailed;
    }

    /**
     * True when an item can be bought outright rather than quoted: the shop
     * is switched on, the product carries a visible price, and it is flagged
     * buyable and in stock.
     */
    public static function isBuyable(array $product): bool
    {
        if (setting('enable_shop', '1') !== '1') {
            return false;
        }

        return (int) ($product['buyable'] ?? 0) === 1
            && (int) ($product['price_visible'] ?? 0) === 1
            && $product['price'] !== null
            && (float) $product['price'] > 0
            && ($product['stock_status'] ?? '') !== 'out_of_stock';
    }

    /**
     * Split the list into what can be paid for now and what needs a quote.
     *
     * @return array{buyable:array, quote:array, subtotal:float, units:int}
     */
    public static function split(): array
    {
        $buyable  = [];
        $quote    = [];
        $subtotal = 0.0;
        $units    = 0;

        foreach (self::detailed() as $item) {
            $units += $item['quantity'];

            if (self::isBuyable($item['product'])) {
                $item['line_total'] = (float) $item['product']['price'] * $item['quantity'];
                $subtotal          += $item['line_total'];
                $buyable[]          = $item;
            } else {
                $quote[] = $item;
            }
        }

        return [
            'buyable'  => $buyable,
            'quote'    => $quote,
            'subtotal' => round($subtotal, 2),
            'units'    => $units,
        ];
    }

    /** Remove the purchasable lines once an order has been placed. */
    public static function removeBuyable(): void
    {
        $split = self::split();
        $items = self::items();

        foreach ($split['buyable'] as $item) {
            unset($items[$item['key']]);
        }

        self::save($items);
    }
}
