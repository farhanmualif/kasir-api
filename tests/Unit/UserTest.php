<?php

namespace Tests\Unit;

use App\Models\User;
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

        $user = new User();
        $this->userRepository = new UserRepositoryImpl($user);
    }

    public function test_find_user_by_id(): void
    {
        $findUser = $this->userRepository->findById(1);
        $this->assertTrue($findUser);
    }
}
