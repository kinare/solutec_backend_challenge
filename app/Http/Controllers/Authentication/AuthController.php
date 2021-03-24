<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Concerns\ApiResponser;
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
    use ApiResponser;

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

            return $this->success($user, "Registration Success");
        }catch (\Exception $exception){
            return  $this->error( $exception->getMessage());
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
                return $this->error('The provided credentials are not correct');
            }

            return $this->success([
                'user' => $user->refresh(),
                'token' => $user->createToken("crimson")->plainTextToken
            ]);


        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }


    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            /* clear application cache */
            return $this->success(null, 'logout success');
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
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

            return $this->success('', 'Password reset link has been sent to your email');
        }catch (\Exception $exception){
           return $this->error($exception->getMessage());
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
                return $this->error('Invalid password reset token.', 404);

            $user = User::whereEmail($reset->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            $reset->forceDelete();
            $user->notify(new UserPasswordResetSuccessNotice());

            return $this->error('Password Set Successfully.', 404);
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    public function user(Request $request)
    {
        try {
            return $this->success(new JsonResource($request->user()));
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }
}
