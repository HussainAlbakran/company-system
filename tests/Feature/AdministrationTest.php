<?php

use App\Models\User;

test('admin can open administration hub and subpages', function () {
    $admin = User::factory()->internal('admin')->create();

    $this->actingAs($admin)
        ->get(route('administration.index', absolute: false))
        ->assertOk()
        ->assertSee('مركز الإدارة', false);

    $this->actingAs($admin)
        ->get(route('administration.assignments', absolute: false))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('administration.updates', absolute: false))
        ->assertOk();
});

test('non-admin cannot access administration routes', function () {
    $engineer = User::factory()->internal('engineer')->create();

    $this->actingAs($engineer)
        ->get(route('administration.index', absolute: false))
        ->assertForbidden();
});
