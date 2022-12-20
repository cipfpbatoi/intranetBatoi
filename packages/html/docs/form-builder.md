# Form Builder

## Novalidate

Allow users to set the novalidate option for every form generated with the `Form::open` or `Form::model` method, so developers can skip HTML5 validation in order to test backend validation in local or development environments:

```php
return [
    //..
    'novalidate' => true,
    //..
];
```

## Radios

Create a list of radios.

This function is similar to `Form::select` but it generates a collection of radios instead of options. i.e.:

```blade
{!! Form::radios('status', ['a' => 'Active', 'i' => 'Inactive']) !!}
```

You can pass 'inline' as a value in the attributes array, to set the radios as inline (they'll be rendered with the 'radios-inline' template).

## Checkboxes

Create a list of checkboxes.

This function is similar to Form::select, but it generates a collection of checkboxes instead of options, i.e.:

```php
$tags = [
    'php' => 'PHP',
    'python' => 'Python',
    'js' => 'JS',
    'ruby' => 'Ruby on Rails'
];

$checked = ['php', 'js'];
```

```blade
{!! Form::checkboxes('tags', $tags, $checked) !!}
```

You can pass 'inline' as a value of the attribute's array, to set the checkboxes as inline (they'll be rendered using the 'checkboxes-inline' template).