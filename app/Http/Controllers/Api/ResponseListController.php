<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ResponseListController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id): JsonResponse
    {
        $thread = Thread
            ::with([
                'responses' => fn ($query) => $query->orderBy('id', 'asc'),
            ])
            ->where('id', $id)->first();

        return response()->json($thread);
    }
}
