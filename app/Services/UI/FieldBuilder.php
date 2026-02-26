<?php

namespace Intranet\Services\UI;

use Collective\Html\FormBuilder as CollectiveFormBuilder;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator;

/**
 * Builder compatible amb `Field::*` per desacoblar Styde Html.
 */
class FieldBuilder
{
    /**
     * @var \Collective\Html\FormBuilder
     */
    private CollectiveFormBuilder $form;

    /**
     * @var \Illuminate\Translation\Translator
     */
    private Translator $lang;

    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    private ViewFactory $view;

    /**
     * @var array<string, string>
     */
    private array $abbreviations = [];

    /**
     * @var array<string, string>
     */
    private array $cssClasses = [];

    /**
     * @var array<string, string>
     */
    private array $templates = [];

    /**
     * @param \Collective\Html\FormBuilder $form
     * @param \Illuminate\Translation\Translator $lang
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function __construct(CollectiveFormBuilder $form, Translator $lang, ViewFactory $view)
    {
        $this->form = $form;
        $this->lang = $lang;
        $this->view = $view;
    }

    /**
     * @param array<string, string> $abbreviations
     */
    public function setAbbreviations(array $abbreviations): void
    {
        $this->abbreviations = $abbreviations;
    }

    /**
     * @param array<string, string> $cssClasses
     */
    public function setCssClasses(array $cssClasses): void
    {
        $this->cssClasses = $cssClasses;
    }

    /**
     * @param array<string, string> $templates
     */
    public function setTemplates(array $templates): void
    {
        $this->templates = $templates;
    }

    /**
     * Redirigix qualsevol mètode desconegut a un build dinàmic per tipus.
     *
     * @param string $method
     * @param array<int, mixed> $parameters
     */
    public function __call(string $method, array $parameters): string
    {
        return (string) call_user_func_array([$this, 'build'], array_merge([$method], $parameters));
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param array<int|string, mixed> $attributes
     * @param array<int|string, mixed> $extra
     */
    public function text(string $name, mixed $value = null, array $attributes = [], array $extra = []): string
    {
        return $this->build('text', $name, $value, $attributes, $extra);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param array<int|string, mixed> $attributes
     * @param array<int|string, mixed> $extra
     */
    public function textarea(string $name, mixed $value = null, array $attributes = [], array $extra = []): string
    {
        return $this->build('textarea', $name, $value, $attributes, $extra);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param array<int|string, mixed> $attributes
     */
    public function hidden(string $name, mixed $value = null, array $attributes = []): string
    {
        return (string) $this->form->input('hidden', $name, $value, $attributes);
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $attributes
     * @param array<int|string, mixed> $extra
     */
    public function file(string $name, array $attributes = [], array $extra = []): string
    {
        return $this->build('file', $name, null, $attributes, $extra);
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $options
     * @param mixed $selected
     * @param array<int|string, mixed> $attributes
     * @param array<int|string, mixed> $extra
     */
    public function select(string $name, array $options = [], mixed $selected = null, array $attributes = [], array $extra = []): string
    {
        if (is_array($selected) && empty($attributes)) {
            $extra = $attributes;
            $attributes = $selected;
            $selected = null;
        }

        return $this->doBuild('select', $name, $selected, $attributes, $extra, $options);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param mixed $selected
     * @param array<int|string, mixed> $attributes
     * @param array<int|string, mixed> $extra
     */
    public function checkbox(string $name, mixed $value = 1, mixed $selected = null, array $attributes = [], array $extra = []): string
    {
        return $this->build('checkbox', $name, $selected, $attributes, $extra, $value);
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $options
     * @param mixed $selected
     * @param array<int|string, mixed> $attributes
     * @param array<int|string, mixed> $extra
     */
    public function checkboxes(string $name, array $options = [], mixed $selected = null, array $attributes = [], array $extra = []): string
    {
        return $this->doBuild('checkboxes', $name, $selected, $attributes, $extra, $options);
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $options
     * @param mixed $selected
     * @param array<int|string, mixed> $attributes
     * @param array<int|string, mixed> $extra
     */
    public function radios(string $name, array $options = [], mixed $selected = null, array $attributes = [], array $extra = []): string
    {
        return $this->doBuild('radios', $name, $selected, $attributes, $extra, $options);
    }

    /**
     * @param string $type
     * @param string $name
     * @param mixed $value
     * @param array<int|string, mixed> $attributes
     * @param array<int|string, mixed> $extra
     * @param array<int|string, mixed>|null $options
     */
    private function build(
        string $type,
        string $name,
        mixed $value = null,
        array $attributes = [],
        array $extra = [],
        ?array $options = null
    ): string {
        if (is_array($value)) {
            $extra = $attributes;
            $attributes = $value;
            $value = null;
        }

        return $this->doBuild($type, $name, $value, $attributes, $extra, $options);
    }

    /**
     * @param string $type
     * @param string $name
     * @param mixed $value
     * @param array<int|string, mixed> $attributes
     * @param array<int|string, mixed> $extra
     * @param array<int|string, mixed>|null $options
     */
    private function doBuild(
        string $type,
        string $name,
        mixed $value = null,
        array $attributes = [],
        array $extra = [],
        ?array $options = null
    ): string {
        $attributes = $this->replaceAttributes($attributes);

        if (!$this->checkAccess($attributes)) {
            return '';
        }

        $required = $this->getRequired($attributes);
        $label = $this->getLabel($name, $attributes);
        $htmlName = $this->getHtmlName($name);
        $id = $this->getHtmlId($name, $attributes);
        $errors = $this->getControlErrors($name);
        $hasErrors = !empty($errors);
        $customTemplate = $this->getCustomTemplate($attributes);

        $attributes = $this->getHtmlAttributes($type, $attributes, $errors, $id);
        $input = $this->buildControl($type, $name, $value, $attributes, $options, $htmlName, $hasErrors);

        $template = $this->resolveFieldTemplate($customTemplate, $this->getDefaultTemplate($type));

        return (string) $this->view->make($template, array_merge($extra, compact(
            'htmlName',
            'id',
            'label',
            'input',
            'errors',
            'hasErrors',
            'required'
        )))->render();
    }

    /**
     * @param string|null $customTemplate
     * @param string $defaultTemplate
     */
    private function resolveFieldTemplate(?string $customTemplate, string $defaultTemplate): string
    {
        if (!empty($customTemplate)) {
            $normalized = str_replace('/', '.', $customTemplate);
            if ($this->view->exists($normalized)) {
                return $normalized;
            }
        }

        return 'themes.bootstrap.fields.'.$defaultTemplate;
    }

    /**
     * @param string $type
     */
    private function getDefaultTemplate(string $type): string
    {
        return $this->templates[$type] ?? 'default';
    }

    /**
     * @param array<int|string, mixed> $attributes
     */
    private function getCustomTemplate(array $attributes): ?string
    {
        return isset($attributes['template']) ? (string) $attributes['template'] : null;
    }

    /**
     * @param string $name
     */
    private function getHtmlName(string $name): string
    {
        if (strpos($name, '.') !== false) {
            $segments = explode('.', $name);
            return array_shift($segments).'['.implode('][', $segments).']';
        }

        return $name;
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $attributes
     */
    private function getHtmlId(string $name, array $attributes): string
    {
        if (isset($attributes['id'])) {
            return (string) $attributes['id'];
        }

        return str_replace(['.', '[', ']'], ['_', '_', ''], $name);
    }

    /**
     * @param array<int|string, mixed> $attributes
     */
    private function getRequired(array $attributes): bool
    {
        return in_array('required', $attributes, true);
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $attributes
     */
    private function getLabel(string $name, array $attributes = []): string
    {
        if (isset($attributes['label'])) {
            return (string) $attributes['label'];
        }

        $attribute = 'validation.attributes.'.$name;
        $label = $this->lang->get($attribute);

        if ($label === $attribute) {
            $label = str_replace(['_', '.'], ' ', $name);
        }

        return ucfirst($label);
    }

    /**
     * @param string $type
     */
    private function getDefaultClasses(string $type): string
    {
        if (isset($this->cssClasses[$type])) {
            return (string) $this->cssClasses[$type];
        }

        return (string) ($this->cssClasses['default'] ?? '');
    }

    /**
     * @param string $type
     * @param array<int|string, mixed> $attributes
     * @param array<int, string> $errors
     */
    private function getClasses(string $type, array $attributes = [], array $errors = []): string
    {
        $classes = trim($this->getDefaultClasses($type));

        if (isset($attributes['class']) && trim((string) $attributes['class']) !== '') {
            $classes = trim($classes.' '.(string) $attributes['class']);
        }

        if (!empty($errors)) {
            $classes = trim($classes.' '.($this->cssClasses['error'] ?? 'error'));
        }

        return $classes;
    }

    /**
     * @param string $name
     * @return array<int, string>
     */
    private function getControlErrors(string $name): array
    {
        $errors = session('errors');
        if ($errors === null) {
            return [];
        }

        $normalized = str_replace(['[', ']'], ['.', ''], $name);
        return $errors->get($normalized, []);
    }

    /**
     * @param string $type
     * @param array<int|string, mixed> $attributes
     * @param array<int, string> $errors
     */
    private function getHtmlAttributes(string $type, array $attributes, array $errors, string $htmlId): array
    {
        $attributes['class'] = $this->getClasses($type, $attributes, $errors);
        $attributes['id'] = $htmlId;

        unset($attributes['template'], $attributes['label'], $attributes['roles']);

        return $attributes;
    }

    /**
     * @param array<int|string, mixed> $attributes
     */
    private function replaceAttributes(array $attributes): array
    {
        foreach ($this->abbreviations as $abbreviation => $attribute) {
            if (isset($attributes[$abbreviation])) {
                $attributes[$attribute] = $attributes[$abbreviation];
                unset($attributes[$abbreviation]);
            }
        }

        return $attributes;
    }

    /**
     * @param array<int|string, mixed> $attributes
     */
    private function checkAccess(array $attributes): bool
    {
        if (!isset($attributes['roles'])) {
            return true;
        }

        if (!function_exists('authUser') || !function_exists('esRol')) {
            return true;
        }

        $user = authUser();
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        $roles = $attributes['roles'];
        if (!is_array($roles)) {
            $roles = explode('|', (string) $roles);
        }

        foreach ($roles as $role) {
            if (esRol((int) $user->rol, (int) $role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $type
     * @param string $name
     * @param mixed $value
     * @param array<int|string, mixed> $attributes
     * @param array<int|string, mixed>|null $options
     * @param string $htmlName
     */
    private function buildControl(
        string $type,
        string $name,
        mixed $value,
        array $attributes,
        ?array $options,
        string $htmlName,
        bool $hasErrors = false
    ): string {
        switch ($type) {
            case 'password':
            case 'file':
                return (string) $this->form->{$type}($htmlName, $attributes);
            case 'select':
                return (string) $this->form->select(
                    $htmlName,
                    $this->addEmptyOption($name, $this->getOptionsList($name, $options ?? []), $attributes),
                    $value,
                    $attributes
                );
            case 'radios':
                return (string) $this->renderRadioCollection($htmlName, $this->getOptionsList($name, $options ?? []), $value, $attributes);
            case 'checkboxes':
                return (string) $this->renderCheckboxCollection(
                    $htmlName,
                    $this->getOptionsList($name, $options ?? []),
                    $value,
                    $attributes,
                    $hasErrors
                );
            case 'checkbox':
                return (string) $this->form->checkbox($htmlName, $options[0] ?? 1, $value, $attributes);
            default:
                return (string) $this->form->{$type}($htmlName, $value, $attributes);
        }
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $options
     */
    private function getOptionsList(string $name, array $options): array
    {
        if (!empty($options)) {
            return $options;
        }

        return $this->getOptionsFromModel($name);
    }

    /**
     * @param string $name
     */
    private function getOptionsFromModel(string $name): array
    {
        $model = $this->form->getModel();
        if ($model === null) {
            return [];
        }

        $method = 'get'.Str::studly($name).'Options';
        if (method_exists($model, $method)) {
            $result = $model->{$method}();
            return is_array($result) ? $result : [];
        }

        return [];
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $options
     * @param array<int|string, mixed> $attributes
     */
    private function addEmptyOption(string $name, array $options, array &$attributes): array
    {
        if (empty($options)) {
            return [];
        }

        if (isset($attributes['multiple']) || in_array('multiple', $attributes, true)) {
            return $options;
        }

        if (isset($attributes['empty'])) {
            $text = (string) $attributes['empty'];
            unset($attributes['empty']);
        } else {
            $text = $this->getEmptyOption($name);
        }

        if ($text === false) {
            return $options;
        }

        return ['' => $text] + $options;
    }

    /**
     * @param string $name
     */
    private function getEmptyOption(string $name): string|false
    {
        $emptyText = $this->lang->get("validation.empty_option.$name");
        if ($emptyText !== "validation.empty_option.$name") {
            return $emptyText;
        }

        $emptyText = $this->lang->get('validation.empty_option.default');
        if ($emptyText !== 'validation.empty_option.default') {
            return $emptyText;
        }

        return '';
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $options
     * @param mixed $selected
     * @param array<int|string, mixed> $attributes
     */
    private function renderRadioCollection(string $name, array $options, mixed $selected, array $attributes): string
    {
        $radios = [];
        foreach ($options as $value => $label) {
            $valueAsString = (string) $value;
            $radios[] = [
                'name' => $name,
                'value' => $valueAsString,
                'id' => $attributes['id'].'_'.$valueAsString,
                'label' => $label,
                'selected' => (string) $selected === $valueAsString,
            ];
        }

        $template = in_array('inline', $attributes, true)
            ? 'themes.bootstrap.forms.radios-inline'
            : 'themes.bootstrap.forms.radios';

        return (string) $this->view->make($template, ['radios' => $radios])->render();
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $options
     * @param mixed $selected
     * @param array<int|string, mixed> $attributes
     */
    private function renderCheckboxCollection(
        string $name,
        array $options,
        mixed $selected,
        array $attributes,
        bool $hasErrors = false
    ): string {
        $selectedValues = is_array($selected)
            ? array_map('strval', $selected)
            : (strlen((string) $selected) > 0 ? [(string) $selected] : []);

        $checkboxes = [];
        foreach ($options as $value => $label) {
            $valueAsString = (string) $value;
            $checkboxes[] = [
                'name' => $name.'[]',
                'value' => $valueAsString,
                'id' => $attributes['id'].'_'.$valueAsString,
                'label' => $label,
                'checked' => in_array($valueAsString, $selectedValues, true),
            ];
        }

        $template = in_array('inline', $attributes, true)
            ? 'themes.bootstrap.forms.checkboxes-inline'
            : 'themes.bootstrap.forms.checkboxes';

        return (string) $this->view->make($template, [
            'checkboxes' => $checkboxes,
            'hasErrors' => $hasErrors,
        ])->render();
    }
}
