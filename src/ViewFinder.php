<?php

namespace Maduser\Laravel\ViewModel;

use Closure;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\App;
use ReflectionClass;
use ReflectionException;

/**
 * Class ViewFinder (unused at the moment)
 * @todo: working, but needs refactoring. a lot.
 */
class ViewFinder
{
    /**
     * @var
     */
    protected $model;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $view;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var
     */
    protected $variant;

    /**
     * @var string
     */
    protected $suffix = '.blade.php';

    /**
     * @var string
     */
    protected $exposedAs;

    /**
     * @var array
     */
    protected $directoryAliases;

    /**
     * @var array
     */
    protected $alternatives = [];

    /**
     * @var array
     */
    protected $contexts = [];

    /**
     * @var array
     */
    protected $viewContexts = [];

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     *
     * @return ViewFinder
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDirectory()
    {
        return empty($this->directory) ? '' : trim($this->directory, '/') . '/';
    }

    /**
     * @param mixed $directory
     *
     * @return ViewFinder
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     *
     * @todo $this->getInputType() can't be here!!
     *
     * @return string
     */
    public function getView(): string
    {
        return $this->view ?? $this->getModel()->getClassBasenameSnaked('-');
    }

    /**
     * @param string $view
     *
     * @return ViewFinder
     */
    public function setView(string $view = null)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVariant()
    {
        return empty($this->variant) ? '' : '--' . $this->variant;
    }

    /**
     * @param string $variant
     *
     * @return ViewFinder
     */
    public function setVariant(string $variant)
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     *
     * @return ViewFinder
     */
    public function setSuffix(string $suffix): ViewFinder
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public function getExposedAs(): string
    {
        return $this->exposedAs ?? lcfirst(
            (new ReflectionClass($this->getModel()))->getShortName()
        );
    }

    /**
     * @param string $exposedAs
     *
     * @return ViewFinder
     */
    public function setExposedAs(string $exposedAs = null): ViewFinder
    {
        $this->exposedAs = $exposedAs;

        return $this;
    }

    /**
     * @return array
     */
    public function getDirectoryAliases(): array
    {
        if (!$this->directoryAliases) {

            $hints = collect(config('view-model.view.hints'));

            $this->directoryAliases =
                $hints->sortBy('priority', SORT_REGULAR, true)->keys()->toArray();
        }

        return $this->directoryAliases;
    }

    /**
     * @param array $directoryAliases
     *
     * @return ViewFinder
     */
    public function setDirectoryAliases(array $directoryAliases): ViewFinder
    {
        $this->directoryAliases = $directoryAliases;

        return $this;
    }

    /**
     * @return array
     */
    public function getAlternatives(): array
    {
        return $this->alternatives;
    }

    /**
     * @param array $alternatives
     *
     * @return ViewFinder
     */
    public function setAlternatives(array $alternatives): ViewFinder
    {
        $this->alternatives = $alternatives;

        return $this;
    }

    /**
     * @return array
     */
    public function getContexts(): array
    {
        return $this->contexts;
    }

    /**
     * @param array $contexts
     *
     * @return ViewFinder
     */
    public function setContexts(array $contexts): ViewFinder
    {
        $this->contexts = $contexts;

        return $this;
    }


    /**
     * @return array
     */
    public function getViewContexts(): array
    {
        return $this->viewContexts;
    }

    /**
     * @param array $viewContexts
     *
     * @return ViewFinder
     */
    public function setViewContexts(array $viewContexts): ViewFinder
    {
        $this->viewContexts = $viewContexts;

        return $this;
    }

    /**
     * @param $alias
     * @param $context
     *
     * @return string
     */
    public function withContextAndName($alias, $context)
    {
        $template = $alias
            . $this->getDirectory()
            . $this->getView()
            . $context
            . '--field-' . $this->getModel()->getName()
            . $this->getVariant();

        if (view()->exists($template)) {
            $this->template = $template;
        }

        return $template;
    }

    /**
     * @param $alias
     * @param $context
     *
     * @return string
     */
    public function withContext($alias, $context)
    {
        $template = $alias
            . $this->getDirectory()
            . $this->getView()
            . $context
            . $this->getVariant();

        if (view()->exists($template)) {
            $this->template = is_null($this->template) ? $template : $this->template;
        }

        return $template;
    }

    /**
     * @param $alias
     *
     * @return string
     */
    public function withName($alias)
    {
        $template = $alias
            . $this->getDirectory()
            . $this->getView()
            . $this->getVariant();

        if (view()->exists($template)) {
            $this->template = is_null($this->template) ? $template : $this->template;
        }

        return $template;
    }

    /**
     * @return array
     */
    public function getValidContexts(): array
    {
        if (empty($this->getContexts())) {
            return [];
        }

        $contexts = array_merge(
            array_intersect($this->getContexts(), $this->getViewContexts()),
            ['']
        );

        return $contexts;
    }

    /**
     * @return string
     * @throws FileNotFoundException
     */
    public function getTemplate(): string
    {
        $aliases = array_merge([''], $this->getDirectoryAliases());

        foreach ($aliases as $alias) {
            $alias = empty($alias) ? '' : $alias . '::';

            if ($contexts = $this->getValidContexts()) {
                foreach ($contexts as $context) {
                    $context = empty($context) ? '' : '--context-' . $context;

                    $this->alternatives[] = $this->withContextAndName($alias, $context);

                    $this->alternatives[] = $this->withContext($alias, $context);
                }
            }

            $this->alternatives[] = $this->withName($alias);
        }

        if (empty($this->template)) {
            throw new FileNotFoundException(
                'ViewModel ' . $this->getModel()->getClassBasename() .
                ' could not find the view to render'
            );
        }

        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @param Closure     $callable
     * @param string|null $view
     *
     * @return string
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    public function render(Closure $callable, string $view = null)
    {
        is_null($view) || $this->setView($view);

        $view = $this->getTemplate();

        // TODO: implement different behaviors for 'local' and 'production'
        // Somehow our app is never in 'local' mode, even if it is defined as
        // such in our .env file.
         if (! App::isLocal() ) {
             return $callable($view, $this->getExposedAs());
         }

        $str = "\n<!-- Start " . $view . $this->getSuffix() . ": -->\n";

        foreach ($this->alternatives as $alternative) {
            $indicator = $alternative === $this->template ? '[x]' : '[ ]';
            $str .= "<!-- " . $indicator . " " . $alternative . $this->getSuffix() . " -->\n";
        }

        $str .= $callable($view, $this->getExposedAs());

        $str .= "<!-- End " . $view . $this->getSuffix() . " -->\n";

        return $str;
    }


    /**
     * Static object creation
     *
     * @param ViewModel   $viewModel
     *
     * @param string|null $view
     * @param string|null $exposedAs
     *
     * @return $this
     */
    public static function create(ViewModel $viewModel, string $view = null, string $exposedAs = null): ViewFinder
    {
        return app(static::class)
            ->setModel($viewModel)
            ->setView($view)
            ->setExposedAs($exposedAs);
    }


    /**
     * @param string|null $view
     *
     * @return string
     * @throws FileNotFoundException
     */
    public function find(string $view = null)
    {
        if ($view) {
            $this->setView($view);
        }

        $template = $this->getTemplate();


        return '';
    }
}
