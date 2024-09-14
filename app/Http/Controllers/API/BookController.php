<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowedBook;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/books",
     *     tags={"Books"},
     *     summary="Get all available books",
     *     description="Returns a list of all books with their available quantities (excluding borrowed books)",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="code", type="string", example="JK-45"),
     *                 @OA\Property(property="title", type="string", example="Harry Potter"),
     *                 @OA\Property(property="author", type="string", example="J.K Rowling"),
     *                 @OA\Property(property="available_stock", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        // Fetch books that are not currently being borrowed
        $books = Book::all()->filter(function ($book) {
            // Check if the book is currently borrowed
            $isBorrowed = BorrowedBook::where('book_id', $book->id)
                ->whereNull('returned_at')
                ->exists();

            // If the book is not borrowed, include it in the response
            return !$isBorrowed;
        })->map(function ($book) {
            return [
                'code' => $book->code,
                'title' => $book->title,
                'author' => $book->author,
                'available_stock' => $book->stock,
            ];
        });

        return response()->json($books);
    }
}
