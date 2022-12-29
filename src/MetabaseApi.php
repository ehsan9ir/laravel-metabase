<?php

namespace Ehsan9\MetabaseLaravel;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Request;

class MetabaseApi {

    /** @var string Metabase API base URL */
    private $url;

    /** @var string Metabase API authentication token */
    private $token;

    /**
     * MetabaseClient constructor.
     *
     * @param string $url
     * @param string $username
     * @param string $password
     */
    public function __construct(string $url, string $username, string $password)
    {
        $this->url = $url;
        $this->token = $this->getSessionToken($username, $password);
    }

    /**
     * Obtain session token from cache or generate a new one otherwise.
     *
     * @param string $username
     * @param string $password
     * @return string
     */
    private function getSessionToken(string $username, string $password) : ?string {
        // Hash the username and password and use the result as the cache unique identifier
        $uniqueId = md5($username . $password);
        if (Cache::has('metabase_token_' . $uniqueId)) {
            return Cache::get('metabase_token_' . $uniqueId);
        }
        $sessionToken = $this->generateSessionToken($username, $password);
        if (isset($sessionToken)) {
            Cache::put('metabase_token_' . $uniqueId, $sessionToken, now()->addDays(7));
        }
        return $sessionToken;
    }

    /**
     * Generate a new session token in case it's not available through cache
     *
     * @param string $username
     * @param string $password
     * @return string
     */
    private function generateSessionToken(string $username, string $password) : ?string {
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $data = ["username" => $username, "password" => $password];
        $response = Http::withHeaders($headers)->post($this->url.'/api/session', $data);
        $status = $response->status();
        if ($status === 200) {
            $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            return $response['id'];
        }
        return NULL;
    }

    /**
     * Make a Guzzle call to the specified Metabase API endpoint and retrieve the result
     *
     * @param string $method
     * @param string $endpoint
     * @param array|null $parameters
     * @param string $format
     * @return mixed
     */
    private function call(string $method, string $endpoint, $parameters = NULL, string $format = 'json') {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'X-Metabase-Session' => $this->token,
        ];

        if ($method == 'POST') {
            $response = Http::withHeaders($headers)->asForm()->post($this->url.$endpoint, $parameters);
        } else {
            $response = Http::withHeaders($headers)->asForm()->get($this->url.$endpoint, $parameters);
        }
        if ($format === 'json') {
            return json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        }
        return $response->getBody();
    }

    /**
     * Returns the result of running the query associated with the specified question in an specific format
     * Accepts query parameters to filter the data on the Metabase endpoint
     *
     * @param string $questionId
     * @param string $exportFormat
     * @param array|null $parameters
     * @return mixed
     */
    public function getQuestion(string $questionId, string $exportFormat = 'json', $parameters = NULL) {
        $endpoint = "/api/card/$questionId/query/$exportFormat";
        return $this->call(Request::METHOD_POST, $endpoint, ["parameters" => json_encode($parameters)]);
    }
}
