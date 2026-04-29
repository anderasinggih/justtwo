<?php

namespace App\Livewire;

use App\Models\PublicSetting;
use Livewire\Component;

class PublicJourney extends Component
{
    public $playlistId;
    public $videos = []; // Array of ['id' => ..., 'title' => ...]
    public $settings;

    public function mount()
    {
        $this->settings = PublicSetting::getSettings();
        
        // Extract playlist ID
        $url = $this->settings->youtube_url;
        if ($url) {
            preg_match('/list=([a-zA-Z0-9_-]+)/', $url, $matches);
            $this->playlistId = $matches[1] ?? null;

            if ($this->playlistId) {
                $this->fetchVideos();
            }
        }
    }

    protected function fetchVideos()
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
                ]
            ]);
            $html = file_get_contents("https://www.youtube.com/playlist?list=" . $this->playlistId, false, $context);
            
            if ($html) {
                // First, find all videoId occurrences
                preg_match_all('/"videoId":"([a-zA-Z0-9_-]{11})"/', $html, $idMatches);
                
                if (isset($idMatches[1]) && !empty($idMatches[1])) {
                    $ids = array_values(array_unique($idMatches[1]));
                    
                    foreach ($ids as $id) {
                        // For each ID, try to find its title and description in the surrounding text
                        // We look for the first title and descriptionSnippet that appears after the videoId
                        $pos = strpos($html, '"videoId":"' . $id . '"');
                        $segment = substr($html, $pos, 2000); // Look at next 2000 chars
                        
                        preg_match('/"title":\{"runs":\[\{"text":"(.*?)"\}\]/', $segment, $titleMatch);
                        preg_match('/"descriptionSnippet":\{"runs":\[\{"text":"(.*?)"\}\]/', $segment, $descMatch);
                        
                        $this->videos[] = [
                            'id' => $id,
                            'title' => html_entity_decode($titleMatch[1] ?? 'Untitled Video'),
                            'description' => html_entity_decode($descMatch[1] ?? 'no description available')
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            $this->videos = [];
        }
    }

    public function render()
    {
        return view('livewire.public-journey')
            ->layout('layouts.public');
    }
}
