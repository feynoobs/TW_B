<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Response;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ResponseCreateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $rules = [
            'content' => 'required|string|max:2048',
            'images' => 'nullable|array|max:5',
            'images.*' => 'array:data,file_name',
            'images.*.data' => ['string', 'max:22369647', 'regex:/\A(?:data:)?image\/(?:jpeg|png|gif|webp);base64,[A-Za-z0-9+\/]*={0,2}\z/',],
            'images.*.file_name' => 'string|max:255',
            'mail' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);
        $result = ['result' => 'fail', 'message' => $validator->errors()->first()];
        

        if (!$validator->fails()) {
            $result = DB::transaction(function () use ($request) {
                $r = ['result' => 'fail', 'message' => 'Thread not found'];
                $thread = Thread::where('id', $request->input('thread_id'))->first();
                if (!is_null($thread)) {
                    $response = new Response();
                    $response->thread_id = $thread->id;
                    $response->content = $request->input('content');
                    $response->name = $request->input('name');
                    $response->mail = $request->input('mail');
                    $response->hash = $request->input('hash');
                    $response->user_agent = $request->userAgent();
                    $response->ip_address = $request->ip();
                    $response->save();

                    // sageでない場合は更新時間を更新する
                    // この更新時間でスレの順位がソートされる
                    // sageでないのはageであるということ
                    if (mb_strpos($response->mail, 'sage') === false) {
                        $thread->modified_at = now();
                        $thread->save();
                    }

                    foreach ($request->input('images', []) as $image) {
                        $path = "{$thread->board->group_id}/{$thread->board_id}/{$thread->id}/{$response->id}/".Str::uuid();
                        $base64 = preg_replace('/\A(?:data:)?image\/(?:jpeg|png|gif|webp);base64,/', '', $image['data']);
                        $binData = base64_decode($base64, true);

                        $imageModel = new Image();
                        $imageModel->response_id = $response->id;
                        $imageModel->file_name = $image['file_name'];
                        $imageModel->path = $path;
                        $imageModel->size = strlen($binData);
                        $imageModel->mime_type = 'image/' . pathinfo($image['file_name'], PATHINFO_EXTENSION);
                        $imageModel->save();

                        Storage::disk('s3')->put($path, $binData, ['ContentType' => $imageModel->mime_type]);
                    }

                    return ['result' => 'success', 'message' => 'Response created successfully'];
                }
                return $r;
            });
        }
        return response()->json($result);
    }
}
