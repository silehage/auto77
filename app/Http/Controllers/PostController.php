<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $posts = [];
        if($request->query('q') == 'listing'){

            $posts = Cache::rememberForever('listing_post', function() {
                return Post::listing()->latest()->get();
            });

        }elseif($request->query('q') == 'promote'){

            $posts = Cache::rememberForever('promote_post', function() {
                return Post::promote()->latest()->get();
            });

        } else {

            $posts = Cache::rememberForever('all_post', function() {
                return Post::latest()->get();
            });
        }

        return response()->json([
            'success' => true,
            'results' => $posts
        ]);
    }
    public function getListing()
    {
        return response()->json([
            'success' => true,
            'results' => Post::listing()->latest()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required'],
            'image' => ['required'],
            'body' => ['required']
        ]);

        $path = public_path('/upload/images');

        if(! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        $result = ['success' => true];

        DB::beginTransaction();


        try {
            //code...
            $post = new Post();
    
            $post->title = $request->title;
            $post->slug = Str::slug($request->title);
            $post->tags = $request->tags;
            $post->body = $request->body;
    
            $post->is_listing = $request->boolean('is_listing');
            $post->is_promote = $request->boolean('is_promote');
    
            if($file = $request->file('image')) {
    
                $filename = Str::random(42).'.' . $file->extension();
                        
                if($file->move($path, $filename)){
    
                    $post->image = $filename;
                }
            }
            
            $post->save();
    
            if($request->gallery && count($request->gallery) > 0) {
                foreach($request->gallery as $file) {
                    
                    $filename = Str::random(41).'.' . $file->extension();
    
                    $file->move($path, $filename);
    
                    $post->galleries()->create([
                        'filename' => $filename
                    ]);
                }
            }
            
            DB::commit();


        } catch (\Throwable $th) {

            DB::rollback();

            $result = ['success' => false, 'message' => $th->getMessage()];
        }

        return response($result);
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'success' => true,
            'results' => Post::with('galleries')->where('id', $id)->first()
        ]);
    }
    public function getPostBySlug($slug)
    {
        return response()->json([
            'success' => true,
            'results' => Post::with('galleries')->where('slug', $slug)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => ['required'],
            'image' => $request->boolean('del_image')? ['required'] : ['nullable'],
            'body' => ['required']
        ]);

        $path = public_path('/upload/images');

        $post = Post::findOrFail($id);

        if($request->boolean('del_image')) {

            File::delete('upload/images/'. $post->image);
 
        }


        if($file = $request->file('image')) {

            $filename = Str::random(42).'.' . $file->extension();
                    
            if($file->move($path, $filename)){

                $post->image = $filename;
            }
        }

        $post->title = $request->title;
        $post->tags = $request->tags;
        $post->body = $request->body;

        $post->is_listing = $request->boolean('is_listing');
        $post->is_promote = $request->boolean('is_promote');

        $post->save();

        if($request->delete_gallery) {

            $assetIds = json_decode($request->delete_gallery);

            Asset::whereIn('id', $assetIds)->delete();
        }

        if($request->gallery && count($request->gallery) > 0) {
            foreach($request->gallery as $file) {
                
                $filename = Str::random(41).'.' . $file->extension();

                $file->move($path, $filename);

                $post->galleries()->create([
                    'filename' => $filename
                ]);
            }
        }

        return response([
            'success' => true
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if($post->image) {
            File::delete('upload/images/'. $post->image);
        }

        $post->delete();

        return response([
            'success' => true
        ], 200);
    }
}
