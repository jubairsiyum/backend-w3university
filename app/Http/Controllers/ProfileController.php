<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserFavorite;
use App\Models\UserActivity;
use App\Models\UserPerformance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Get authenticated user's complete profile
     */
    public function show(Request $request)
    {
        $user = $request->user();
        
        // Load all relationships
        $user->load(['profile', 'performance', 'favorites', 'activities']);
        
        // Calculate additional stats
        $recentActivities = $user->activities()
            ->where('activity_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('activity_date', 'desc')
            ->get();
        
        $todayActivity = $user->activities()
            ->whereDate('activity_date', Carbon::today())
            ->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                    'last_login_at' => $user->last_login_at ?? null,
                ],
                'profile' => $user->profile,
                'performance' => $user->performance,
                'favorites' => $user->favorites,
                'recent_activities' => $recentActivities,
                'today_activity' => $todayActivity,
                'stats' => [
                    'total_favorites' => $user->favorites->count(),
                    'active_days_last_30' => $recentActivities->count(),
                    'total_active_minutes_last_30' => $recentActivities->sum('minutes_active'),
                ],
            ],
        ]);
    }

    /**
     * Update user's basic information
     */
    public function updateBasicInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $user->update($request->only(['name', 'email']));

        return response()->json([
            'success' => true,
            'message' => 'Basic information updated successfully',
            'data' => $user,
        ]);
    }

    /**
     * Update user's profile information
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|max:255|unique:user_profiles,username,' . $request->user()->id . ',user_id',
            'phone' => 'sometimes|nullable|string|max:20',
            'bio' => 'sometimes|nullable|string|max:1000',
            'github_url' => 'sometimes|nullable|url|max:255',
            'linkedin_url' => 'sometimes|nullable|url|max:255',
            'twitter_url' => 'sometimes|nullable|url|max:255',
            'portfolio_url' => 'sometimes|nullable|url|max:255',
            'location' => 'sometimes|nullable|string|max:255',
            'timezone' => 'sometimes|string|max:50',
            'date_of_birth' => 'sometimes|nullable|date',
            'skill_level' => 'sometimes|in:beginner,intermediate,advanced,expert',
            'programming_languages' => 'sometimes|array',
            'interests' => 'sometimes|array',
            'daily_goal_minutes' => 'sometimes|integer|min:0|max:1440',
            'email_notifications' => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
        
        $profile->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $profile,
        ]);
    }

    /**
     * Upload and update avatar
     */
    public function uploadAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        // Delete old avatar if exists
        if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
            Storage::disk('public')->delete($profile->avatar);
        }

        // Store new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $profile->update(['avatar' => $avatarPath]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar uploaded successfully',
            'data' => [
                'avatar_url' => Storage::url($avatarPath),
                'avatar_path' => $avatarPath,
            ],
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 401);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Add favorite item
     */
    public function addFavorite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:course,tutorial,blog,tool,resource',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $favorite = $request->user()->favorites()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Favorite added successfully',
            'data' => $favorite,
        ], 201);
    }

    /**
     * Get all favorites
     */
    public function getFavorites(Request $request)
    {
        $type = $request->query('type');
        
        $query = $request->user()->favorites()->orderBy('order', 'asc');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $favorites = $query->get();

        return response()->json([
            'success' => true,
            'data' => $favorites,
        ]);
    }

    /**
     * Update favorite
     */
    public function updateFavorite(Request $request, $id)
    {
        $favorite = $request->user()->favorites()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|in:course,tutorial,blog,tool,resource',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $favorite->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Favorite updated successfully',
            'data' => $favorite,
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
            'success' => true,
            'message' => 'Favorite deleted successfully',
        ]);
    }

    /**
     * Track user activity (daily)
     */
    public function trackActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'minutes_active' => 'required|integer|min:0',
            'lessons_completed' => 'nullable|integer|min:0',
            'exercises_completed' => 'nullable|integer|min:0',
            'quizzes_completed' => 'nullable|integer|min:0',
            'blogs_read' => 'nullable|integer|min:0',
            'comments_posted' => 'nullable|integer|min:0',
            'code_snippets_created' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $today = Carbon::today();

        // Update or create today's activity
        $activity = $user->activities()->updateOrCreate(
            ['activity_date' => $today],
            [
                'minutes_active' => $request->input('minutes_active', 0),
                'lessons_completed' => $request->input('lessons_completed', 0),
                'exercises_completed' => $request->input('exercises_completed', 0),
                'quizzes_completed' => $request->input('quizzes_completed', 0),
                'blogs_read' => $request->input('blogs_read', 0),
                'comments_posted' => $request->input('comments_posted', 0),
                'code_snippets_created' => $request->input('code_snippets_created', 0),
            ]
        );

        // Update performance stats
        $this->updatePerformanceStats($user);

        return response()->json([
            'success' => true,
            'message' => 'Activity tracked successfully',
            'data' => $activity,
        ]);
    }

    /**
     * Get activity history
     */
    public function getActivityHistory(Request $request)
    {
        $days = $request->query('days', 30);
        $startDate = Carbon::now()->subDays($days);

        $activities = $request->user()->activities()
            ->where('activity_date', '>=', $startDate)
            ->orderBy('activity_date', 'desc')
            ->get();

        // Calculate summary
        $summary = [
            'total_minutes' => $activities->sum('minutes_active'),
            'total_lessons' => $activities->sum('lessons_completed'),
            'total_exercises' => $activities->sum('exercises_completed'),
            'total_quizzes' => $activities->sum('quizzes_completed'),
            'active_days' => $activities->count(),
            'average_daily_minutes' => $activities->count() > 0 
                ? round($activities->sum('minutes_active') / $activities->count(), 2) 
                : 0,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'activities' => $activities,
                'summary' => $summary,
            ],
        ]);
    }

    /**
     * Get user performance stats
     */
    public function getPerformance(Request $request)
    {
        $user = $request->user();
        $performance = $user->performance()->firstOrCreate(
            ['user_id' => $user->id],
            ['joined_date' => $user->created_at]
        );

        return response()->json([
            'success' => true,
            'data' => $performance,
        ]);
    }

    /**
     * Update performance stats (internal method)
     */
    private function updatePerformanceStats(User $user)
    {
        $performance = $user->performance()->firstOrCreate(
            ['user_id' => $user->id],
            ['joined_date' => $user->created_at]
        );

        $totalActivities = $user->activities;

        // Update totals
        $performance->total_lessons_completed = $totalActivities->sum('lessons_completed');
        $performance->total_exercises_completed = $totalActivities->sum('exercises_completed');
        $performance->total_quizzes_completed = $totalActivities->sum('quizzes_completed');
        $performance->total_hours_learned = round($totalActivities->sum('minutes_active') / 60, 2);
        $performance->last_active_date = Carbon::today();

        // Calculate streak
        $this->calculateStreak($user, $performance);

        $performance->save();
    }

    /**
     * Calculate learning streak
     */
    private function calculateStreak(User $user, UserPerformance $performance)
    {
        $activities = $user->activities()
            ->orderBy('activity_date', 'desc')
            ->get();

        $currentStreak = 0;
        $expectedDate = Carbon::today();

        foreach ($activities as $activity) {
            $activityDate = Carbon::parse($activity->activity_date);
            if ($activityDate->isSameDay($expectedDate)) {
                $currentStreak++;
                $expectedDate = $expectedDate->subDay();
            } else {
                break;
            }
        }

        $performance->current_streak = $currentStreak;
        
        if ($currentStreak > $performance->longest_streak) {
            $performance->longest_streak = $currentStreak;
        }
    }

    /**
     * Award badge to user
     */
    public function awardBadge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'badge' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $performance = $user->performance()->firstOrCreate(['user_id' => $user->id]);

        $badges = $performance->badges ?? [];
        
        if (!in_array($request->badge, $badges)) {
            $badges[] = $request->badge;
            $performance->badges = $badges;
            $performance->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Badge awarded successfully',
            'data' => [
                'badges' => $badges,
            ],
        ]);
    }

    /**
     * Get public profile (for other users to view)
     */
    public function getPublicProfile($userId)
    {
        $user = User::with(['profile', 'performance'])->findOrFail($userId);

        // Check if profile is public
        if ($user->profile && !$user->profile->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'This profile is private',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'created_at' => $user->created_at,
                ],
                'profile' => $user->profile ? [
                    'username' => $user->profile->username,
                    'bio' => $user->profile->bio,
                    'avatar' => $user->profile->avatar,
                    'location' => $user->profile->location,
                    'skill_level' => $user->profile->skill_level,
                    'programming_languages' => $user->profile->programming_languages,
                    'github_url' => $user->profile->github_url,
                    'linkedin_url' => $user->profile->linkedin_url,
                    'twitter_url' => $user->profile->twitter_url,
                    'portfolio_url' => $user->profile->portfolio_url,
                ] : null,
                'performance' => $user->performance ? [
                    'total_courses_completed' => $user->performance->total_courses_completed,
                    'total_certificates_earned' => $user->performance->total_certificates_earned,
                    'current_streak' => $user->performance->current_streak,
                    'experience_level' => $user->performance->experience_level,
                    'badges' => $user->performance->badges,
                ] : null,
            ],
        ]);
    }
}
