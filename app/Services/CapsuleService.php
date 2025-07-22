<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Capsule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\ApiResponse;
use Stevebauman\Location\Facades\Location;
use App\Models\Mood;
use App\Models\Country;
use Illuminate\Support\Carbon;
use App\Traits\ZipCapsuleTrait;

class CapsuleService
{
    use ApiResponse, ZipCapsuleTrait;

     function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'nullable|string',
                'image' => 'nullable|string',
                'audio' => 'nullable|string',
                'mood_id' => 'nullable|exists:moods,id',
                'is_public' => 'required|boolean',
                'is_surprise' => 'required|boolean',
                'reveal_at' => 'nullable|date',
            ]);

            $imagePath = null;
            $audioPath = null;

            if ($request->has('image') && $request->image) {
                $imageData = $request->image;
                
            if (strpos($imageData, 'base64,') !== false) {
                $imageData = explode('base64,', $imageData)[1];
            }
            
            $decodedImage = base64_decode($imageData);
            $imageName = 'capsules/images/' . uniqid() . '.png';
            Storage::disk('public')->put($imageName, $decodedImage);
            $imagePath = $imageName;
        }
        
        
            if ($request->has('audio') && $request->audio) {
                $audioData = $request->audio;
                
                
            if (strpos($audioData, 'base64,') !== false) {
                $audioData = explode('base64,', $audioData)[1];
            }
            
            $decodedAudio = base64_decode($audioData);
            $audioName = 'capsules/audio/' . uniqid() . '.mp3';
            Storage::disk('public')->put($audioName, $decodedAudio);
            $audioPath = $audioName;
        }

            $user = Auth::user();

            $ip = $request->ip();
            if ($ip === '127.0.0.1' || $ip === '::1') {
                $ip = '8.8.8.8';
            }

            $locationData = \Location::get($ip);
            $countryName = $locationData?->countryName ?? 'Unknown';
            $city = $locationData?->city ?? null;
            $location = $city ? "$city, $countryName" : $countryName;

            $country = \App\Models\Country::firstOrCreate([
                'name' => $countryName
            ]);

            $mood = isset($validated['mood_id'])
                ? Mood::where('id', $validated['mood_id'])->first()
                : null;

            if (!$mood) {
                return $this->responseJSON(null, 'Invalid mood', 422);
            }

            $capsule = Capsule::create([
                'user_id' => $user->id,
                'title' => $validated['title'],
                'message' => $validated['message'] ?? null,
                'image_path' => $imagePath,
                'audio_path' => $audioPath,
                'ip_address' => $ip,
                'location' => $location,
                'mood_id' => $mood->id,
                'country_id' => $country->id,
                'is_public' => $validated['is_public'],
                'is_surprise' => $validated['is_surprise'],
                'reveal_at' => $validated['reveal_at'] ?? null,
            ]);

            return $this->responseJSON($capsule, 'Capsule created successfully');
        } catch (\Throwable $e) {
            return $this->responseJSON(null, $e->getMessage(), 500);
        }
    }


     function getUserCapsules(Request $request)
{
    $user = Auth::user();

    $capsules = Capsule::with(['mood', 'country'])
        ->where('user_id', $user->id)
        ->latest()
        ->get();

    return $this->responseJSON($capsules, 'User capsules fetched successfully');
}

    

     function getPublicCapsules(Request $request)
{
    $capsules = Capsule::with(['mood', 'country'])
        ->where('is_public', true)
        ->when($request->has('mood') && $request->mood !== '', function ($query) use ($request) {
            $query->whereHas('mood', function ($q) use ($request) {
                $q->where('name', $request->mood);
            });
        })
        ->when($request->has('country') && $request->country !== '', function ($query) use ($request) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('name', $request->country);
            });
        })
        ->where(function ($query) {
            $query->whereNull('reveal_at')
                  ->orWhere('reveal_at', '<=', Carbon::now());
        })
        ->latest()
        ->get();

    return $this->responseJSON($capsules, 'Public capsules fetched successfully');
}


     function delete($id)
    {
        $capsule = Capsule::find($id);

        if (!$capsule) {
            return $this->responseJSON('Capsule not found', 404);
        }

        if ($capsule->user_id !== Auth::id()) {
            return $this->responseJSON('Unauthorized to delete this capsule', 403);
        }

        $capsule->delete();

        return $this->responseJSON('Capsule deleted successfully');
    }

     function getAllCountries() {

    $countries = Capsule::where('is_public', true)
        ->where(function ($query) {
            $query->whereNull('reveal_at')
                  ->orWhere('reveal_at', '<=', now());
        })
        ->with('country')
        ->get()
        ->pluck('country.name')
        ->unique()
        ->filter()
        ->values();

    return response()->json(['data' => $countries]);
}

    function downloadZip($id)
{
    try {
        $capsule = Capsule::with(['mood', 'country'])->findOrFail($id);

        $zipFileName = 'capsule_' . $capsule->id . '.zip';
        $zipFilePath = storage_path("app/public/zips/{$zipFileName}");

        if (!file_exists(storage_path('app/public/zips'))) {
            mkdir(storage_path('app/public/zips'), 0777, true);
        }

        $success = $this->createCapsuleZip($capsule, $zipFilePath);

        if (!$success) {
            return $this->responseJSON(null, 'Failed to create ZIP file', 500);
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        \Log::error('Download ZIP failed: ' . $e->getMessage());
        return response()->json(['message' => 'Internal server error'], 500);
    }
}

}

    