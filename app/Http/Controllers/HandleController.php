<?php namespace App\Http\Controllers;


use App\User;
use Illuminate\Support\Facades\Response;

class HandleController extends Controller
{
    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    //error 404 response not found
    public function responseNotFound($message = "Not Found")
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }


    //error 500 internal error
    public function internalError ($message = "Problem occurred locally")
    {
        return $this->setStatusCode(500)->respondWithError($message);
    }


    //error 422 unprocessable entity
    public function responseNotServed($message = 'Failed to serve request')
    {
        return $this->setStatusCode(422)->respondWithError($message);
    }


    public function respond($data, $headers = [])
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }



    public function respondWithError ($message = "Error")
    {
        return $this->respond([
            "error" => [
                "message" => $message,
                "status_code" => $this->getStatusCode()
            ]
        ]);
    }

}
