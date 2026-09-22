<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;

class BoardListController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $slug): JsonResponse    
    {
        $group = Group
            ::with([
                'boards' => fn ($query) => $query->orderBy('id', 'asc'),
            ])
            ->where('slug', $slug)->firstOrFail();

        return response()->json($group->boards);
    }
}
