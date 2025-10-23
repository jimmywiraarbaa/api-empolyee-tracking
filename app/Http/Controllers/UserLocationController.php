<?php

namespace App\Http\Controllers;

use App\Models\LatestUserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use OpenApi\Annotations as OA;

class UserLocationController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/locations",
     *   summary="Ambil semua lokasi terakhir user",
     *   tags={"Location"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="max_accuracy",
     *     in="query",
     *     required=false,
     *     description="Filter maksimum akurasi (meter), contoh: 50 → hanya tampil akurasi ≤ 50 m",
     *     @OA\Schema(type="integer", example=50)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Daftar lokasi user",
     *     @OA\JsonContent(
     *       @OA\Property(property="data", type="array",
     *         @OA\Items(
     *           @OA\Property(property="user_id", type="integer", example=1),
     *           @OA\Property(property="user_name", type="string", example="Jimmy"),
     *           @OA\Property(property="lat", type="number", format="float", example=-0.947812),
     *           @OA\Property(property="lng", type="number", format="float", example=100.417523),
     *           @OA\Property(property="recorded_at", type="string", example="2025-10-23 20:00:00"),
     *           @OA\Property(property="accuracy_m", type="integer", nullable=true, example=12)
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(Request $r)
    {
        $query = LatestUserLocation::with(['user:id,name'])
            ->orderByDesc('updated_at');

        if ($r->has('max_accuracy')) {
            $query->where('accuracy_m', '<=', $r->integer('max_accuracy'));
        }

        $items = $query->get()->map(fn($x) => [
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
     * @OA\Put(
     *   path="/api/locations/{id}",
     *   summary="Update atau create lokasi terbaru user (latest only)",
     *   tags={"Location"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID dummy untuk memenuhi resource route (tidak digunakan di server)",
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"lat","lng"},
     *       @OA\Property(property="lat", type="number", format="float", example=-0.947812),
     *       @OA\Property(property="lng", type="number", format="float", example=100.417523),
     *       @OA\Property(property="recorded_at", type="string", example="2025-10-23T20:00:00+07:00"),
     *       @OA\Property(property="accuracy_m", type="integer", nullable=true, example=10)
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Lokasi berhasil diperbarui",
     *     @OA\JsonContent(
     *       @OA\Property(property="ok", type="boolean", example=true),
     *       @OA\Property(property="location", type="object",
     *         @OA\Property(property="user_id", type="integer", example=1),
     *         @OA\Property(property="user_name", type="string", example="Jimmy"),
     *         @OA\Property(property="lat", type="number", format="float", example=-0.947812),
     *         @OA\Property(property="lng", type="number", format="float", example=100.417523),
     *         @OA\Property(property="recorded_at", type="string", example="2025-10-23 20:00:00"),
     *         @OA\Property(property="accuracy_m", type="integer", nullable=true, example=10)
     *       )
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthorized"),
     *   @OA\Response(response=422, description="Validasi gagal")
     * )
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

    // store/show/destroy tidak dipakai untuk demo, jadi tidak didokumentasikan.
}
