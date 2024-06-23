<?php

namespace App\Repositories\Base;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BaseRepository
{
    public static function logInfo($info, $id = null): void
    {
        Log::info('INFO: ' . $info.' | ID: '.$id);
    }

    public static function logError($e, $id = null): void
    {
        Log::error('ERROR: Line ' . $e->getLine() . ' of ' . $e->getFile() . ', ' . $e->getMessage() . ' | ID: ' . ($id ?? ''));
    }

    public static function tryCatchException($e, $id = null, $message = null): JsonResponse
    {
        Log::error('SERVER: Line ' . $e->getLine() . ' of ' . $e->getFile() . ', ' . $e->getMessage() . ' | ID: ' . $id);

        return response()->json([
            'success' => false,
            'message' => $message,
            'server_error' => "Line ".$e->getLine()." of ".$e->getFile().", ".$e->getMessage(),
        ], 500);
    }
}
