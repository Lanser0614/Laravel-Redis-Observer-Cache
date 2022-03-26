<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Redis;
use App\Models\Post;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PostResource;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{

    private function responseWith(array $response, int $statusCode): JsonResponse
    {
        return response()->json($response)->setStatusCode($statusCode);
    }

    public function responseWithPagination($data = [], string $status = 'ok', int $statusCode = 200, $paginatorKey = null): JsonResponse
    {
        $response['message'] = '';
        $response['status'] = $status;
        $response['data'] = $data;
        $response['data']['paginator'] = null;
        $paginatorData = $paginatorKey ? Arr::get($data, $paginatorKey) : $data;
        if ($paginatorData instanceof LengthAwarePaginator) {
            $dataArray = $data->toArray();
            $response['data'] = $dataArray['data'];
            $response['data']['paginator'] = Arr::except($dataArray, 'data');
        } elseif ($paginatorData instanceof JsonResource) {
            if ($paginatorData->resource instanceof LengthAwarePaginator) {
                $response['data']['paginator'] = Arr::except($paginatorData->resource->toArray(), 'data');
            }
        }

        return $this->responseWith($response, $statusCode);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return Cache::remember('Posts', 43200, function () {
            // $data = Post::paginate(10);
            // return $this->responseWithPagination(["Post"=>PostResource::collection($data)], 'ok', 200, 'Post');
            return Post::all();
        });
        // $data = Post::paginate(10);
        // return $this->responseWithPagination(["Post"=>PostResource::collection($data)], 'ok', 200, 'Post');
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->all();
        Post::create($data);
        return response()->json(['message'=> 'ok']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cachedBlog = Redis::get('blog_' . $id);


        if(isset($cachedBlog)) {
            $blog = json_decode($cachedBlog, FALSE);

            return response()->json([
                'status_code' => 201,
                'message' => 'Fetched from redis',
                'data' => $blog,
            ]);
        }else {
            $blog = Post::find($id);
            Redis::set('blog_' . $id, $blog);

            return response()->json([
                'status_code' => 201,
                'message' => 'Fetched from database',
                'data' => $blog,
            ]);
    }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, Post $post, $id)
    {
        $data = $request->all();
        $post = Post::find($id);
        // dd($post);
        if ($post) {
          $post->update($data);
        }else {
            $post = 'Post Not Found';
        }
        return $post;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post, $id)
    {
        $post = Post::find($id);
        if ($post) {
            $post->delete();
            return ['status' => true ];
        }else{
            return  ['status' => false ];
        }

    }
}
