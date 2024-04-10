<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Book;

class StoreUpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $minTitle = Book::MIN_TITLE_LENGTH;
        $maxTitle = Book::MAX_TITLE_LENGTH;
        $minAuthor = Book::MIN_AUTHOR_LENGTH;
        $maxAuthor = Book::MAX_AUTHOR_LENGTH;

        return [
            "title" => [
                "required",
                "min:{$minTitle}",
                "max:{$maxTitle}",
            ],
            "author" => [
                "required",
                "min:{$minAuthor}",
                "max:{$maxAuthor}",
            ],
        ];
    }
}
