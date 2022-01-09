<?php
namespace Maduser\Laravel\ViewModel;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Maduser\Generic\Traits\SelfAwareClass;
use Maduser\Laravel\Support\Traits\CallSettersTrait;
use Maduser\Laravel\Support\Traits\ResponsableTrait;
use Throwable;

/**
 * Class ViewModel
 *
 * @package Previon\Base\Views
 */
class ViewModel implements Htmlable, Arrayable, Jsonable, JsonSerializable, Responsable
{
    use CallSettersTrait;
    use Macroable;
    use ResponsableTrait;
    use SelfAwareClass;

    protected $ignore = ['macros'];

    /**
     * Static object creation
     *
     * @param array       $properties
     *
     * @param string|null $view
     *
     * @return $this
     */
    public static function create(array $properties = [], string $view = null): ViewModel
    {
        return app(static::class)->view($view)->callSetters($properties);
    }

    /**
     * The string representation of the view model
     *
     * @return string
     * @throws Throwable
     */
//    public function __toString()
//    {
//        return $this->toHtml();
//    }
}
