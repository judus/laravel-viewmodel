<?php

namespace Maduser\Laravel\ViewModel;

use Illuminate\Contracts\Support\Htmlable;
use Maduser\Laravel\Support\Collections\TypedCollection;

class ViewCollection extends TypedCollection implements Htmlable
{
    /**
     * @var array
     */
    protected static $allowedTypes = [Htmlable::class];

    /**
     * @inheritDoc
     */
    public function toHtml()
    {
        // TODO: Implement toHtml() method.
    }
}
