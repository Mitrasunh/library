<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/members",
     *     tags={"Members"},
     *     summary="Get all members and their borrowed book count",
     *     description="Returns a list of all members with the number of books they are currently borrowing",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="code", type="string", example="M001"),
     *                 @OA\Property(property="name", type="string", example="Angga"),
     *                 @OA\Property(property="borrowed_books_count", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        // Get all members and count the number of books each member is currently borrowing
        $members = Member::all()->map(function ($member) {
            $borrowedBooksCount = $member->borrowedBooks()
                ->whereNull('returned_at')
                ->count();

            return [
                'code' => $member->code,
                'name' => $member->name,
                'borrowed_books_count' => $borrowedBooksCount,
            ];
        });

        return response()->json($members);
    }
}
