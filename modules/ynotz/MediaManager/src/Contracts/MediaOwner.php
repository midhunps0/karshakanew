<?php
namespace Ynotz\MediaManager\Contracts;

use Illuminate\Support\Collection;
use PhpParser\ErrorHandler\Collecting;
use Ynotz\MediaManager\Models\MediaItem;

interface MediaOwner
{
    public function attachMedia(
        MediaItem $mediaItem,
        string $property,
        array $customProps = []
    ): void;

    public function addMediaFromEAInput(
        string $property,
        array|string $vals
    ): void;

    public function getMediaVariants(): array;

    public function getMediaStorage(): array;

    public function getAllMedia(string $property): Collection;

    public function getSingleMedia(string $property): MediaItem;

    public function getSingleMediaPath(string $property): string;

    public function getSingleMediaName(string $property): string;
}
?>
