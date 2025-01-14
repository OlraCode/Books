<?php

namespace App\MessageHandler;

use App\Message\DeleteBookMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteBookMessageHandler
{
    public function __construct(
        private ParameterBagInterface $parameter,
        private Filesystem $filesystem,
    ) {
    }

    public function __invoke(DeleteBookMessage $message): void
    {
        $book = $message->book;

        if ($book->getCoverPath()) {
            $path = $this->parameter->get('app.cover_image_directory') . '/' . $book->getCoverPath();
            $this->filesystem->remove($path);
        }
    }
}
