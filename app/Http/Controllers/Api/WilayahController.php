<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rt;
use Illuminate\Http\JsonResponse;

class WilayahController extends Controller
{
    /**
     * Mengambil data RT berdasarkan RW ID.
     */
    public function getRtByRw($rw_id): JsonResponse
    {
        $rts = Rt::where('rw_id', $rw_id)
            ->orderBy('nomor_rt', 'asc')
            ->get(['id', 'nomor_rt']);

        return response()->json($rts);
    }
}
