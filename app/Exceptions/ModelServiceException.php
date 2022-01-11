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
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

            if ($e->errorInfo[1] === 1062) {
                $message = explode(" for ", $message)[0];
                $statusCode = Response::HTTP_CONFLICT;
            }

            return response($message, $statusCode);
        }

        return response('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
