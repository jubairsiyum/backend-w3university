<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function show(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
                'profile' => [
                    'username' => $user->username ?? null,
                    'phone' => $user->phone ?? null,
                    'bio' => $user->bio ?? null,
                    'avatar' => $user->avatar ?? null,
                    'github_url' => $user->github_url ?? null,
                    'linkedin_url' => $user->linkedin_url ?? null,
                    'twitter_url' => $user->twitter_url ?? null,
                    'portfolio_url' => $user->portfolio_url ?? null,
                    'location' => $user->location ?? null,
                    'timezone' => $user->timezone ?? null,
                    'date_of_birth' => $user->date_of_birth ?? null,
                    'skill_level' => $user->skill_level ?? 'beginner',
                    'programming_languages' => $user->programming_languages ?? [],
                    'interests' => $user->interests ?? [],
                    'daily_goal_minutes' => $user->daily_goal_minutes ?? 0,
                    'email_notifications' => $user->email_notifications ?? true,
                    'is_public' => $user->is_public ?? true,
                ],
                'stats' => [
                    'total_courses' => 0, // Add actual data when you have courses
                    'hours_learned' => 0,
                    'certificates_earned' => 0,
                    'current_streak' => $user->current_streak ?? 0,
                ]
            ]
        ]);
    }

    /**
     * Update basic info (name, email)
     */
    public function updateBasicInfo(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
        ]);

        $request->user()->update($validated);
        
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $request->user()
        ]);
    }

    /**
     * Update profile details
     */
    public function updateDetails(Request $request)
    {
        $validated = $request->validate([
            'username' => 'sometimes|string|max:255|unique:users,username,' . $request->user()->id,
            'phone' => 'sometimes|string|max:20',
            'bio' => 'sometimes|string|max:1000',
            'github_url' => 'sometimes|url|nullable',
            'linkedin_url' => 'sometimes|url|nullable',
            'twitter_url' => 'sometimes|url|nullable',
            'portfolio_url' => 'sometimes|url|nullable',
            'location' => 'sometimes|string|max:255',
            'timezone' => 'sometimes|string|max:50',
            'date_of_birth' => 'sometimes|date|nullable',
            'skill_level' => 'sometimes|string|in:beginner,intermediate,advanced,expert',
            'programming_languages' => 'sometimes|array',
            'interests' => 'sometimes|array',
            'daily_goal_minutes' => 'sometimes|integer|min:0',
            'email_notifications' => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
        ]);

        $request->user()->update($validated);
        
        return response()->json([
            'message' => 'Profile details updated successfully',
            'user' => $request->user()
        ]);
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request)
    {
        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar && file_exists(public_path($user->avatar))) {
            unlink(public_path($user->avatar));
        }

        // Store new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => '/storage/' . $avatarPath]);

        return response()->json([
            'message' => 'Avatar uploaded successfully',
            'avatar_url' => asset('/storage/' . $avatarPath)
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Get user favorites
     */
    public function getFavorites(Request $request)
    {
        $type = $request->query('type');
        
        $query = $request->user()->favorites();
        
        if ($type) {
            $query->where('type', $type);
        }

        $favorites = $query->orderBy('order')->get();

        return response()->json($favorites);
    }

    /**
     * Add favorite
     */
    public function addFavorite(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:course,tutorial,blog,tool,resource',
            'title' => 'required|string|max:255',
            'description' => 'sometimes|string|max:500',
            'url' => 'sometimes|url|nullable',
            'category' => 'sometimes|string|max:100',
            'tags' => 'sometimes|array',
            'order' => 'sometimes|integer',
        ]);

        $favorite = $request->user()->favorites()->create($validated);

        return response()->json([
            'message' => 'Favorite added successfully',
            'favorite' => $favorite
        ], 201);
    }

    /**
     * Update favorite
     */
    public function updateFavorite(Request $request, $id)
    {
        $favorite = $request->user()->favorites()->findOrFail($id);

        $validated = $request->validate([
            'type' => 'sometimes|in:course,tutorial,blog,tool,resource',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:500',
            'url' => 'sometimes|url|nullable',
            'category' => 'sometimes|string|max:100',
            'tags' => 'sometimes|array',
            'order' => 'sometimes|integer',
        ]);

        $favorite->update($validated);

        return response()->json([
            'message' => 'Favorite updated successfully',
            'favorite' => $favorite
        ]);
    }

    /**
     * Delete favorite
     */
    public function deleteFavorite(Request $request, $id)
    {
        $favorite = $request->user()->favorites()->findOrFail($id);
        $favorite->delete();

        return response()->json([
            'message' => 'Favorite deleted successfully'
        ]);
    }

    /**
     * Track activity
     */
    public function trackActivity(Request $request)
    {
        $validated = $request->validate([
            'minutes_active' => 'required|integer|min:0',
            'lessons_completed' => 'sometimes|integer|min:0',
            'exercises_completed' => 'sometimes|integer|min:0',
            'quizzes_completed' => 'sometimes|integer|min:0',
            'blogs_read' => 'sometimes|integer|min:0',
            'comments_posted' => 'sometimes|integer|min:0',
            'code_snippets_created' => 'sometimes|integer|min:0',
        ]);

        $activity = $request->user()->activities()->create($validated);

        return response()->json([
            'message' => 'Activity tracked successfully',
            'activity' => $activity
        ], 201);
    }

    /**
     * Get activity history
     */
    public function getActivityHistory(Request $request)
    {
        $days = $request->query('days', 30);
        
        $activities = $request->user()->activities()
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($activities);
    }

    /**
     * Get performance stats
     */
    public function getPerformance(Request $request)
    {
        $user = $request->user();
        
        // Calculate performance metrics
        $stats = [
            'total_minutes' => $user->activities()->sum('minutes_active'),
            'total_lessons' => $user->activities()->sum('lessons_completed'),
            'total_exercises' => $user->activities()->sum('exercises_completed'),
            'total_quizzes' => $user->activities()->sum('quizzes_completed'),
            'badges' => $user->badges ?? [],
            'current_streak' => $user->current_streak ?? 0,
            'longest_streak' => $user->longest_streak ?? 0,
        ];

        return response()->json($stats);
    }

    /**
     * Award badge
     */
    public function awardBadge(Request $request)
    {
        $validated = $request->validate([
            'badge' => 'required|string|max:100',
        ]);

        $user = $request->user();
        $badges = $user->badges ?? [];
        
        if (!in_array($validated['badge'], $badges)) {
            $badges[] = $validated['badge'];
            $user->update(['badges' => $badges]);
        }

        return response()->json([
            'message' => 'Badge awarded successfully',
            'badges' => $badges
        ]);
    }

    /**
     * Get public profile
     */
    public function getPublicProfile($userId)
    {
        $user = \App\Models\User::findOrFail($userId);

        // Only return public information
        if (!$user->is_public) {
            return response()->json([
                'message' => 'This profile is private'
            ], 403);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'bio' => $user->bio,
            'avatar' => $user->avatar,
            'location' => $user->location,
            'github_url' => $user->github_url,
            'linkedin_url' => $user->linkedin_url,
            'twitter_url' => $user->twitter_url,
            'portfolio_url' => $user->portfolio_url,
            'skill_level' => $user->skill_level,
            'programming_languages' => $user->programming_languages,
            'badges' => $user->badges,
        ]);
    }
}
