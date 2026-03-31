<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function test_pack_price_uses_pack_value(): void
    {
        $product = new Product([
            'price_per_unit' => 4000,
            'price_per_pack' => 48000,
            'price_per_dozen' => 42000,
        ]);

        $this->assertSame(48000.0, $product->getPriceForUnit('pak'));
        $this->assertSame(42000.0, $product->getPriceForUnit('lusin'));
        $this->assertSame(4000.0, $product->getPriceForUnit('satuan'));
    }
}
