<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\UserPasswordResetNotice;
use App\Notifications\UserPasswordResetSuccessNotice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);

            $user = new User();
            $user->email = $request->email;
            $user->name = $request->name;
            $user->password = Hash::make( $request->password);
            $user->save();

            return response()->json([
                'message' => "Registration Success",
            ], 200);
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::whereEmail($request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)){
                return response()->json([
                    'message' => 'The provided credentials are not correct'
                ], 500);
            }

            return response()->json([
                'user' => $user->refresh(),
                'token' => $user->createToken("crimson")->plainTextToken
            ], 200);

        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }


    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            /* clear application cache */
            return response()->json([
                'message' => 'logout success',
            ], 200);
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }


    public function reset(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email'
            ]);

            $user = User::whereEmail($request->email)->first();


            $reset = PasswordReset::where('email', $request->email)->withTrashed()->first();

            if ($reset)
                $reset->forceDelete();

            $reset = new PasswordReset();
            $reset->email = $request->email;
            $reset->token = Str::random(50).Carbon::now()->timestamp;
            $reset->save();

            $user->notify(new UserPasswordResetNotice($reset));

            return response()->json([
                'message' => 'Password reset link has been sent to your email'
            ], 200);
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }


    public function password(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'password' => 'required|min:8',
            ]);

            $reset = PasswordReset::whereToken($request->token)->first();

            if (!$reset)
                return response()->json([
                    'message' => 'Invalid password reset token.'
                ], 404);

            $user = User::whereEmail($reset->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            $reset->forceDelete();
            $user->notify(new UserPasswordResetSuccessNotice());

            return response()->json([
                'message' => 'Password Set Successfully.'
            ], 404);
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            return new JsonResource($request->user());
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
