<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   title="Employee Tracking API",
 *   version="1.0.0",
 *   description="Dokumentasi API untuk demo tracking lokasi karyawan (Laravel + Sanctum)."
 * )
 *
 * @OA\Server(
 *   url=L5_SWAGGER_CONST_HOST,
 *   description="Server utama"
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="Token"
 * )
 */

abstract class Controller
{
    //
}
