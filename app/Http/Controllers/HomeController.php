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
            'redirect_uri' => urlencode("http://nynaeve.org/spotify_copy/auth1"),
            'client_id' => Config::get('services.spotify.clientid'),
            'client_secret' => Config::get('services.spotify.clientsecret'),
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

        $BASE_URL = "https://api.spotify.com";

        $return = $this->call_spotify($BASE_URL."/v1/me/playlists");
        
        dump($return);

    }

    public function call_spotify($url) {
        $headerStr = "Authorization: Bearer ". Session::get('access_token') ."\r\n";
        // Create a stream
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>$headerStr,
            )
        );

        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        return file_get_contents($url, false, $context);
    }
}
