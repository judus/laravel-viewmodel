<?php
namespace Maduser\Laravel\ViewModel;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Maduser\Laravel\Support\Traits\CallSettersTrait;
use Maduser\Laravel\Support\Traits\ResponsableTrait;
use Throwable;

/**
 * Class ViewModel
 *
 * @package Maduser\Laravel\ViewModel
 */
class ViewModel implements Htmlable, Arrayable, Jsonable, JsonSerializable, Responsable
{
    use CallSettersTrait;
    use Macroable;
    use ResponsableTrait;

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
        /** @var ViewModel $viewModel */
        $viewModel = app(static::class);
        return $viewModel->view($view)->callSetters($properties);
    }

    /**
     * The string representation of the view model
     *
     * @return string
     * @throws Throwable
     */
    public function __toString()
    {
        return $this->toHtml();
    }
}
