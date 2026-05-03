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

    public function journey()
    {
        $settings = PublicSetting::getSettings();
        $theme = $settings->theme ?? 'light';
        $videos = cache()->remember('journey_videos_' . $settings->id, 3600, function() use ($settings) {
            $url = $settings->youtube_url;
            if (!$url) return [];
            
            preg_match('/list=([a-zA-Z0-9_-]+)/', $url, $matches);
            $playlistId = $matches[1] ?? null;
            if (!$playlistId) return [];

            try {
                $context = stream_context_create([
                    'http' => ['header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"]
                ]);
                $html = file_get_contents("https://www.youtube.com/playlist?list=" . $playlistId, false, $context);
                if (!$html) return [];

                preg_match_all('/"videoId":"([a-zA-Z0-9_-]{11})"/', $html, $idMatches);
                if (!isset($idMatches[1]) || empty($idMatches[1])) return [];

                $ids = array_values(array_unique($idMatches[1]));
                $scrapedVideos = [];
                foreach ($ids as $id) {
                    $pos = strpos($html, '"videoId":"' . $id . '"');
                    $segment = substr($html, $pos, 2000);
                    preg_match('/"title":\{"runs":\[\{"text":"(.*?)"\}\]/', $segment, $titleMatch);
                    preg_match('/"descriptionSnippet":\{"runs":\[\{"text":"(.*?)"\}\]/', $segment, $descMatch);
                    
                    $scrapedVideos[] = [
                        'id' => $id,
                        'title' => html_entity_decode($titleMatch[1] ?? 'Untitled Video'),
                        'description' => html_entity_decode($descMatch[1] ?? 'no description available')
                    ];
                }
                return $scrapedVideos;
            } catch (\Exception $e) {
                return [];
            }
        });

        return view('public.journey', [
            'videos' => $videos,
            'theme' => $theme,
            'settings' => $settings,
        ]);
    }
}
