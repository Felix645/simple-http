<?php


namespace Neon\Http;


use Neon\Http\Exceptions\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as GuzzleResponseInterface;
use Psr\Http\Message\StreamInterface;


class Response implements ResponseInterface
{
    /**
     * Guzzle response instance.
     *
     * @var ResponseInterface
     */
    private $response;

    /**
     * Response body.
     *
     * @var string
     */
    private $body = '';

    /**
     * @var null|RequestInterface $last_request
     */
    private $last_request;

    /**
     * @var Http
     */
    private $original_request;

    /**
     * Response constructor.
     *
     * @param GuzzleResponseInterface $response
     * @param RequestInterface|null $last_request
     * @param Http $original_request
     */
    public function __construct(GuzzleResponseInterface $response, ?RequestInterface $last_request, Http $original_request)
    {
        $this->response = $response;
        $this->last_request = $last_request;
        $this->original_request = $original_request;
    }

    /**
     * Gets the raw guzzle response interface.
     *
     * @return GuzzleResponseInterface
     */
    public function getRawResponse() : GuzzleResponseInterface
    {
        return $this->response;
    }

    /**
     * Gets the last request
     *
     * @return null|RequestInterface
     */
    public function getLastRequest() : ?RequestInterface
    {
        return $this->last_request;
    }

    /**
     * Gets the original http request builder object.
     *
     * @return Http
     */
    public function getOriginalHttpBuilder() : Http
    {
        return $this->original_request;
    }

    /**
     * Gets the URL of the last redirect, if redirection tracking was enabled.
     *
     * @return string
     */
    public function getRedirectUrl() : string
    {
        if( empty($this->last_request) ) {
            return '';
        }

        return $this->last_request->getUri()->__toString();
    }

    /**
     * Checks if given header exists in the response.
     *
     * @param string $header
     *
     * @return bool
     */
    public function hasHeader(string $header) : bool
    {
        return $this->response->hasHeader($header);
    }

    /**
     * Gets the specified header.
     *
     * @param string $header
     *
     * @return string[]
     */
    public function getHeader(string $header) : array
    {
        return $this->response->getHeader($header);
    }

    /**
     * Gets the response body string.
     *
     * @return string
     */
    public function body() : string
    {
        $this->body = empty($this->body) ? $this->response->getBody()->getContents() : $this->body;

        return $this->body;
    }

    /**
     * Gets the Guzzle response instance.
     *
     * @return StreamInterface
     */
    public function bodyRaw() : StreamInterface
    {
        return $this->response->getBody();
    }

    /**
     * Gets the response body as an array converted from json.
     *
     * @throws RequestException
     *
     * @return array
     */
    public function json() : array
    {
        $body = empty($this->body) ? $this->body() : $this->body;

        $json = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RequestException('Fetched body is not a json string');
        }

        return $json;
    }

    /**
     * Gets the response status code.
     *
     * @return int
     */
    public function code() : int
    {
        return $this->response->getStatusCode();
    }

    /**
     * Checks if the request was successful.
     *
     * @param void
     *
     * @return bool
     */
    public function successfull() : bool
    {
        $success_codes = [200, 201, 204];
        return in_array($this->response->getStatusCode(), $success_codes);
    }

    /**
     * Checks if the request was not successful.
     *
     * @param void
     *
     * @return bool
     */
    public function failure() : bool
    {
        return $this->response->getStatusCode() >= 400;
    }

    /**
     * Checks for the 201 CREATED response code.
     *
     * @param void
     *
     * @return bool
     */
    public function created() : bool
    {
        return $this->response->getStatusCode() === 201;
    }

    /**
     * Checks for the 204 NO CONTENT response code.
     *
     * @param void
     *
     * @return bool
     */
    public function noContent() : bool
    {
        return $this->response->getStatusCode() === 204;
    }

    /**
     * Checks if the requested resource was not found.
     *
     * @param void
     *
     * @return bool
     */
    public function notFound() : bool
    {
        return $this->response->getStatusCode() === 404;
    }

    /**
     * Checks if the request was not authorized.
     *
     * @param void
     *
     * @return bool
     */
    public function unauthorized() : bool
    {
        return $this->response->getStatusCode() === 401;
    }

    /**
     * Checks if the request was forbidden.
     *
     * @param void
     *
     * @return bool
     */
    public function forbidden() : bool
    {
        return $this->response->getStatusCode() === 403;
    }

    /**
     * Checks if the request was a bad request.
     *
     * @param void
     *
     * @return bool
     */
    public function badRequest() : bool
    {
        return $this->response->getStatusCode() === 400;
    }

    /**
     * Checks for a server error.
     *
     * @param void
     *
     * @return bool
     */
    public function serverError() : bool
    {
        return $this->response->getStatusCode() >= 500;
    }

    /**
     * Checks for a client error.
     *
     * @param void
     *
     * @return bool
     */
    public function clientError() : bool
    {
        return $this->response->getStatusCode() >= 400 && $this->response->getStatusCode() < 500;
    }
}