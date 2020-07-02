<?php

namespace App\Http\Controllers\Api;

use App\Enums\Constant;
use App\Http\Controllers\Controller;
use App\Enums\LogLevel;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Auth;

class ApiController extends Controller
{
    protected $defaultLimitResponse = Constant::DEFAULT_LIMIT_RESPONSE;

    /**
     * Get guard
     *
     * @param $guardName
     * @return void
     */
    protected function getGuard($guard = 'admin')
    {
        return Auth::guard($guard);
    }

    /**
     * @param $result
     * @param $message
     * @return JsonResponse
     */
    protected function sendSuccess($data = null, $message = null): JsonResponse
    {
        $response = [
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    protected function sendError($code = 5000, $statusCode = 500, $message = null): JsonResponse
    {
        $response = [
            'error' => [
                'code' => (int)$code,
                'message' => $message,
            ],
        ];

        return response()->json($response, $statusCode);
    }
}
