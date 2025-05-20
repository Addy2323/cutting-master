<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Appointment $appointment)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        // Check if the user has already reviewed this appointment
        if ($appointment->review) {
            return response()->json([
                'message' => 'You have already reviewed this appointment'
            ], 422);
        }

        // Check if the appointment is completed
        if ($appointment->status !== 'completed') {
            return response()->json([
                'message' => 'You can only review completed appointments'
            ], 422);
        }

        $review = Review::create([
            'appointment_id' => $appointment->id,
            'user_id' => Auth::id(),
            'employee_id' => $appointment->employee_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_verified' => true,
            'is_public' => true
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review
        ]);
    }

    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json([
            'message' => 'Review updated successfully',
            'review' => $review
        ]);
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        
        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully'
        ]);
    }
} 