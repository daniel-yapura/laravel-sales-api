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
        ->assertJsonFragment(['name' => 'Bebidas']);

    $this->assertDatabaseHas('category', ['name' => 'Bebidas']);
});

test('No crea categoría duplicada', function () {
    Category::factory()->create(['name' => 'Bebidas']);

    $response = $this->postJson(route('category.store'), [
        'name' => 'Bebidas',
    ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['name']);
});

test('Actualiza una categoría', function () {
    $category = Category::factory()->create(['name' => 'Vieja']);

    $response = $this->putJson(route('category.update', $category), [
        'name' => 'Nueva',
    ]);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['name' => 'Nueva']);

    $this->assertDatabaseHas('category', ['id' => $category->id, 'name' => 'Nueva']);
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

test('Restaura una categoría eliminada', function () {
    $category = Category::factory()->create(['name' => 'Restaurar']);
    $category->delete();

    $response = $this->postJson("/api/category/{$category->id}/restore");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['name' => 'Restaurar']);

    $this->assertDatabaseHas('category', [
        'id' => $category->id,
        'deleted_at' => null,
    ]);
});

test('No restaura si existe categoría activa con mismo nombre', function () {
    $active = Category::factory()->create(['name' => 'Duplicada']);
    $deleted = Category::factory()->create(['name' => 'Duplicada']);
    $deleted->delete();

    $response = $this->postJson("/api/category/{$deleted->id}/restore");

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});
