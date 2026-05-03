<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PublicSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicAlbumController extends Controller
{
    public function show($year, $month)
    {
        // Convert month name to number
        $monthNumber = date('m', strtotime($month));

        $settings = PublicSetting::first();
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
}
