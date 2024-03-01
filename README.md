# maduser/laravel-viewmodel

The **maduser/laravel-viewmodel** package provides an elegant way to encapsulate data and logic needed for views in Laravel applications, promoting clean separation of concerns and reusable code. By using ViewModels, you can simplify your controllers and views, making your codebase more maintainable and understandable.

## Features
- **Flexible Responses:** ViewModel can automatically determine whether to render a view or return JSON based on the request, providing flexibility for APIs and web interfaces.
- **Encapsulation of Logic:** Keep your controller clean by moving presentation logic into ViewModels.
- **Ease of Use:** Implement ViewModel with minimal setup and use it seamlessly with Laravel's response handling.

## Installation
Install the package via composer:
```bash
composer require maduser/laravel-viewmodel
```

## Quick Start

### Creating a ViewModel
ViewModels are simple to define. Here's an example of a ViewModel that displays a quote:
```php 
use Maduser\Laravel\ViewModel\ViewModel;

class MyQuoteWidget extends ViewModel
{
    protected $view = 'my-widget'; // Blade template
    protected $quote; // Quote string

    public function getQuote(): ?string
    {
        return $this->quote;
    }

    public function setQuote(?string $quote): MyQuoteWidget
    {
        $this->quote = $quote;
        return $this;
    }
}
```

### Blade Template
Create a corresponding Blade template for your ViewModel. For the MyQuoteWidget ViewModel, the **my-widget.blade.php** file might look like this:
```html
<div class="widget quote">
    <p>{{ $view->getQuote() }}</p>
</div>
```

### Using ViewModel in a Controller
You can use the ViewModel in a controller to pass data to your view. Here's an example of how to use a **Page** ViewModel

```php
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maduser\Laravel\ViewModel\ViewModels\Page;

class ExampleController extends Controller
{
    private $exampleVars;

    public function __construct()
    {
        $this->exampleVars = [
            'title' => 'Welcome Home',
            'text' => 'An inspiring quote here'
        ];
    }

    public function showPage(): Responsable
    {
        // Creating and returning a ViewModel instance
        return Page::create($this->exampleVars);
    }
}

```

### Advanced Usage

#### Responsable Interface
ViewModels implement Laravel's **Responsable** interface, allowing them to be directly returned from controller methods. Depending on the request's **acceptable content types**, the response can be either the **rendered view or a JSON representation**.

To force a response type, you can use methods like render() or toJson().
To add more acceptable content types (for example pdf), use ViewModel::macro() in conjunction with Laravel Request::macro() and Response::macro().

#### Usefull Methods
- **__toString():** Automatically renders the view when the ViewModel is treated as a string.
- **toArray():** Returns an array representation of the ViewModel, useful for formating the structure of the JSON responses.
- **toJson():** Returns a JSON string representation of the ViewModel.

#### Nesting ViewModels
With the ViewModel ability to dynamically set properties, and its capacity to represent its data as an array, you can create nested structures

```php
$userWidget = UserWidget::create([
    'profile' => UserProfile::create(),
    'activity' => UserActivity::create(['activities' => $user->activities])
]);
```
