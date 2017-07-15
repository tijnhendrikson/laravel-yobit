<?php
namespace Pepijnolivier\Yobit;

class YobitManager
{
    /**
     * @var Client
     */
    public $client;

    /**
     * YobitManager constructor.
     */
    public function __construct()
    {

    }

    /**
     * Package version.
     *
     * @return string
     */
    public function version()
    {
        return '0.1';
    }

    /**
     * Create new client instance with given credentials.
     *
     * @param array $auth
     * @param array $urls
     * @return Client
     */
    private function with(array $auth, array $urls = null)
    {
        $urls = $urls ?: config('yobit.urls');

        $client = new Client($auth, $urls);

        return $client;
    }

    /**
     * Dynamically call methods on the client.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $client = $this->with(
            config('yobit.auth')
        );

        if (!method_exists($client, $method)) {
            abort(500, "Method $method does not exist");
        }

        return call_user_func_array([$client, $method], $parameters);
    }
}
