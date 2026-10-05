<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; 

class ThreadCreateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $slug): JsonResponse
    {
        $rules = [
            'slug' => 'required|string|exists:boards,slug|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2048',
        ];
        $request->merge(['slug' => $slug]);
        $validator = Validator::make($request->all(), $rules);
        $result = ['result' => 'fail', 'message' => $validator->errors()->first()];
        if (!$validator->fails()) {
            $result = DB::transaction(function () use ($request, $slug) {
                $r = ['result' => 'fail', 'message' => 'Board not found'];
                $board = Board::where('slug', $slug)->first();
                if (!is_null($board)) {
                    $thread = new Thread();
                    $thread->board_id = $board->id;
                    $thread->title = $request->input('title');
                    $thread->modified_at = now();
                    $thread->save();
                    $r = ['result' => 'success', 'message' => 'Thread created successfully'];
                }
                return $r;
            });
        }

        return response()->json($result);
    }
}
