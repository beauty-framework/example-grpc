<?php
declare(strict_types=1);

namespace App\Services\Auth;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Entities\UserToken;
use App\Jobs\LogUserJob;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserTokenRepositoryInterface;
use App\Services\Auth\Enums\UserActionEnum;
use Beauty\Database\Connection\ConnectionInterface;
use Beauty\Database\Connection\Exceptions\QueryException;
use Beauty\Jobs\Dispatcher;
use Psr\Log\LoggerInterface;
use Random\RandomException;
use Spiral\RoadRunner\GRPC\Exception\GRPCException;
use Spiral\RoadRunner\GRPC\StatusCode;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

class AuthService
{
    /**
     * @param NativePasswordHasher $hasher
     * @param ConnectionInterface $connection
     * @param UserRepositoryInterface $userRepository
     * @param UserTokenRepositoryInterface $tokenRepository
     * @param LoggerInterface $logger
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        protected NativePasswordHasher $hasher,
        protected ConnectionInterface $connection,
        protected UserRepositoryInterface $userRepository,
        protected UserTokenRepositoryInterface $tokenRepository,
        protected LoggerInterface $logger,
        protected Dispatcher $dispatcher,
    )
    {
    }

    /**
     * @param LoginDTO $dto
     * @return UserToken
     * @throws RandomException
     */
    public function login(LoginDTO $dto): UserToken
    {
        try {
            $user = $this->userRepository->findByEmail($dto->email);

            if (!$user) {
                throw GRPCException::create('User not found', StatusCode::NOT_FOUND);
            }

            if (!$this->hasher->verify($user->getPassword(), $dto->password)) {
                throw GRPCException::create('Invalid credentials', StatusCode::INVALID_ARGUMENT);
            }

            $this->sendLoggerAction(UserActionEnum::Login, $user->getId(), $user->getEmail());

            return $this->tokenRepository->create($user->getId(), $this->generateToken());
        } catch (QueryException $exception) {
            $this->logger->error($exception->getMessage());
            throw GRPCException::create($exception->getMessage(), StatusCode::INTERNAL);
        }
    }

    /**
     * @param RegisterDTO $dto
     * @return UserToken
     */
    public function register(RegisterDTO $dto): UserToken
    {
        try {
            return $this->connection->transaction(function (ConnectionInterface $tx) use ($dto) {
                $dto->password = $this->hasher->hash($dto->password);

                $user = $this->userRepository->create($dto);

                $token = $this->generateToken();

                $userToken = $this->tokenRepository->create($user->getId(), $token);

                $this->sendLoggerAction(UserActionEnum::Register, $user->getId(), $user->getEmail());

                return $userToken;
            });
        } catch (QueryException $exception) {
            $this->logger->error($exception->getMessage());
            throw GRPCException::create($exception->getMessage(), StatusCode::INTERNAL);
        }
    }

    /**
     * @param string $token
     * @return UserToken
     */
    public function validateToken(string $token): UserToken
    {
        try {
            $user = $this->tokenRepository->findByToken($token);

            if (!$user) {
                throw GRPCException::create('Token not found', StatusCode::NOT_FOUND);
            }

            if ($user->isExpired()) {
                throw GRPCException::create('Token expired', StatusCode::UNAUTHENTICATED);
            }

            $this->sendLoggerAction(UserActionEnum::Validate, $user->getUserId(), $user->getUser()->getEmail());

            return $user;
        } catch (QueryException $exception) {
            $this->logger->error($exception->getMessage());
            throw GRPCException::create($exception->getMessage(), StatusCode::INTERNAL);
        }
    }

    /**
     * @param string $token
     * @return bool
     */
    public function logout(string $token): bool
    {
        try {
            $user = $this->tokenRepository->findByToken($token);

            if (!$user) {
                throw GRPCException::create('Token not found', StatusCode::NOT_FOUND);
            }

            $this->tokenRepository->delete($token);

            $this->sendLoggerAction(UserActionEnum::Logout, $user->getUserId(), $user->getUser()->getEmail());

            return true;
        } catch (QueryException $exception) {
            $this->logger->error($exception->getMessage());
            throw GRPCException::create($exception->getMessage(), StatusCode::INTERNAL);
        }
    }

    /**
     * @return string
     * @throws RandomException
     */
    protected function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * @param UserActionEnum $action
     * @param int $userId
     * @param string $email
     * @return void
     */
    protected function sendLoggerAction(UserActionEnum $action, int $userId, string $email): void
    {
        $this->dispatcher->dispatch(new LogUserJob($action, $userId, $email));
    }
}