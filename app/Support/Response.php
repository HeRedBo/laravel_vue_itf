<?php

namespace  App\Support;

use Illuminate\Contracts\Routing\ResponseFactory;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Response
{
    /**
     * @var ResponseFactory
     */
    private $response;

    /**
     * API transform helper
     * @var Transform
     */
    public  $transform;

    /**
     * HTTP status code
     * @var int
     */
    private  $statusCode = HttpResponse::HTTP_OK;

    public function __construct(ResponseFactory $response, Transform $transform)
    {
        $this->response   = $response;
        $this->transform  = $transform;
    }

    /**
     * return a 201 response with give  created resource
     *
     * @param null $resource
     * @param TransformerAbstract|null $transformer
     * @return \Illuminate\Http\JsonResponse
     */
    public function withCreated($resource = NULL, TransformerAbstract $transformer = null)
    {
        $this->statusCode = HttpResponse::HTTP_CREATED;
        if(is_null($resource)) {
            return $this->json();
        }
        return $this->item($resource, $transformer);
    }

    /**
     * Make a 204 no Content response
     * @return \Illuminate\Http\JsonResponse
     */
    public function  withNoContent()
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_NO_CONTENT
        )->json();
    }

    /**
     * Make a 401 'Bad Request' response
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function  withBadRequest($message = "Bad Request")
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_BAD_REQUEST
        )->withError($message);
    }

    /**
     * Make a 401 'Unauthorized' response
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function withUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_UNAUTHORIZED
        )->withError($message);
    }

    /**
     * Make a 403 'Forbidden' response
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function withForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_FORBIDDEN
        )->withError($message);
    }

    /**
     * Make a 404 'Not Found' message
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function withNoFound($message = "Not found")
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_NOT_FOUND
        )->withError($message);
    }


    /**
     * Make a 429 'Too Many Request' response
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function  withTooManyRequest($message = 'Too Many Request')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_TOO_MANY_REQUESTS
        )->withError($message);
    }

    /**
     * Make a 500 'Internal Server Error'
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public  function withInternalServer($message = "Internal Server Error")
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_INTERNAL_SERVER_ERROR
        )->withError($message);
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
     * Make a error response
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public  function  withError($message)
    {
        return $this->json([
            'message' => is_array($message) ? $message : [$message]
        ]);
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