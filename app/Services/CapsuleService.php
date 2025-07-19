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

class CapsuleService
{
    use ApiResponse;

    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'nullable|string',
                'image' => 'nullable|string',
                'audio' => 'nullable|string',
                'mood_id' => 'nullable|string',
                'country_id' => 'nullable|string',
                'is_public' => 'required|boolean',
                'is_surprise' => 'required|boolean',
                'reveal_at' => 'nullable|date',
            ]);

            $imagePath = null;
            $audioPath = null;

            if ($request->has('image') && $request->image) {
                $imageData = base64_decode($request->image);
                $imageName = 'capsules/images/' . uniqid() . '.png';
                Storage::disk('public')->put($imageName, $imageData);
                $imagePath = $imageName;
            }

            if ($request->has('audio') && $request->audio) {
                $audioData = base64_decode($request->audio);
                $audioName = 'capsules/audio/' . uniqid() . '.mp3';
                Storage::disk('public')->put($audioName, $audioData);
                $audioPath = $audioName;
            }

            $user = Auth::user();

            $ip = $request->ip();
            $locationData = Location::get($ip);
            $location = $locationData ? ($locationData->city . ', ' . $locationData->countryName) : 'Unknown';
            $mood = Mood::where('name', $validated['mood_id'])->first();
            $country = Country::where('name', $validated['country_id'])->first();

            if(!$mood || !$country) {
                return $this->responseJSON(null, 'Invalid mood or country name', 422);
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

            return $this->responseJSON('Capsule created successfully', $capsule);
        } catch (\Exception $e) {
            return $this->responseJSON($e->getMessage(), 500);
        }
    }

    public function getUserCapsules(Request $request)
    {
        $user = Auth::user();
        $capsules = Capsule::where('user_id', $user->id)->latest()->get();

        return $this->responseJSON('User capsules fetched successfully', $capsules);
    }

    public function getPublicCapsules(Request $request)
    {
        $user = Auth::user();
        $capsules = Capsule::where('is_public', true)
            ->where(function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id)
                      ->orWhereNull('user_id');
            })
            ->latest()
            ->get();

        return $this->responseJSON('Public capsules fetched successfully', $capsules);
    }

    public function delete($id)
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
}