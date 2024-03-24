<?php

declare(strict_types=1);

namespace App\Services\ParsableContentProviders;

use App\Contracts\ParsableContentProvider;

final readonly class MentionProviderParsable implements ParsableContentProvider
{
    /**
     * {@inheritDoc}
     */
    public function parse(string $content): string
    {
        return (string) preg_replace_callback(
            '/(<a\s+[^>]*>.*?<\/a>)|@([^\s,.?!\/@<]+)/i',
            fn (array $matches): string => $matches[1] !== ''
                ? $matches[1]
                : '<a href="/@'.$matches[2].'" class="text-blue-500 hover:underline hover:text-blue-700 cursor-pointer" wire-navigate>@'.$matches[2].'</a>',
            $content
        );
    }
}
