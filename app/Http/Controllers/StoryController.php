<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Story;
use App\Models\User;
use App\Models\Post_like;
use App\Models\Follow;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Resources\GeneralResponse;
use App\Http\Resources\GeneralError;
use Validator;

class StoryController extends Controller
{
    public function social_story(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'text' => 'required',
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $story = new Story();
            $story->user_id = $request->input('user_id');
            $story->text = $request->input('text');
            $story->story_time = Carbon::now('Asia/Kolkata');

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('photos', 'public');
                $story['photo'] = $photoPath;
            }
            if ($request->hasFile('video')) {
                $videoPath = $request->file('video')->store('videos', 'public');
                $story['video'] = $videoPath;
            }
            $story->save();
            return new GeneralResponse(['data'=> $story,'message' => 'Story add successfully', 'toast' => true]);

        } catch (Exception $e) {
        return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    }

    public function story_view(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'story_id' => 'required|exists:stories,id',
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $user = Story::where('id',$request->story_id)->first();
          
            $follow_user = Follow::where('following_id',$user->user_id)->where('follow',1)->first();
           // dd($follow_user);
            if($follow_user == Null)
            {
                return 'no record found';
            }else {
                $datas = Story::where('user_id', $follow_user->following_id)->where('id',$user->id)->first();
            } 
            return new GeneralResponse(['data'=> $datas,'message' => 'Story Details Fetch successfully', 'toast' => true]);

        } catch (Exception $e) {
            return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    } 

    public function user_story_view(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            //post id fatch
            $post_user = Story::where('user_id',$request->user_id)->get();

            foreach($post_user as $user)
            {
                $follow_user = Follow::where('following_id',$user->user_id)->where('follow',1)->first();

                if($follow_user == Null)
                {
                    return 'no record found';
                }else {
                    $datas = Story::where('user_id', $follow_user->following_id)->get();
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


    public function story_delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'story_id' => 'required|exists:stories,id',
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            
            $story_delete = Story::find($request->story_id);
                !is_null($story_delete->photo) &&  Storage::disk('public')->delete($story_delete->photo);
                !is_null($story_delete->video) &&  Storage::disk('public')->delete($story_delete->video);
            $story_delete->delete();
           
            return new GeneralResponse(['data'=> $story_delete,'message' => 'Story Delete successfully', 'toast' => true]);
        } catch (Exception $e) {
            return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    }
}
