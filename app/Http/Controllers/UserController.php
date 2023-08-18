<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Follow;
use App\Http\Resources\GeneralResponse;
use App\Http\Resources\GeneralError;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'password' => 'required|min:5',
                'email' => 'required|email|unique:users'
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = $request->input('password');
            $user->save();
            return new GeneralResponse(['data'=> $user,'message' => 'User Register Successfully', 'toast' => true]);
            
        } catch (Exception $e) {
            return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    }

    public function follow(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'following_id' => 'required|exists:users,id',
                'follow' => 'required'
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $follow = new Follow();
            $follow->user_id = $request->input('user_id');
            $follow->following_id = $request->input('following_id');
            if($request->follow)
            {
                $follow->follow = $request->input('follow');
            }else{
                $follow->follow = 0;//unfollow
            }
            $follow->save();
            return new GeneralResponse(['data'=> $follow,'message' => 'starting a Following', 'toast' => true]);
            
        } catch (Exception $e) {
            return new GeneralError(['code' => 500, 'message' => $e, 'toast' => true]);
        }
    }
}
