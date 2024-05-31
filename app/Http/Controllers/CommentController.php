<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    //
    public function list(Request $request)
    {
        $request->validate([
            'limit' => ['required', 'integer', 'min:1', 'max:100'],
        ]);
        $_comments = Comment::where('parent_id', null)->latest()->simplePaginate($request->limit);
        return response()->json([
            'status'    => 'OK',
            'data'  => JsonResource::collection($_comments),
            'code'  => 200
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'comment' => ['required', 'string'],
            'presence' => ['required', 'boolean'],
            'id' => ['nullable', 'string','unique:comments,own,parent_id,NULL'],
        ]);
        if (!empty($request->id)) {
            $comment = Comment::where('uuid', $request->id)->first();
            $comment->comments()->create([
                ...$request->except(['id']),
                'uuid'  => Str::uuid(),
                'user_id' => Auth::id(),
                'own' => $comment->uuid,
                'parent_id' => $request->id
            ]);
            return response()->json([
                'status'    => 'OK',
                'data'  => $comment,
                'code'  => 201
            ]);
        }
        $comment = Comment::create(
            [
                ...$request->except(['id']),
                'uuid'  => Str::uuid(),
                'user_id' => Auth::id(),
                'own' => Str::uuid(),
                'is_admin' => !empty(auth()->user()->is_admin)
            ]
        );
        $_comment = Comment::find($comment->id);
        return response()->json([
            'status'    => 'OK',
            'data'  => $_comment->only(['name', 'presence', 'comment', 'uuid', 'own', 'created_at']),
            'code'  => 201
        ]);
    }

    public function like($uuid, Request $request)
    {
        $comment = Comment::where('uuid', $uuid)->first();
        $uuid = Str::uuid();
        $comment->like()->create([
            'uuid'  => $uuid,
            'user_id' => Auth::id(),
            'comment_id' => $comment->uuid
        ]);

        $comment = Comment::where('uuid', $uuid)->first();
        return response()->json([
            'status'    => 'OK',
            'data'  => ['uuid' => $uuid],
            'code'  => 201
        ]);
    }

    public function unlike($uuid, Request $request)
    {
        $like = Like::where('user_id', Auth::id())->where('uuid', $uuid)->first();
        $like->delete();
        return response()->json([
            'data'  => ['status' => true],
            'code'  => 200
        ]);
    }

    public function show($uuid, Request $request)
    {
        $comment = Comment::where('uuid', $uuid)->first()->only(['name', 'presence', 'comment', 'is_admin', 'created_at']);
        return response()->json([
            'status'    => 'OK',
            'data'  => $comment,
            'code'  => 200
        ]);
    }
    public function update($uuid, Request $request)
    {
        $request->validate([
            'presence' => ['nullable', 'sometimes', 'boolean'],
            'comment' => ['required', 'string'],
        ]);

        $comment = Comment::where('own', $uuid)->firstOrFail();

        // Check if 'presence' is provided, otherwise exclude it from the update data
        $data = $request->only(['comment']);
        if ($request->has('presence') && !empty($request->presence)) {
            $data['presence'] = $request->presence;
        }

        $comment->update($data);

        return response()->json([
            'status' => 'OK',
            'data' => ['status' => true],
            'code' => 200
        ]);
    }


    public function delete($uuid, Request $request)
    {
        $comment = Comment::where('own', $uuid)->first();
        $comment->delete();
        return response()->json([
            'status'    => 'OK',
            'data'  => ['status' => true],
            'code'  => 200
        ]);
    }
}
