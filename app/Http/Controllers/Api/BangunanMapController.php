<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bangunan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class BangunanMapController extends Controller
{
    /**
     * Mengambil semua data bangunan dalam format GeoJSON.
     */
    public function getGeoJson(): JsonResponse
    {
        $bangunans = Bangunan::with('rw', 'rt')->get();

        $features = $bangunans->map(function ($bangunan) {
            if (is_null($bangunan->latitude) || is_null($bangunan->longitude)) {
                return null;
            }

            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $bangunan->longitude, (float) $bangunan->latitude]
                ],
                'properties' => [
                    'nama' => $bangunan->nama_bangunan,
                    'kategori' => $bangunan->kategori,
                    'deskripsi' => $bangunan->deskripsi,
                    'foto_url' => $bangunan->foto ? Storage::url($bangunan->foto) : asset('images/no-image.png'),
                    'rw' => $bangunan->rw->nomor_rw ?? 'N/A',
                    'rt' => $bangunan->rt->nomor_rt ?? 'N/A'
                ]
            ];
        })->filter();

        $geoJsonData = [
            'type' => 'FeatureCollection',
            'features' => $features
        ];

        return response()->json($geoJsonData);
    }
}
