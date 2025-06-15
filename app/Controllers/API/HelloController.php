<?php
declare(strict_types=1);

namespace App\Controllers\API;

use Beauty\Core\Router\Route;
use Beauty\Http\Enums\HttpMethodsEnum;
use Beauty\Http\Request\HttpRequest;
use Beauty\Http\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

class HelloController
{
    #[Route(HttpMethodsEnum::GET, '/api/hello')]
    public function index(HttpRequest $request): ResponseInterface
    {
        return new JsonResponse(200, [
            'message' => 'Hello, world',
        ]);
    }


}