<?php

namespace App\Business\Entities;

class SaleEntity
{
    public ?int $id;

    public string $email;

    public string $sale_date;

    public float $total;

    public $concepts;

    public function __construct(?int $id, string $email, string $sale_date, array $concepts)
    {
        $this->id = $id;
        $this->email = $email;
        $this->sale_date = $sale_date;
        $this->concepts = $concepts;
        $this->total = $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->concepts as $concept) {
            $total += $concept->total;
        }

        return $total;
    }
}
