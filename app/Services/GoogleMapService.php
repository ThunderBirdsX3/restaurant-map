<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GoogleMapService
{
    /**
     * @param array $input
     * @return array
     */
    public function getPlaceByTextSearch(array $input)
    {
        $query = Arr::get($input, 'query');
        $next_page_token = Arr::get($input, 'next_page_token');

        return Cache::remember(
            "get_place:{$query}:{$next_page_token}",
            now()->addMinutes(5),
            function () use ($query, $next_page_token) {
                return is_null($next_page_token)
                    ? $this->getPlace($query)
                    : $this->getPlaceNextPage($next_page_token);
            }
        );
    }

    /**
     * @param string $query
     * @return array
     */
    private function getPlace(string $query)
    {
        $request = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
            'fields' => 'formatted_address%2Cname%2Cgeometry%2Cphotos',
            'query' => $query,
            'inputtype' => 'textquery',
            'key' => config('services.google.google_map_key'),
            'language' => 'th',
            'types' => 'restaurant|food',
        ]);

        return $request->json();
    }

    /**
     * @param string $next_page_token
     * @return array
     */
    private function getPlaceNextPage(string $next_page_token)
    {
        $request = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
            'fields' => 'formatted_address%2Cname%2Cgeometry%2Cphotos',
            'key' => config('services.google.google_map_key'),
            'language' => 'th',
            'pagetoken' => $next_page_token,
        ]);

        return $request->json();
    }
}
