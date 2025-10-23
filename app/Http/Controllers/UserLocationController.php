<?php

namespace App\Http\Controllers;

use App\Models\LatestUserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $r)
    {
        $items = LatestUserLocation::with(['user:id,name'])
            ->orderByDesc('updated_at')->get()
            ->map(fn($x) => [
                'user_id' => $x->user_id,
                'user_name' => $x->user->name ?? 'Unknown',
                'lat' => (float) $x->lat,
                'lng' => (float) $x->lng,
                'recorded_at' => $x->recorded_at,
                'accuracy_m' => $x->accuracy_m,
            ]);

        return response()->json(['data' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $r)
    {
        $data = $r->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'recorded_at' => ['nullable', 'date'],
            'accuracy_m' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $user = $r->user();
        $loc = LatestUserLocation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'lat' => (float) $data['lat'],
                'lng' => (float) $data['lng'],
                'recorded_at' => Carbon::parse($data['recorded_at'] ?? now())->toDateTimeString(),
                'accuracy_m' => $data['accuracy_m'] ?? null,
            ]
        );

        return response()->json([
            'ok' => true,
            'location' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'lat' => (float) $loc->lat,
                'lng' => (float) $loc->lng,
                'recorded_at' => $loc->recorded_at,
                'accuracy_m' => $loc->accuracy_m,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
