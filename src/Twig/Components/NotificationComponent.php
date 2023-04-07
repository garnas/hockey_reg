<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('notification')]
final class NotificationComponent
{
    public string $message;
}
