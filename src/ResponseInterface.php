<?php

namespace Neon\Http;

use Neon\Http\Exceptions\RequestException;
use Psr\Http\Message\ResponseInterface as GuzzleResponseInterface;
use Psr\Http\Message\StreamInterface;

interface ResponseInterface
{
    /**
     * Gets the raw guzzle response interface.
     *
     * @return GuzzleResponseInterface
     */
    public function getRawResponse() : GuzzleResponseInterface;

    /**
     * Gets the original http request builder object.
     *
     * @return Http
     */
    public function getOriginalHttpBuilder() : Http;

    /**
     * Gets the URL of the last redirect, if redirection tracking was enabled.
     *
     * @return string
     */
    public function getRedirectUrl() : string;

    /**
     * Checks if given header exists in the response.
     *
     * @param string $header
     *
     * @return bool
     */
    public function hasHeader(string $header) : bool;

    /**
     * Gets the specified header.
     *
     * @param string $header
     *
     * @return string[]
     */
    public function getHeader(string $header) : array;

    /**
     * Gets the response body string.
     *
     * @return string
     */
    public function body() : string;

    /**
     * Gets the Guzzle response instance.
     *
     * @return StreamInterface
     */
    public function bodyRaw() : StreamInterface;

    /**
     * Gets the response body as an array converted from json.
     *
     * @throws RequestException
     *
     * @return array
     */
    public function json() : array;

    /**
     * Gets the response status code.
     *
     * @return int
     */
    public function code() : int;

    /**
     * Checks if the request was successful.
     *
     * @param void
     *
     * @return bool
     */
    public function successfull() : bool;

    /**
     * Checks if the request was not successful.
     *
     * @param void
     *
     * @return bool
     */
    public function failure() : bool;

    /**
     * Checks for the 201 CREATED response code.
     *
     * @param void
     *
     * @return bool
     */
    public function created() : bool;

    /**
     * Checks for the 204 NO CONTENT response code.
     *
     * @param void
     *
     * @return bool
     */
    public function noContent() : bool;

    /**
     * Checks if the requested resource was not found.
     *
     * @param void
     *
     * @return bool
     */
    public function notFound() : bool;

    /**
     * Checks if the request was not authorized.
     *
     * @param void
     *
     * @return bool
     */
    public function unauthorized() : bool;

    /**
     * Checks if the request was forbidden.
     *
     * @param void
     *
     * @return bool
     */
    public function forbidden() : bool;

    /**
     * Checks if the request was a bad request.
     *
     * @param void
     *
     * @return bool
     */
    public function badRequest() : bool;

    /**
     * Checks for a server error.
     *
     * @param void
     *
     * @return bool
     */
    public function serverError() : bool;

    /**
     * Checks for a client error.
     *
     * @param void
     *
     * @return bool
     */
    public function clientError() : bool;
}