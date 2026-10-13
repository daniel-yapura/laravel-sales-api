<?php

namespace App\Business\Entities;

class ConceptEntity
{
    public int $quantity;

    public float $price;

    public int $product_id;

    public float $total;

    public function __construct(int $quantity, float $price, int $product_id)
    {
        $this->quantity = $quantity;
        $this->price = $price;
        $this->product_id = $product_id;
        $this->total = $this->calculateTotal();
    }

    public function calculateTotal()
    {
        return $this->quantity * $this->price;
    }
}
