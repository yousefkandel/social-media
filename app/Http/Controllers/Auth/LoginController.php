<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // التحقق من وجود البيانات المدخلة
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // جلب المستخدم بناءً على البريد الإلكتروني
        $user = User::where('email', $request->email)->first();

        // التأكد من وجود المستخدم
        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // التحقق من كلمة المرور المدخلة
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // إنشاء التوكن للمستخدم
        $token = $user->createToken('auth_token')->plainTextToken;

        // إرجاع التوكن مع بيانات المستخدم
        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }
}
