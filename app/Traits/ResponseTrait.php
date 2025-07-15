<?php

namespace App\Traits;

trait ResponseTrait
{
    public function success($message = 'Success', $data = [], $code = 200)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    public function error($message = 'Something went wrong', $errors = [], $code = 400)
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }

    public function unauthorized($message = 'Unauthorized')
    {
        return $this->error($message, [],401);
    }

    public function forbidden($message = 'Forbidden')
    {
        return $this->error($message, [], 403);
    }

    public function notFound($message = 'Resource not found')
    {
        return $this->error($message, [], 404);
    }

    public function validationError($message = 'Validation failed', $errors = [])
    {
        return $this->error($message, $errors, 422);
    }
}
