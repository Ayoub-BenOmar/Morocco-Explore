<?php

namespace App\Http\Controllers;

use id;
use Log;
use App\Models\Itineraire;
use App\Models\Destination;
use App\Models\ToVisitList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ItineraireController extends Controller
{

    public function store(Request $request)
    {
        $validation = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'image' => 'nullable|string',
            'destinations' => 'required|array|min:2',
            'destinations.*.name' => 'required|string|max:255',
            'destinations.*.accommodation' => 'nullable|string',
            'destinations.*.places_to_visit' => 'nullable|string',
            'destinations.*.activities' => 'nullable|string',
            'destinations.*.dishes' => 'nullable|string',
        ]);

        $itinerary = Itineraire::create([
            'title' => $request->title,
            'category' => $request->category,
            'duration' => $request->duration,
            'image' => $request->image,
            'user_id' => Auth::id(),
        ]);

        DB::table('destinations')->insert([
            "itinerary_id" => $itinerary,
            "name" => $validation['destinations']['name'],
            "accommodation" => $validation['destinations']['lieu_logement'],
            "places_to_visit" => json_encode(explode(',', $validation['destinations']['places_to_visit'] ?? '')),
            "activiactivitiestes" => json_encode(explode(',', $validation['destinations']['activities'] ?? '')),
            "dishes" => json_encode(explode(',', $validation['destinations']['dishes'] ?? '')),
        ]); 

        // foreach ($request->destinations as $destinationData) {
        //     // Log::info('Request Data:', $destinationData);
        //     Destination::create([
        //         'name' => $destinationData['name'],
        //         'accommodation' => $destinationData['accommodation'],
        //         'places_to_visit' => json_encode(explode(',', $validation['destinations']['places_to_visit'] ?? '')),
        //         'activities' => json_encode(explode(',', $validation['destinations']['activities'] ?? '')),
        //         'dishes' => json_encode(explode(',', $validation['destinations']['dishes'] ?? '')),
        //         'itinerary_id' => $itinerary->id,
        //     ]);
        // }

        return response()->json([
            'message' => 'Itinerary created successfully',
            'itinerary' => $itinerary->load('destinations'),
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $itinerary = Itineraire::findOrFail($id);

        if ($itinerary->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'You are not authorized to update this itinerary',
            ], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:255',
            'duration' => 'sometimes|integer|min:1',
            'image' => 'nullable|string',
        ]);

        $itinerary->update($request->only(['title', 'category', 'duration', 'image']));

        // Return the 
        return response()->json([
            'message' => 'Itinerary updated successfully',
            'itinerary' => $itinerary,
        ]);
    }


    public function addToVisitList(Request $request, $itineraryId)
    {
        $itinerary = Itineraire::findOrFail($itineraryId);

        $existingEntry = ToVisitList::where('user_id', Auth::id())
            ->where('itinerary_id', $itineraryId)
            ->first();

        if ($existingEntry) {
            return response()->json([
                'message' => 'Itinerary is already in your "To Visit" list',
            ], 400);
        }

        ToVisitList::create([
            'user_id' => Auth::id(),
            'itinerary_id' => $itineraryId,
        ]);

        return response()->json([
            'message' => 'Itinerary added to your "To Visit" list',
        ], 201);
    }
}
