<?php

namespace Modules\Core\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, string $message = '', int $code = 200): JsonResponse
    {
        if (empty($message)) {
            $message = __('crm.success');
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    public static function error(string $message = '', int $code = 400, $errors = null): JsonResponse
    {
        if (empty($message)) {
            $message = __('crm.error');
        }

        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    public static function paginated($paginator, string $resourceClass, string $message = ''): JsonResponse
    {
        if (empty($message)) {
            $message = __('crm.success');
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $resourceClass::collection($paginator),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }
}
