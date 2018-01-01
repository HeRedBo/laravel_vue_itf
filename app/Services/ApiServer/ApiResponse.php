<?php
namespace App\Services\ApiServer;

use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use App\Support\Response as BaseResponse;
use League\Fractal\Manager;
use App\Support\Transform;


class ApiResponse
{
    /**
     * HTTP Response.
     *
     * @var \Illuminate\Contracts\Routing\ResponseFactory|HttpResponse
     */
    protected  $response;

    /**
     * API transformer helper.
     * @var Transform
     */
    protected  $transform;

    /**
     * HTTP status code.
     *
     * @var int
     */
    private $statusCode = HttpResponse::HTTP_OK;


    protected  $responseCode = HttpResponse::HTTP_OK;
    protected  $responseMessage = 'OK';
    protected  $responseData = [];
    protected  $responseObj = NUll;

    /**
     * ApiResponse constructor.
     */
    public function  __construct()
    {
        $this->response = response();
        $manager = new Manager();
        $this->transform =  new Transform($manager);
    }

    /**
     * Return a 201 response with the given created resource.
     *
     * @param null $resource
     * @param TransformerAbstract|null $transformer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function withCreated($message = "Created", $resource = null, TransformerAbstract $transformer = null)
    {
        $this->statusCode = HttpResponse::HTTP_CREATED;
        if (is_null($resource)) {
            return $this->withResult($message);
            //return $this->json();
        }

        return $this->item($resource, $transformer);
    }

    public function withSuccess($message = "Ok")
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_OK
        )->withResult($message);
    }
    /**
     * Make a 400 'Bad Request' response.
     *
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function withBadRequest($message = 'Bad Request')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_BAD_REQUEST
        )->withError($message);
    }

    /**
     * Make a 401 'Unauthorized' response.
     *
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function withUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_UNAUTHORIZED
        )->withError($message);
    }


    public function  withUnprocessableEntity($message = 'Unprocessable Entity')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_UNPROCESSABLE_ENTITY
        )->withError($message);
    }


    /**
     * Make a 403 'Forbidden' response.
     *
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function withForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_FORBIDDEN
        )->withError($message);
    }

    /**
     * Make a 404 'Not Found' response.
     *
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function withNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_NOT_FOUND
        )->withError($message);
    }

    /**
     * Make a 429 'Too Many Requests' response.
     *
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function withTooManyRequests($message = 'Too Many Requests')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_TOO_MANY_REQUESTS
        )->withError($message);
    }

    /**
     * Make a 500 'Internal Server Error' response.
     *
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function withInternalServer($message = 'Internal Server Error')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_INTERNAL_SERVER_ERROR
        )->withError($message);
    }

    /**
     * Make a error response
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public  function  withError($message)
    {
        $response = $this->setResponseMessage($message)->getResponseObj();
        return $this->json($response);
    }

    /**
     * Make a response
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function  withResult($message)
    {
        $response = $this->setResponseMessage($message)->getResponseObj();
        return $this->json($response);
    }

    public  function  withData(array $data)
    {
        $response = $this->setResponseData($data)->getResponseObj();
        return $this->json($response);
    }
    /**
     * Make a JSON response
     * @param $items
     * @param TransformerAbstract|null $transformer
     * @return \Illuminate\Http\JsonResponse
     */
    public function  collection($items , TransformerAbstract $transformer = null)
    {
        return $this->json(
            $this->transform->collection($items, $transformer)
        );
    }

    /**
     * Make a json response with the transform items
     * @param $item
     * @param TransformerAbstract|null $transformer
     * @return \Illuminate\Http\JsonResponse
     */
    public  function item($item , TransformerAbstract $transformer = null)
    {
        return $this->json(
            $this->transform->item($item, $transformer)
        );
    }

    /**
     * Make a json response
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function json($data = [], array $headers = [])
    {
        return $this->response->json($data, $this->statusCode, $headers);
    }


    public  function  getResponseObj()
    {
        $this->responseObj = new \stdClass();
        $this->responseObj->code = $this->getStatusCode();
        $this->responseObj->message = $this->getResponseMessage();
        $this->responseObj->data = $this->getResponseData();
        return $this->responseObj;
    }

    protected  function setResponseCode($code)
    {
        $this->responseCode = $code;
        return $this;
    }

    protected  function getResponseCode()
    {
        return $this->responseCode;
    }

    protected function setResponseMessage($responseMessage)
    {
        $this->responseMessage = $responseMessage;
        return $this;
    }

    public function  getResponseMessage()
    {
        return $this->responseMessage;
    }

    public function  setResponseData($data = [])
    {
        $this->responseData = $data;
        return $this;
    }

    public function  getResponseData()
    {
        return $this->responseData;
    }
    /**
     * set Http status code
     *
     * @param $statusCode
     *
     * @return $this
     */
    public function  setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        $this->responseCode = $statusCode;
        return $this;
    }

    /**
     * Gets the http status code
     *
     * @return int
     */
    public function  getStatusCode()
    {
        return $this->statusCode;
    }












}