<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchPlaceRequest;
use App\Services\GoogleMapService;

class GoogleMapController extends Controller
{
    public function __construct(
        protected GoogleMapService $google_map_service,
    ) {
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showMap()
    {
        return view('map');
    }

    /**
     * @param \App\Http\Requests\SearchPlaceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPlace(SearchPlaceRequest $request)
    {
        $places = $this->google_map_service->getPlaceByTextSearch($request->validationData());

        return response()->json($places);
    }
}
