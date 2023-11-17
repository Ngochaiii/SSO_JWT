<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AuthController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'changePassWord']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $expirationTime = Carbon::now()->addHours(2); // Thời gian sống: 2 giờ

        try {
            if (!$token = JWTAuth::attempt($credentials, ['exp' => $expirationTime])) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(compact('token'));
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token
        ], 201);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        // $request->session()->invalidate(); // Hủy bỏ phiên hiện tại
        // $request->session()->regenerateToken(); // Tạo token mới cho phiên tiếp theo

        // return redirect('/home')->with('message', 'Đăng xuất thành công');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        if (Session::has('jwt_token')) {
            $jwtToken = Session::get('jwt_token');

            // Hiển thị token hoặc thực hiện các thao tác khác
            return response()->json(['token' => $jwtToken]);
        } else {
            // Token không tồn tại trong session
            return response()->json(['error' => 'Token not found in session'], 404);
        }
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function changePassWord(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'email' => 'required|email',
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();
        // Check if the provided email matches the authenticated user's email
        if ($request->email !== $user->email) {
            throw ValidationException::withMessages(['email' => 'The provided email does not match the authenticated user\'s email.']);
        }
        // Check if the provided current password matches the user's actual password
        if (!Hash::check($request->input('currentPassword'), $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 401);
        }
        // Update the user's password
        $user->password = Hash::make($request->input('newPassword'));
        $user->save();

        return response()->json(['message' => 'Password updated successfully']);
    }
}
