<?php


namespace Neon\Http\Facade;


use Neon\Http\Exceptions\FacadeException;


/**
 * Class Http
 * @package Neon\Http\Facade
 *
 * @method static \Neon\Http\Response get(string $uri, array $form_data = []) Performs a GET request.
 * @method static \Neon\Http\Response post(string $uri, array $form_data = []) Performs a POST request.
 * @method static \Neon\Http\Response put(string $uri, array $form_data = []) Performs a PUT request.
 * @method static \Neon\Http\Response patch(string $uri, array $form_data = []) Performs a PATCH request.
 * @method static \Neon\Http\Response delete(string $uri, array $form_data = []) Performs a DELETE request.
 * @method static \Neon\Http\Http addHeader(string $key, $value) Adds a header to the request.
 * @method static \Neon\Http\Http bearer(string $token) Adds a bearer token to the request.
 * @method static \Neon\Http\Http addParam(string $key, $value) Adds a form input to the request.
 * @method static \Neon\Http\Http file(string $key, string $filename, string $file_path) Adds a file to the request.
 *
 * @uses \Neon\Http\Http::get()
 * @uses \Neon\Http\Http::post()
 * @uses \Neon\Http\Http::put()
 * @uses \Neon\Http\Http::patch()
 * @uses \Neon\Http\Http::delete()
 * @uses \Neon\Http\Http::addHeader()
 * @uses \Neon\Http\Http::bearer()
 * @uses \Neon\Http\Http::addParam()
 * @uses \Neon\Http\Http::file()
 * @uses \Neon\Http\Http::setBaseURL()
 * @uses \Neon\Http\Http::setFrameworkMethod()
 */
class Http
{
    /**
     * Accessor class.
     *
     * @var string
     */
    private static $accessor = \Neon\Http\Http::class;

    /**
     * @param $method
     * @param $arguments
     *
     * @throws FacadeException
     *
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        $instance = new self::$accessor();

        if( !method_exists($instance, $method) ) {
            throw new FacadeException(self::$accessor, $method);
        }

        return $instance->$method(...$arguments);
    }

    /**
     * Sets the http base request url.
     *
     * @param string $url
     *
     * @return void
     */
    public static function setBaseURL(string $url) : void
    {
        self::$accessor::setBaseURL($url);
    }

    /**
     * Sets the identifier for the framework methods.
     *
     * @param bool $value
     *
     * @return void
     */
    public static function setFrameworkMethod(bool $value)
    {
        self::$accessor::setFrameworkMethod($value);
    }
}