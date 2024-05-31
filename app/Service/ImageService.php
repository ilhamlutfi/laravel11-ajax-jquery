<?php

namespace App\Service;

use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function uploadImg(array $data, string $oldImage = null)
    {
       $img = $data['image'];
       $img->store('images', 'public');

       if ($oldImage) {
           Storage::disk('public')->delete('images/' . $oldImage);
       }

       return $img->hashName();
    }
}
