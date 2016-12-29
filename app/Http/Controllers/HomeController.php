<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;


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
        $endpoint = sprintf("https://accounts.spotify.com/authorize?client_id=%s&redirect_uri=%s&scope=%s&response_type=token", config('services.spotify.clientid'), urlencode("http://nynaeve.org/spotify_copy/auth1"),"playlist-read-private");
        return redirect($endpoint);
    }

    /**
     * Gets the auth token from the request
     *
     */
    public function auth1(Request $request, Response $response)
    {

        dump($request->fullUrl(), $response);

    }
}
