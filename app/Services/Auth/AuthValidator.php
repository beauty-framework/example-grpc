<?php
declare(strict_types=1);

namespace App\Services\Auth;

use Beauty\Validation\Validation;
use Beauty\Validation\Validator;
use GRPC\Auth\LoginRequest;
use GRPC\Auth\RegisterRequest;
use GRPC\Auth\ValidateRequest;
use Spiral\RoadRunner\GRPC\Exception\GRPCException;
use Spiral\RoadRunner\GRPC\StatusCode;

class AuthValidator
{
    /**
     * @param Validator $validator
     */
    public function __construct(
        protected Validator $validator,
    )
    {
    }

    /**
     * @param LoginRequest $request
     * @return void
     */
    public function validateLogin(LoginRequest $request): void
    {
        $validation = $this->validator->validate([
            'email' => $request->getEmail(),
            'password' => $request->getPassword(),
        ], [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $this->throwValidation($validation);
    }

    /**
     * @param RegisterRequest $request
     * @return void
     */
    public function validateRegister(RegisterRequest $request): void
    {
        $validation = $this->validator->validate([
            'name' => $request->getName(),
            'email' => $request->getEmail(),
            'password' => $request->getPassword(),
        ], [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $this->throwValidation($validation);
    }

    /**
     * @param ValidateRequest $request
     * @return void
     */
    public function validateToken(ValidateRequest $request): void
    {
        $validation = $this->validator->validate([
            'token' => $request->getToken(),
        ], [
            'token' => ['required'],
        ]);

        $this->throwValidation($validation);
    }

    /**
     * @param Validation $validation
     * @return void
     */
    private function throwValidation(Validation $validation): void
    {
        if ($validation->fails()) {
            throw GRPCException::create(implode(', ', $validation->errors()->all()), StatusCode::INVALID_ARGUMENT);
        }
    }
}