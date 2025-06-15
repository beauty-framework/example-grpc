<?php
declare(strict_types=1);

namespace App\Controllers\GRPC;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Entities\UserToken;
use App\Services\Auth\AuthService;
use App\Services\Auth\AuthValidator;
use Beauty\GRPC\GrpcService;
use GRPC\Auth\AuthInterface;
use GRPC\Auth\AuthReply;
use GRPC\Auth\LoginRequest;
use GRPC\Auth\LogoutReply;
use GRPC\Auth\RegisterRequest;
use GRPC\Auth\ValidateReply;
use GRPC\Auth\ValidateRequest;
use Spiral\RoadRunner\GRPC;
use Spiral\RoadRunner\GRPC\ContextInterface;

#[GrpcService(AuthInterface::class)]
final class Auth implements AuthInterface
{
    /**
     * @param AuthService $authService
     * @param AuthValidator $authValidator
     */
    public function __construct(
        private AuthService   $authService,
        private AuthValidator $authValidator,
    )
    {
    }

    /**
     * @param GRPC\ContextInterface $ctx
     * @param LoginRequest $in
     * @return AuthReply
     * @throws \Random\RandomException
     */
    public function Login(GRPC\ContextInterface $ctx, LoginRequest $in): AuthReply
    {
        $this->authValidator->validateLogin($in);

        $dto = new LoginDTO(
            $in->getEmail(),
            $in->getPassword(),
        );

        $user = $this->authService->login($dto);

        return $this->authReplyHydrate($user);
    }

    /**
     * @param GRPC\ContextInterface $ctx
     * @param RegisterRequest $in
     * @return AuthReply
     */
    public function Register(GRPC\ContextInterface $ctx, RegisterRequest $in): AuthReply
    {
        $this->authValidator->validateRegister($in);

        $dto = new RegisterDTO(
            $in->getName(),
            $in->getEmail(),
            $in->getPassword(),
        );

        $userToken = $this->authService->register($dto);

        return $this->authReplyHydrate($userToken);
    }

    /**
     * @param GRPC\ContextInterface $ctx
     * @param ValidateRequest $in
     * @return ValidateReply
     */
    public function Validate(GRPC\ContextInterface $ctx, ValidateRequest $in): ValidateReply
    {
        $this->authValidator->validateToken($in);

        $userToken = $this->authService->validateToken($in->getToken());

        return new ValidateReply([
            'valid' => true,
            'name' => $userToken->getUser()->getName(),
            'email' => $userToken->getUser()->getEmail(),
        ]);
    }

    /**
     * @param ContextInterface $ctx
     * @param ValidateRequest $in
     * @return LogoutReply
     */
    public function Logout(GRPC\ContextInterface $ctx, ValidateRequest $in): LogoutReply
    {
        $this->authValidator->validateToken($in);

        $isSuccess = $this->authService->logout($in->getToken());

        return new LogoutReply([
            'success' => $isSuccess,
        ]);
    }

    /**
     * @param UserToken $user
     * @return AuthReply
     */
    private function authReplyHydrate(UserToken $user): AuthReply
    {
        return new AuthReply([
            'token' => $user->getToken(),
            'name' => $user->getUser()->getName(),
            'email' => $user->getUser()->getEmail(),
        ]);
    }
}