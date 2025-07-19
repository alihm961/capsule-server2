<?php

namespace App\Traits;

trait ApiResponse
{
    protected function responseJSON($data = null, $message = 'success', $status = 200)
    {
        return response()->json([
            'success' => $status === 200,
            'message' => $message,
            'data'    => $data
        ], $status);
    }
}