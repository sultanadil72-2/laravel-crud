<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ModelServiceException extends Exception
{
    private $previous;

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $this->previous = $previous;

        // QueryException may return string sql error code, which is not valid for Exception class
        if ($previous instanceof QueryException) {
            $code = 0; // default code in case of QueryException
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param Request $request
     * @return Response
     * @throws \JsonException
     */
    public function render(Request $request)
    {
        $e = $this->previous;

        if ($e instanceof QueryException) {
            $message = $e->errorInfo[2];
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

            if ($e->errorInfo[1] === 1062) {
                $message = explode(" for ", $message)[0];
                $statusCode = Response::HTTP_CONFLICT;
            }

            $response = [
                'message' => $message,
            ];

            return response($response, $statusCode);
        }

        $response = [
            'message' => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
        ];

        return response($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
