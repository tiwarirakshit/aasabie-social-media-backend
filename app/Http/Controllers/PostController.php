<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Resources\GeneralResponse;
use App\Http\Resources\GeneralError;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Post_gallery;
use App\Models\Post_like;
use App\Models\Follow;
use Validator;

class PostController extends Controller
{
    public function social_store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'description' => 'required',
                'images' => 'required'
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $post = new Post();
            $post->user_id = $request->input('user_id');
            $post->description = $request->input('description');
            $post->save();
            //image
            if ($request->hasFile('images')) {
                foreach($request->file('images') as $key => $value) {
                    $fileName = time() . '_' . $value->getClientOriginalName();
                    // Move the uploaded image to the storage location
                    $filepath = $value->storeAs('images', $fileName, 'public');
                    Post_gallery::create([
                        'post_id' => $post->id,
                        'source_path' => $filepath ?? null,
                    ]);
                }
            }else{
                return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
            }
            return new GeneralResponse(['data'=> $post,'message' => 'Post Created successfully', 'toast' => true]);
            
        } catch (Exception $e) {
            return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    }

    public function social_comment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'post_id' => 'required|exists:posts,id',
                'content' => 'required'
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $comment = new Comment();
            $comment->user_id = $request->input('user_id');
            $comment->post_id = $request->input('post_id');
            $comment->content = $request->input('content'); 
            $comment->save();
            return new GeneralResponse(['data'=> $comment,'message' => 'Comment Send successfully', 'toast' => true]);

        } catch (Exception $e) {
            return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    } 

    public function social_comment_edit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'comment_id' => 'required|exists:comments,id',
                'post_id' => 'required|exists:posts,id',
                'content' => 'required'
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $comment_edit = Comment::where('id',$request->comment_id)->first();
            $comment_edit->user_id = $request->input('user_id');
            $comment_edit->post_id = $request->input('post_id');
            $comment_edit->content = $request->input('content');
            $comment_edit->update();
            return new GeneralResponse(['data'=> $comment_edit,'message' => 'Comment Edit Successfully', 'toast' => true]);
        } catch (Exception $e) {
            return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    } 

    public function postview(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'post_id' => 'required|exists:posts,id',
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            //post id fatch
            $post_user = Post::where('id',$request->post_id)->first();
            $follow_user = Follow::where('following_id',$post_user->user_id)->where('follow',1)->first();

           // dd($follow_user);
            $like = Post_like::where('post_id',$request->post_id)->where('like', 1)->count();
            if($follow_user == Null)
            {
                return 'no record found';
            }else {
                $datas = Post::where('user_id', $follow_user->following_id)->where('id',$post_user->id)->with('postGallery')->get();
                
                foreach($datas as $data)
                {
                    $data->like = $like;
                }
            }
            return new GeneralResponse(['data'=> $datas,'message' => 'Post Details Fetch successfully', 'toast' => true]);

        } catch (Exception $e) {
            return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    } 

    public function userview(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            //post id fatch
            $post_user = Post::where('user_id',$request->user_id)->get();
            foreach($post_user as $user)
            {
                $follow_user = Follow::where('following_id',$user->user_id)->where('follow',1)->first();
                $like = Post_like::where('post_id',$request->post_id)->where('like', 1)->count();
                if($follow_user == Null)
                {
                    return 'no record found';
                }else {
                    $datas = Post::where('user_id', $follow_user->following_id)->with('postGallery')->get();
                    
                    foreach($datas as $data)
                    {
                        $data->like = $like;
                    }
                }
            }
            // dd($post_user->user_id);
            // $follow_user = Follow::where('following_id',$post_user->user_id)->where('follow',1)->first();

           // dd($follow_user);
            
            return new GeneralResponse(['data'=> $datas,'message' => 'Post Details Fetch successfully', 'toast' => true]);

        } catch (Exception $e) {
            return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    } 

    public function postlike(Request $request)
    {   
        try {
            $validator = Validator::make($request->all(), [
                'post_id' => 'required|exists:posts,id',
                'user_id' => 'required|exists:users,id',
                'like' => 'required',
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $post_like = new Post_like();
            $post_like->user_id = $request->input('user_id');
            $post_like->post_id = $request->input('post_id');
            if($request->like)
            {
                $post_like->like = $request->input('like');
            }else{
                $post_like->like = 0;//dislike
            }
            $post_like->save();
            return new GeneralResponse(['data'=> $post_like,'message' => 'Post Liked', 'toast' => true]);

        } catch (Exception $e) {
            return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    }

    public function post_edit(Request $request)
    {      
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'post_id' => 'required|exists:posts,id',
                'description' => 'required',
                'images' => 'required'
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $data = Post::with('postGallery')->where('id',$request->post_id)->first();
            if($request->description){
                $data['description'] = $request->description;
            }
            //image 
            if ($request->hasFile('images')) {
                $result =  Post_gallery::where('post_id',$request->post_id)->get();
                    foreach($request->file('images') as $key => $value) {
                        $fileName = time() . '_' . $value->getClientOriginalName();
                        // Move the uploaded image to the storage location
                        $filepath = $value->storeAs('images', $fileName, 'public');
                            Post_gallery::create([
                            'post_id' =>$request->post_id,
                            'source_path' => $filepath ?? null,
                        ]);
                    }
                $data->save();
            }
            $data = Post::with('postGallery')->where('id',$request->post_id)->first();
            return new GeneralResponse(['data'=> $data,'message' => 'Post Edited successfully', 'toast' => true]);

        } catch (Exception $e) {
            return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    }

    public function post_delete(Request $request)
    {   
        try {
            $validator = Validator::make($request->all(), [
                'post_id' => 'required|exists:posts,id',
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $data = Post::find($request->post_id);
            $result = Post_gallery::where('post_id',$request->post_id)->get();
            foreach($result as $res){
                if (Storage::disk('public')->exists($res->source_path)) {
                    Storage::disk('public')->delete($res->source_path);
                } 
                $res->delete();
            }
            $data_comment = Comment::where('post_id',$request->post_id)->get();
            foreach($data_comment as $comment){
                $comment->delete();
            }
            $data->delete();
            return new GeneralResponse(['data'=> $data,'message' => 'Post Deleted successfully', 'toast' => true]);
            
            
        } catch (Exception $e) {
            return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    }
 
}
