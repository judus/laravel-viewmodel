<?php

namespace Maduser\Laravel\ViewModel;

use Illuminate\Support\HtmlString;
use Maduser\Laravel\Support\Collections\TypedCollection;

class RenderableCollection extends ViewCollection
{
    /**
     * @var string
     */
    protected $wrapper = 'div';

    /**
     * @var bool
     */
    protected $shouldWrap = false;

    /**
     * @var array
     */
    protected $attributes = [
        'class' => ['container-wrapper']
    ];

    /**
     * @var string
     */
    protected $itemWrapper = 'div';

    /**
     * @var bool
     */
    protected $shouldWrapItems = false;

    /**
     * @var array
     */
    protected $itemAttributes = [
        'class' => ['container-item-wrapper']
    ];

    /**
     * @param string $wrapper
     *
     * @return Container
     */
    public function setWrapper(string $wrapper): Container
    {
        $this->wrapper = $wrapper;

        return $this;
    }

    /**
     * @param bool $shouldWrap
     *
     * @return Container
     */
    public function setShouldWrap(bool $shouldWrap): Container
    {
        $this->shouldWrap = $shouldWrap;

        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return Container
     */
    public function setAttributes(array $attributes): Container
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $itemWrapper
     *
     * @return Container
     */
    public function setItemWrapper(string $itemWrapper): Container
    {
        $this->itemWrapper = $itemWrapper;

        return $this;
    }

    /**
     * @param bool $shouldWrapItems
     *
     * @return Container
     */
    public function setShouldWrapItems(bool $shouldWrapItems): Container
    {
        $this->shouldWrapItems = $shouldWrapItems;

        return $this;
    }

    /**
     * @param array $itemAttributes
     *
     * @return Container
     */
    public function setItemAttributes(array $itemAttributes): Container
    {
        $this->itemAttributes = $itemAttributes;

        return $this;
    }

    /**
     * @param array $items
     * @param array $properties
     *
     * @return $this
     */
    public static function create(array $items = [], array $properties = [])
    {
        extract($properties);
        $instance = new static($items);

        isset($wrap) && $instance->setShouldWrap($wrap);
        isset($wrapper) && $instance->setWrapper($wrapper);
        isset($attributes) && $instance->setAttributes($attributes);

        isset($wrapItems) && $instance->setShouldWrapItems($wrapItems);
        isset($itemWrapper) && $instance->setItemWrapper($itemWrapper);
        isset($itemAttributes) && $instance->setItemAttributes($itemAttributes);

        return $instance;
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    public function getAttributes(array $attributes): string
    {
        $items = [];
        foreach ($attributes as $attribute => $values) {
            $items[] = sprintf(' %s="%s"', $attribute, implode(' ', $values));
        }

        return implode(' ', $items);
    }

    /**
     * @return string
     */
    public function toHtml(): string
    {
        $string = '';

        foreach ($this->items as $item) {
            if ($this->shouldWrapItems) {
                $string .= $this->getWrapped(
                    $item->render(), $this->itemAttributes
                );
            } else {
                $string .= $item->render();
            }
        }

        if ($this->shouldWrap) {
            $string = $this->getWrapped($string, $this->attributes);
        }

        return new HtmlString($string);
    }

    /**
     * @param string $string
     * @param array  $attributes
     *
     * @return string
     */
    protected function getWrapped(string $string, array $attributes = []): string
    {
        return strtr('<{tag}{attr}>{string}</{tag}>', [
            '{tag}' => $this->wrapper,
            '{attr}' => $this->getAttributes($attributes),
            '{string}' => $string
        ]);
    }
}
