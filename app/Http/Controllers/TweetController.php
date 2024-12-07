<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TweetController extends Controller
{
   
    public function store(Request $request)
    {
           // التحقق من صحة البيانات المدخلة
           $request->validate([
            'content' => 'required|string|max:140',
        ]);

        // التحقق من وجود مستخدم مُصادق عليه باستخدام Sanctum
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // إنشاء التغريدة
        $tweet = new Tweet();
        $tweet->user_id = $user->id; // استخدام معرف المستخدم المُصادق عليه
        $tweet->content = $request->content;
        $tweet->save();

        // إرجاع استجابة تحتوي على التغريدة المُضافة
        return response()->json($tweet, 201);
    }
    public function update(Request $request, $id)
    {
        // تحقق من أن المستخدم هو صاحب التغريدة
        $tweet = Tweet::findOrFail($id);

        if ($tweet->user_id !== Auth::id()) {
            return response()->json(['error' => 'You can only edit your own tweets.'], 403);
        }

        // التحقق من محتوى التغريدة
        $request->validate([
            'content' => 'required|string|max:140',
        ]);

        // تعديل التغريدة
        $tweet->content = $request->content;
        $tweet->save();

        return response()->json(['message' => 'Tweet updated successfully', 'tweet' => $tweet]);
    }
    public function likeTweet($tweetId)
    {
        // تحقق إذا كان المستخدم مسجلاً الدخول
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tweet = Tweet::findOrFail($tweetId);

        // تحقق إذا كان التغريدة قد حصلت بالفعل على 5 إعجابات
        if ($tweet->likes()->count() >= 5) {
            return response()->json(['message' => 'This tweet has already reached the maximum number of likes (5)'], 400);
        }

        // تحقق إذا كان المستخدم قد أعجب بالفعل بالتغريدة
        if ($tweet->likes()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'You already liked this tweet'], 400);
        }

        // إضافة الإعجاب
        $tweet->likes()->attach(auth()->id());

        // تحديث عدد الإعجابات
        $likesCount = $tweet->likes()->count();

        // إرجاع الاستجابة مع العدد المحدث
        return response()->json([
            'message' => 'Tweet liked successfully',
            'likes_count' => $likesCount,
        ]);
    }

    public function commentOnTweet(Request $request, $tweetId)
    {
   // تحقق من صحة الإدخال
   $request->validate([
    'content' => 'required|string|max:255',
]);

// إيجاد التغريدة
$tweet = Tweet::findOrFail($tweetId);

// إضافة تعليق
$comment = $tweet->comments()->create([
    'user_id' => auth()->id(),
    'content' => $request->content,
]);

// جلب عدد التعليقات وقائمة أحدث التعليقات (5 تعليقات)
$commentsCount = $tweet->comments()->count();
$latestComments = $tweet->comments()->latest()->take(5)->get();

return response()->json([
    'message' => 'Comment added successfully',
    'comment' => $comment,
    'comments_count' => $commentsCount,
    'latest_comments' => $latestComments,
], 201);
    }

        // عرض التغريدات الخاصة بالمستخدمين الذين يتابعهم





}
