<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Capsule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\ApiResponse;

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
                'ip_address' => 'nullable|string',
                'location' => 'nullable|string',
                'mood_id' => 'nullable|exists:moods,id',
                'country_id' => 'nullable|exists:countries,id',
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

            $capsule = Capsule::create([
                'user_id'     => $user->id,
                'title'       => $validated['title'],
                'message'     => $validated['message'] ?? null,
                'image_path'  => $imagePath,
                'audio_path'  => $audioPath,
                'ip_address'  => $validated['ip_address'] ?? null,
                'location'    => $validated['location'] ?? null,
                'mood_id'     => $validated['mood_id'] ?? null,
                'country_id'  => $validated['country_id'] ?? null,
                'is_public'   => $validated['is_public'],
                'is_surprise' => $validated['is_surprise'],
                'reveal_at'   => $validated['reveal_at'] ?? null,
            ]);

            return $this->responseJSON('Capsule created successfully', $capsule);

        } catch (\Exception $e) {
            return $this->responseJSON( $e->getMessage(), 'Capsule creation failed', 500);
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