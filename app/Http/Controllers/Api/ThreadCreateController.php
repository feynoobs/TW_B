<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Thread;
use App\Models\Response;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
            'images' => 'nullable|array|max:5',
            'images.*' => 'array:data,file_name',
            'images.*.data' => ['string', 'max:22369647', 'regex:/\A(?:data:)?image\/(?:jpeg|png|gif|webp);base64,[A-Za-z0-9+\/]*={0,2}\z/',],
            'images.*.file_name' => 'string|max:255',
            'mail' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
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

                    $response = new Response();
                    $response->thread_id = $thread->id;
                    $response->content = $request->input('content');
                    $response->name = $request->input('name');
                    $response->mail = $request->input('mail');
                    $response->hash = $request->input('hash');
                    $response->user_agent = $request->userAgent();
                    $response->ip_address = $request->ip();
                    $response->save();

                    foreach ($request->input('images', []) as $image) {
                        $path = "{$board->group_id}/{$board->id}/{$thread->id}/{$response->id}/".Str::uuid();

                        $base64 = preg_replace('/\A(?:data:)?image\/(?:jpeg|png|gif|webp);base64,/', '', $image['data']);
                        $binData = base64_decode($base64, true);

                        $i = new Image();
                        $i->response_id = $response->id;
                        $i->file_name = $image['file_name'];
                        $i->path = $path;
                        $i->size = strlen($binData);
                        $i->mime_type = 'image/' . pathinfo($image['file_name'], PATHINFO_EXTENSION);
                        $i->save();

                        Storage::disk('s3')->put($path, $binData, ['ContentType' => $i->mime_type]);
                    }

                    $r = ['result' => 'success', 'message' => 'Thread created successfully'];
                }
                return $r;
            });
        }

        return response()->json($result);
    }
}
