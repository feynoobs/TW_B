<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;

class ThreadListController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $slug): JsonResponse
    {
        $board = Board
            ::with([
                'threads' => fn ($query) => $query->orderBy('modified_at', 'desc'),
            ])
            ->where('slug', $slug)->firstOrFail();

        return response()->json($board);
    }
}
