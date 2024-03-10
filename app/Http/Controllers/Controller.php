<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function findDataOrFail($model, $key, $errorMessage = 'Record not found')
    {
        try {
            return $model::findOrFail($key);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => 404,
                'message' => $errorMessage
            ], 404);
        }
    }

    protected function jsonResponse($status, $status_code, $message, $data = null, $errors = null)
    {
        $responseData = [
            'status' => $status,
            'status_code' => $status_code,
            'message' => $message,
        ];

        if ($data !== null) {
            $responseData['data'] = $data;
        }

        if ($errors !== null) {
            $responseData['errors'] = $errors;
        }

        return response()->json($responseData);
    }
}
