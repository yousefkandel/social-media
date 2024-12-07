<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use App\Models\follows;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\FollowedNotification;

class FollowController extends Controller
{
    /**
     * متابعة مستخدم آخر.
     */
    public function follow($userId)
    {
        $user = Auth::user();

        // التحقق إذا كان المستخدم يحاول متابعة نفسه
        if ($user->id == $userId) {
            return response()->json(['error' => 'You cannot follow yourself.'], 400);
        }

        // التحقق إذا كان المستخدم قد تابع بالفعل
        $existingFollow = follows::where('follower_id', $user->id)
                                ->where('following_id', $userId)
                                ->first();

        if ($existingFollow) {
            return response()->json(['message' => 'You are already following this user.']);
        }

        // إضافة المتابعة
        follows::create([
            'follower_id' => $user->id,
            'following_id' => $userId,
        ]);
        $followedUser = User::findOrFail($userId);
        $followedUser->notify(new FollowedNotification($user)); // إرسال الإشعار

        return response()->json(['message' => 'Successfully followed the user.']);
    }

    /**
     * إلغاء متابعة مستخدم آخر.
     */
    public function unfollow($userId)
    {
        $user = Auth::user();

        // التحقق إذا كان المستخدم قد تابع هذا الشخص
        $follow = follows::where('follower_id', $user->id)
                        ->where('following_id', $userId)
                        ->first();

        if (!$follow) {
            return response()->json(['error' => 'You are not following this user.'], 400);
        }

        // إلغاء المتابعة
        $follow->delete();

        return response()->json(['message' => 'Successfully unfollowed the user.']);
    }
}
