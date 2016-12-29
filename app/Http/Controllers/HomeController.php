<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Logs into spotify and gets an auth token
     */
    public function get_playlists()
    {
        $endpoint = sprintf("https://accounts.spotify.com/authorize?client_id=%s&redirect_uri=%s&scope=%s&response_type=code", config('services.spotify.clientid'), urlencode("http://nynaeve.org/spotify_copy/auth1"),"playlist-read-private");
        return redirect($endpoint);
    }

    /**
     * Gets the auth token from the request
     *
     */
    public function auth1(Request $request, Response $response)
    {
        $endpoint = sprintf("https://accounts.spotify.com/api/token");
        $params = array(
            'grant_type' => 'authorization_code',
            'code' => Input::get('code'),
            'redirect_uri' => Config::get('spotify.REDIRECT_URI'),
            'client_id' => Config::get('spotify.CLIENT_ID'),
            'client_secret' => Config::get('spotify.CLIENT_SECRET'),
        );

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($params),
            ),
        );
        $context  = stream_context_create($options);
        $result = json_decode(file_get_contents($endpoint, false, $context));

        Session::put('access_token', $result->access_token);
        dump($result->access_token);

    }
}
