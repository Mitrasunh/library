<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowedBook;
use App\Models\Member;
use Illuminate\Http\Request;

class BorrowBookController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/borrow",
     *     tags={"Borrow"},
     *     summary="Borrow a book",
     *     description="Member borrows a book",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="member_code", type="string"),
     *             @OA\Property(property="book_code", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book borrowed successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Member is penalized or other error"
     *     )
     * )
     */
    public function borrow(Request $request)
    {
        $request->validate([
            'member_code' => 'required|exists:members,code',
            'book_code' => 'required|exists:books,code',
        ]);

        $member = Member::where('code', $request->member_code)->firstOrFail();
        $book = Book::where('code', $request->book_code)->firstOrFail();

        // Check if member is penalized
        $penalizedUntil = $member->penalized_until;
        if ($penalizedUntil && now()->lessThan($penalizedUntil)) {
            return response()->json(['message' => 'You are currently penalized and cannot borrow books'], 403);
        }

        // Check if member already borrowed 2 books
        if ($member->borrowedBooks()->whereNull('returned_at')->count() >= 2) {
            return response()->json(['message' => 'Cannot borrow more than 2 books'], 403);
        }

        // Check if the book is already borrowed
        if ($book->borrowedBooks()->whereNull('returned_at')->exists()) {
            return response()->json(['message' => 'Book is already borrowed'], 403);
        }

        // Borrow the book
        BorrowedBook::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        return response()->json([
            'member_code' => $member->code,
            'book_code' => $book->code,
            'message' => 'Book borrowed successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/return",
     *     tags={"Return"},
     *     summary="Return a borrowed book",
     *     description="Member returns a borrowed book",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="member_code", type="string", example="M001"),
     *             @OA\Property(property="book_code", type="string", example="JK-45")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book returned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="member_code", type="string", example="M001"),
     *             @OA\Property(property="book_code", type="string", example="JK-45"),
     *             @OA\Property(property="message", type="string", example="Book returned successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Error returning book"
     *     )
     * )
     */
    public function return(Request $request)
    {
        $request->validate([
            'member_code' => 'required|exists:members,code',
            'book_code' => 'required|exists:books,code',
        ]);

        $member = Member::where('code', $request->member_code)->firstOrFail();
        $book = Book::where('code', $request->book_code)->firstOrFail();

        $borrowedBook = BorrowedBook::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->firstOrFail();

        // Check if book is returned late (more than 7 days)
        $isLate = now()->diffInDays($borrowedBook->borrowed_at) > 7;
        if ($isLate) {
            // Apply penalty
            $penaltyEndDate = now()->addDays(3); // 3 days penalty period
            $member->penalized_until = $penaltyEndDate;
            $member->save();

            // Mark the borrowed book as penalized
            $borrowedBook->penalized = true;
        }

        $borrowedBook->returned_at = now();
        $borrowedBook->save();

        return response()->json([
            'member_code' => $member->code,
            'book_code' => $book->code,
            'message' => 'Book returned successfully'
        ]);
    }
}
