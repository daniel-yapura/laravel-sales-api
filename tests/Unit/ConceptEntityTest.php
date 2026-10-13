<?php

use App\Business\Entities\ConceptEntity;

test('Creación correcta', function () {
    $concept = new ConceptEntity(quantity: 3, price: 20.0, product_id: 1);

    expect($concept->quantity)->toBe(3)
        ->and($concept->price)->toBe(20.0)
        ->and($concept->product_id)->toBe(1)
        ->and($concept->total)->toBe(60.0);
});

test('Calcular total', function () {
    $concept = new ConceptEntity(quantity: 3, price: 15.0, product_id: 2);

    expect($concept->calculateTotal())->toBe(45.0);
});
