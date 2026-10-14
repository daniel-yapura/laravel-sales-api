<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(Authenticate::class);
});

test('Lista categorías activas', function () {
    Category::factory()->count(3)->create();
    $deleted = Category::factory()->create();
    $deleted->delete();

    $response = $this->getJson(route('category.index'));

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(3);
});

test('Crea una categoría', function () {
    $response = $this->postJson(route('category.store'), [
        'name' => 'Bebidas',
    ]);

    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJsonFragment(['name' => 'Bebidas', 'slug' => 'bebidas']);

    $this->assertDatabaseHas('category', ['name' => 'Bebidas', 'slug' => 'bebidas']);
});

test('No crea categoría duplicada por slug', function () {
    Category::factory()->create(['name' => 'Bebidas', 'slug' => 'bebidas']);

    $response = $this->postJson(route('category.store'), [
        'name' => 'BEBIDAS',
    ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['name']);
});

test('Actualiza una categoría y recalcula slug', function () {
    $category = Category::factory()->create(['name' => 'Vieja', 'slug' => 'vieja']);

    $response = $this->putJson(route('category.update', $category), [
        'name' => 'Nueva categoria',
    ]);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['name' => 'Nueva categoria', 'slug' => 'nueva-categoria']);

    $this->assertDatabaseHas('category', ['id' => $category->id, 'slug' => 'nueva-categoria']);
});

test('No actualiza con slug duplicado', function () {
    Category::factory()->create(['name' => 'Existente', 'slug' => 'existente']);
    $category = Category::factory()->create(['name' => 'Otra', 'slug' => 'otra']);

    $response = $this->putJson(route('category.update', $category), [
        'name' => 'EXISTENTE',
    ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

test('Elimina una categoría con soft delete', function () {
    $category = Category::factory()->create();

    $response = $this->deleteJson(route('category.destroy', $category));

    $response->assertStatus(Response::HTTP_OK);
    $this->assertSoftDeleted('category', ['id' => $category->id]);
});

test('No muestra categoría eliminada en index', function () {
    $category = Category::factory()->create();
    $category->delete();

    $response = $this->getJson(route('category.index'));

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonMissing(['id' => $category->id]);
});

test('Un producto conserva category_id tras soft delete de categoría', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);

    $category->delete();

    $this->assertDatabaseHas('product', [
        'id' => $product->id,
        'category_id' => $category->id,
    ]);
});

test('Reactiva automáticamente una categoría eliminada por slug', function () {
    $category = Category::factory()->create(['name' => 'Restaurar', 'slug' => 'restaurar']);
    $category->delete();

    $response = $this->postJson(route('category.store'), [
        'name' => 'RESTAURAR',
    ]);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['id' => $category->id, 'slug' => 'restaurar']);

    $this->assertDatabaseHas('category', [
        'id' => $category->id,
        'deleted_at' => null,
        'slug' => 'restaurar',
    ]);
});
