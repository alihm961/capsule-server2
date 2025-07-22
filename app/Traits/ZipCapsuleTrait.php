<?php

namespace App\Traits;

use ZipArchive;

trait ZipCapsuleTrait
{
    public function createCapsuleZip($capsule, $zipFilePath)
    {
        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return false;
        }

        if ($capsule->image) {
            $imageData = base64_decode($capsule->image);
            if ($imageData !== false) {
                $zip->addFromString('image.jpg', $imageData);
            }
        }

        if ($capsule->audio) {
            $audioData = base64_decode($capsule->audio);
            if ($audioData !== false) {
                $zip->addFromString('audio.mp3', $audioData);
            }
        }

        $messageText = "Title: {$capsule->title}\nMessage: {$capsule->message}\nReveal Date: {$capsule->reveal_at}\nMood: {$capsule->mood->name}\nCountry: {$capsule->country->name}";
        $zip->addFromString('message.txt', $messageText);

        $zip->close();

        return true;
    }
}