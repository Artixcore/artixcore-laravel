<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Media\CreateMediaAssetFromUpload;
use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MediaAdminController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', MediaAsset::class);

        return view('admin.media.index', [
            'assets' => MediaAsset::query()->orderByDesc('id')->paginate(24),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', MediaAsset::class);
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'alt_text' => ['nullable', 'string', 'max:500'],
        ]);

        app(CreateMediaAssetFromUpload::class)->execute(
            $request->file('file'),
            $request->user(),
            $request->input('alt_text')
        );

        return redirect()->back()->with('status', 'File uploaded.');
    }

    public function destroy(Request $request, MediaAsset $media_asset): RedirectResponse
    {
        $this->authorize('delete', $media_asset);
        Storage::disk($media_asset->disk)->delete($media_asset->path);
        $media_asset->delete();

        return redirect()->back()->with('status', 'Media deleted.');
    }
}
