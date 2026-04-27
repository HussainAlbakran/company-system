<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class PublicHomeController extends Controller
{
    public function index(): View
    {
        $projects = Project::query()
    ->orderByDesc('updated_at')
    ->limit(6)
    ->get();

        $usingSampleProjects = $projects->isEmpty();

        if ($usingSampleProjects) {
            $projects = $this->sampleProjects();
        }

        return view('public.home', [
            'projects' => $projects,
            'usingSampleProjects' => $usingSampleProjects,
            'logoUrl' => $this->publicAssetUrl(['images/public/logo.svg', 'images/public/logo.png', 'images/public/logo.webp']),
            'heroBgUrl' => $this->publicAssetUrl(['images/public/hero-bg.webp', 'images/public/hero-bg.jpg', 'images/public/hero-bg.png']),
        ]);
    }

    /**
     * @param  list<string>  $relativePaths  Paths under public/, first existing wins
     */
    private function publicAssetUrl(array $relativePaths): ?string
    {
        foreach ($relativePaths as $path) {
            if (is_string($path) && $path !== '' && file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return null;
    }

    private function sampleProjects(): Collection
    {
        return collect([
            (object) [
                'id' => null,
                'name' => 'برج سكني — حي الملقا',
                'progress_percentage' => 78,
                'status' => 'ongoing',
                'current_stage' => 'production_installation',
                'is_sample' => true,
            ],
            (object) [
                'id' => null,
                'name' => 'مجمع فيلات — الطريق الدائري',
                'progress_percentage' => 45,
                'status' => 'ongoing',
                'current_stage' => 'architect',
                'is_sample' => true,
            ],
            (object) [
                'id' => null,
                'name' => 'مستودعات لوجستية — المنطقة الصناعية',
                'progress_percentage' => 100,
                'status' => 'completed',
                'current_stage' => 'completed',
                'is_sample' => true,
            ],
        ]);
    }
}
