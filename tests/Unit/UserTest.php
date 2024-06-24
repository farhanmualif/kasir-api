<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryImpl;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     */

    protected $userRepository;

    public function setUp(): void
    {
        $this->userRepository = new UserRepositoryImpl();
    }

    public function test_find_user_by_id(): void
    {
        $findUser = $this->userRepository->findById(1);
        $this->assertTrue($findUser);
    }
}
