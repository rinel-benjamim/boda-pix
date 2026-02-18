<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class GenerateThumbnailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Media $media) {}

    public function handle(): void
    {
        if ($this->media->type !== 'image') {
            return;
        }

        $tempPath = sys_get_temp_dir() . '/' . basename($this->media->file_path);
        $thumbnailPath = sys_get_temp_dir() . '/thumb_' . basename($this->media->file_path);

        Storage::disk('s3')->download($this->media->file_path, $tempPath);

        $manager = ImageManager::gd();
        $image = $manager->read($tempPath);
        $image->scale(width: 400);
        $image->save($thumbnailPath);

        $s3Path = str_replace('/media/', '/thumbnails/', $this->media->file_path);
        Storage::disk('s3')->put($s3Path, file_get_contents($thumbnailPath));

        $this->media->update(['thumbnail_path' => $s3Path]);

        @unlink($tempPath);
        @unlink($thumbnailPath);
    }
}
