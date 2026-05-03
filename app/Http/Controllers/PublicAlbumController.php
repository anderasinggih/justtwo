<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PublicSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PublicAlbumController extends Controller
{
    public function show($year, $month)
    {
        // Convert month name to number robustly
        $monthNumber = date('m', strtotime("1 $month $year"));

        $settings = PublicSetting::getSettings();
        $theme = $settings->theme ?? 'light';

        $posts = Post::where('is_public', true)
            ->where('is_archived', false)
            ->where('is_secret', false)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNumber)
            ->with(['user', 'media', 'reactions'])
            ->latest()
            ->paginate(48);

        $allMediaPaths = Post::where('is_public', true)
            ->where('is_archived', false)
            ->where('is_secret', false)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNumber)
            ->with(['media'])
            ->get()
            ->flatMap(fn($p) => $p->media)
            ->map(fn($m) => Storage::disk('public')->url($m->file_path_original))
            ->shuffle()
            ->values()
            ->all();

        return view('public.album-detail', [
            'posts' => $posts,
            'year' => $year,
            'month' => $month,
            'monthName' => $month,
            'theme' => $theme,
            'allMediaPaths' => $allMediaPaths,
            'settings' => $settings,
        ]);
    }

    public function preview(Post $post)
    {
        $settings = PublicSetting::getSettings();
        $theme = $settings->theme ?? 'light';
        
        $allMedia = $post->media->map(function ($m) {
            return [
                'id' => $m->id,
                'post_id' => $m->post_id,
                'file_path' => Storage::disk('public')->url($m->file_path_original),
                'file_type' => $m->file_type,
                'captured_at' => $m->captured_at ? $m->captured_at->format('M d, Y') : $m->created_at->format('M d, Y'),
                'lat' => $m->lat,
                'lng' => $m->lng,
                'location_name' => $m->location_name,
            ];
        });

        return view('public.post-preview', [
            'post' => $post,
            'allMedia' => $allMedia,
            'theme' => $theme,
            'settings' => $settings,
        ]);
    }

    public function toggleReaction(Post $post)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $reaction = $post->reactions()->where('user_id', $user->id)->first();

        if ($reaction) {
            $reaction->delete();
            return response()->json(['status' => 'removed']);
        } else {
            $post->reactions()->create([
                'user_id' => $user->id,
                'type' => 'heart'
            ]);
            return response()->json(['status' => 'added']);
        }
    }
}
