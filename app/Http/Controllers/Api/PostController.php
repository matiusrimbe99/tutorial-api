<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\EditPostRequest;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Post::query();
            $perPage = 2;
            $page = $request->input('page', 1);
            $search = $request->input('search');

            if ($search) {
                $query->whereRaw("title LIKE '%" . $search . "%'");
            }

            $total = $query->count();
            $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

            return response()->json([
                'status_code' => 200,
                'status_message' => 'Semua data post ditampilkan',
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'items' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function store(CreatePostRequest $request)
    {
        try {
            $post = new Post();
            $post->title = $request->title;
            $post->description = $request->description;
            $post->user_id = auth()->user()->id;
            $post->save();

            return response()->json([
                'status_code' => 200,
                'status_message' => 'Post berhasil dibuat',
                'data' => $post,
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function update(EditPostRequest $request, Post $post)
    {
        try {
            $post->title = $request->title;
            $post->description = $request->description;

            if ($post->user_id === auth()->user()->id) {
                $post->save();
            } else {
                return response()->json([
                    'status_code' => 422,
                    'status_message' => 'Anda tidak memiliki akses untuk mengubah',
                ]);
            }

            return response()->json([
                'status_code' => 200,
                'status_message' => 'Post berhasil diubah',
                'data' => $post,
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function destroy(Post $post)
    {
        try {
            if ($post->user_id === auth()->user()->id) {
                $post->delete();
            } else {
                return response()->json([
                    'status_code' => 422,
                    'status_message' => 'Anda tidak memiliki akses untuk mengubah',
                ]);
            }

            return response()->json([
                'status_code' => 200,
                'status_message' => 'Post berhasil dihapus',
                'data' => $post,
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
