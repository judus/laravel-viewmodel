# maduser/laravel-viewmodel

#### Get it with composer
```bash
composer require maduser/laravel-viewmodel
```

#### Example ViewModel
```php 
use Maduser\Laravel\ViewModel\ViewModel;

class MyQuoteWidget extends ViewModel
{
    /**
     * The blade template to render
     *
     * @var string
     */
    protected $view = 'my-widget';

    /**
     * @var string|null
     */
    protected $quote;

    /**
     * @return string|null
     */
    public function getQuote(): ?string
    {
        return $this->quote;
    }

    /**
     * @param string|null $quote
     *
     * @return MyQuoteWidget
     */
    public function setQuote(?string $quote): MyQuoteWidget
    {
        $this->quote = $quote;

        return $this;
    }
}
```
#### Example template my-widget.blade.php
```html
<div class="widget quote">
    <p>{{ $view->getQuote() }}</p>
</div>
```

### Example controller
Assume we have a ViewModel "Page" with the properties $title and $text
```php
class ExampleController extends Controller
{
    /**
     * @var array
     */
    private $exampleVars;

    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        $this->exampleVars = [
            'title' => 'Welcome Home',
            'text' => Inspiring::quote()
        ];
    }

    /**
     * ViewModels implements laravel's Responsable
     * Depending on the request acceptable content types, response will be the
     * either the rendered view defined in the ViewModel or the json
     * representation.
     *
     * If you want to force the response type, use the ViewModels methods
     * render(), toArray() or toJson() etc...
     *
     * To add more acceptable content types use:
     * ViewModel::macro(), Request::macro(), Response::macro()
     *
     * As per default it executes:
     * toResponse(toHtml(render(new HtmlString(view()))))
     *
     * @return Responsable|JsonResponse|Response
     */
    public function object(): Responsable
    {
        return Page::create($this->exampleVars);
    }

    /**
     * This is the same as returning the object without calling toResponse(),
     *
     * @param Request $request
     *
     * @return Responsable|JsonResponse|Response
     * @throws Throwable
     */
    public function toResponse(Request $request)
    {
        return Page::create($this->exampleVars)->toResponse($request);
    }

    /**
     * ViewModels implements __toString:
     * Response will be the rendered view defined in the ViewModel
     *
     * As per default it executes:
     * __toString(toHtml(render(new HtmlString(view()))))
     *
     * @return string
     */
    public function cast(): string
    {
        return (string)Page::create($this->exampleVars);
    }

    /**
     * The render function returns a Htmlable which is stringable:
     * Response will be the rendered view defined in the ViewModel
     *
     * As per default it executes:
     * render(new HtmlString(view()))
     *
     * @return Htmlable
     * @throws Throwable
     */
    public function render(): Htmlable
    {
        return Page::create($this->exampleVars)->render();
    }

    /**
     * ViewModels implement Arrayable:
     * Response will be the json representation of toArray()
     *
     * As per default it executes:
     * toArray(collect())
     *
     * @return array
     */
    public function toArray(): array
    {
        return Page::create($this->exampleVars)->toArray();
    }

    /**
     * ViewModels implement Jsonable:
     * Response will be the json string of toJson()
     * Executes: toJson(toArray(collect()))
     *
     * @return string
     */
    public function toJson(): string
    {
        return Page::create($this->exampleVars)->toJson();
    }

}
```
