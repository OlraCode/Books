<?php

namespace App\Message;

use App\Entity\Book;

final class DeleteBookMessage
{
    public function __construct(
        public readonly Book $book,
    ) {
    }
}
