<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StringFormatterExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_custom_field', [$this, 'formatCustomField']),
        ];
    }

    public function formatCustomField(?string $fieldName): string
    {
        if (!$fieldName) {
            return '';
        }

        // Supprime l'ID numérique à la fin (_XXXX)
        $formattedName = preg_replace('/_\d+$/', '', $fieldName);

        // Remplace les underscores par des espaces
        $formattedName = str_replace('_', ' ', $formattedName);

        // Retourne avec la première lettre en majuscule
        return ucfirst($formattedName);
    }
}
