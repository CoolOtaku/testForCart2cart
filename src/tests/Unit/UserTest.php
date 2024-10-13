<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_user_has_name(): void
    {
        $user = User::factory()->create(['name' => 'John']);

        $this->assertEquals('John', $user->name);
    }
}
