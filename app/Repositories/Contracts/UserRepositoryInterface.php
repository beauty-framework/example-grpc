<?php
declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\Auth\RegisterDTO;
use App\Entities\User;

interface UserRepositoryInterface
{
    /**
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): User|null;

    /**
     * @param RegisterDTO $dto
     * @return User
     */
    public function create(RegisterDTO $dto): User;
}