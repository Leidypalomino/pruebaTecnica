<?php

namespace App\Http;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="E-commerce API",
 *         version="1.0.0",
 *         description="API for the Zenova Digital e-commerce system"
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8080/api",
 *         description="Local development server"
 *     ),
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             securityScheme="bearerAuth",
 *             type="http",
 *             scheme="bearer",
 *             bearerFormat="JWT"
 *         )
 *     )
 * )
 * **/