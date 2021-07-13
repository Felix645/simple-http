<?php


namespace Neon\Http;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;


class Http
{
    /**
     * Base url string.
     *
     * @var null|string
     */
    private static $base_url = null;

    /**
     * GET request method string.
     *
     * @var string
     */
    private const GET = 'GET';

    /**
     * POST request method string.
     *
     * @var string
     */
    private const POST = 'POST';

    /**
     * PUT request method string.
     *
     * @var string
     */
    private const PUT = 'PUT';

    /**
     * PATCH request method string.
     *
     * @var string
     */
    private const PATCH = 'PATCH';

    /**
     * DELETE request method string.
     *
     * @var string
     */
    private const DELETE = 'DELETE';

    /**
     * Guzzle Http Client.
     *
     * @var Client
     */
    private $guzzle;

    /**
     * The request body.
     *
     * @var array
     */
    private $request_body = [];

    /**
     * Collection of appended files.
     *
     * @var File[]
     */
    private $files;

    /**
     * Identifier if the request has appended files.
     *
     * @var bool
     */
    private $has_files = false;

    /**
     * Collection of request headers.
     *
     * @var array
     */
    private $headers = [];

    /**
     * Http constructor.
     */
    public function __construct()
    {
        if( is_null(self::$base_url) ) {
            $this->guzzle = new Client();
        } else {
            $this->guzzle = new Client([
                'base_uri' => self::$base_url
            ]);
        }
    }

    /**
     * Performs a GET request.
     *
     * @param string $uri
     * @param array $form_data
     *
     * @throws GuzzleException
     *
     * @return Response
     */
    public function get(string $uri, array $form_data = []) : Response
    {
        return $this->buildRequest(self::GET, $uri, $form_data);
    }

    /**
     * Performs a POST request.
     *
     * @param string $uri
     * @param array $form_data
     *
     * @throws GuzzleException
     *
     * @return Response
     */
    public function post(string $uri, array $form_data = []) : Response
    {
        return $this->buildRequest(self::POST, $uri, $form_data);
    }

    /**
     * Performs a PUT request.
     *
     * @param string $uri
     * @param array $form_data
     *
     * @throws GuzzleException
     *
     * @return Response
     */
    public function put(string $uri, array $form_data = []) : Response
    {
        return $this->buildRequest(self::PUT, $uri, $form_data);
    }

    /**
     * Performs a PATCH request.
     *
     * @param string $uri
     * @param array $form_data
     *
     * @throws GuzzleException
     *
     * @return Response
     */
    public function patch(string $uri, array $form_data = []) : Response
    {
        return $this->buildRequest(self::PATCH, $uri, $form_data);
    }

    /**
     * Performs a DELETE request.
     *
     * @param string $uri
     * @param array $form_data
     *
     * @throws GuzzleException
     *
     * @return Response
     */
    public function delete(string $uri, array $form_data = []) : Response
    {
        return $this->buildRequest(self::DELETE, $uri, $form_data);
    }

    /**
     * Adds a header to the request.
     *
     * @param string $key
     * @param $value
     *
     * @return $this
     */
    public function addHeader(string $key, $value) : Http
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Adds a bearer token to the request.
     *
     * @param string $token
     *
     * @return $this
     */
    public function bearer(string $token) : Http
    {
        $this->addHeader('Authorization', 'Bearer ' . $token);
        return $this;
    }

    /**
     * Adds a form input to the request.
     *
     * @param string $key
     * @param $value
     *
     * @return $this
     */
    public function addParam(string $key, $value) : Http
    {
        $this->request_body[$key] = $value;

        return $this;
    }

    /**
     * Adds a file to the request.
     *
     * @param string $key
     * @param string $filename
     * @param string $file_path
     *
     * @return $this
     */
    public function file(string $key, string $filename, string $file_path) : Http
    {
        $this->has_files = true;

        $this->files[$key] = new File($filename, $file_path);

        return $this;
    }

    /**
     * Builds a request.
     *
     * @param string $method
     * @param string $uri
     * @param array $form_data
     *
     * @throws GuzzleException
     *
     * @return Response
     */
    private function buildRequest(string $method, string $uri, array $form_data = []) : Response
    {
        $this->request_body = $this->request_body ?? + $form_data;

        $options = $this->buildOptions();

        $response = $this->guzzle->request($method, $uri, $options);

        return $this->buildResponseObject($response);
    }

    /**
     * Builds a response object.
     *
     * @param ResponseInterface $response
     *
     * @return Response
     */
    private function buildResponseObject(ResponseInterface $response) : Response
    {
        return new Response($response);
    }

    /**
     * Builds the options array needed for the request.
     *
     * @return array
     */
    private function buildOptions() : array
    {
        $options = [];

        foreach( $this->request_body as $key => $value ) {
            if( RequestOptions::QUERY === $key ) {
                $options[RequestOptions::QUERY] = $value;
            }
        }

        if( !empty($this->request_body) && !$this->has_files ) {
            $options['form_params'] = $this->request_body;
        }

        if( !empty($this->request_body) && $this->has_files) {
            $options['multipart'] = [];

            $options = $this->multipartAddRequestBody($options);
            $options = $this->multipartAddFile($options);
        }

        if( empty($this->request_body) && $this->has_files ) {
            $options['multipart'] = [];

            $options = $this->multipartAddFile($options);
        }

        if( !empty($this->headers) ) {
            $options['headers'] = [];

            foreach($this->headers as $header => $value ) {
                $options['headers'][$header] = $value;
            }
        }

        return $options;
    }

    /**
     * Adds the request body as a multipart form.
     *
     * @param array $options
     *
     * @return array
     */
    private function multipartAddRequestBody(array $options) : array
    {
        foreach( $this->request_body as $key => $value ) {
            $options['multipart'][] = [
                'name' => $key,
                'contents' => $value
            ];
        }

        return $options;
    }

    /**
     * Adds the appended files as a multipart form.
     *
     * @param array $options
     *
     * @return array
     */
    private function multipartAddFile(array $options) : array
    {
        foreach( $this->files as $key => $file ) {
            if( $file instanceof File ) {
                $options['multipart'][] = [
                    'name' => $key,
                    'contents' => Utils::tryFopen($file->getFilePath(), 'r'),
                    'filename' => $file->getName()
                ];
            }
        }

        return $options;
    }

    /**
     * Sets a base url for new client instances.
     *
     * @param string $url
     *
     * @return void
     */
    public static function setBaseURL(string $url)
    {
        self::$base_url = $url;
    }
}