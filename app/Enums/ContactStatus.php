<?php

namespace App\Enums;

enum ContactStatus: string
{
    case New = 'new';
    case Read = 'read';
    case Replied = 'replied';
    case Closed = 'closed';
    case Handled = 'handled';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
