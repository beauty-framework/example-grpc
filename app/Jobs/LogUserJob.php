<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Services\Auth\Enums\UserActionEnum;
use Beauty\Jobs\AbstractJob;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

class LogUserJob extends AbstractJob
{
    /**
     * @param UserActionEnum $action
     * @param int $userId
     * @param string $email
     */
    public function __construct(
        private UserActionEnum $action,
        private int $userId,
        private string $email,
    )
    {
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(): void
    {
        $logger = $this->container->get(LoggerInterface::class);

        $logger->info("User {$this->action->value}", [
            'id' => $this->userId,
            'email' => $this->email,
            'timestamp' => time(),
        ]);
    }
}
