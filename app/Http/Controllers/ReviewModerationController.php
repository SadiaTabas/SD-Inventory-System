<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\ActivityLog;

class ReviewModerationController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductReview::with(['product', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reviews = $query->paginate(15)->withQueryString();

        return view('reviews.index', compact('reviews'));
    }

    public function updateStatus(Request $request, ProductReview $review)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $review->update(['status' => $request->status]);

        ActivityLog::log('Review Moderated', 'ProductReview', $review->id, "Review status set to {$request->status}");

        return back()->with('success', 'Review status updated.');
    }

    public function destroy(ProductReview $review)
    {
        ActivityLog::log('Review Deleted', 'ProductReview', $review->id, "Review deleted.");
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
