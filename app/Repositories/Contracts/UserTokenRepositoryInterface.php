<?php
declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Entities\UserToken;

interface UserTokenRepositoryInterface
{
    /**
     * @param string $token
     * @return UserToken|null
     */
    public function findByToken(string $token): UserToken|null;

    /**
     * @param int $userId
     * @param string $token
     * @return UserToken
     */
    public function create(int $userId, string $token): UserToken;

    /**
     * @param string $token
     * @return void
     */
    public function delete(string $token): void;
}