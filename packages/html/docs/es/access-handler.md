# Access handler

Algunas veces quieres mostrar o esconder elementos del menú, campos del formulario, etc... para ciertos usuarios; con este componente se puede hacer sin necesidad de condicionales o código boilerplate.

Este paquete incluye una clase `BasicAccessHandler`, pero puedes construir tu propia implementación de `AccessHandler`.

## BasicAccessHandler

Sólo pasa una de las siguientes opciones como un atributo de campo o valor del elemento del menú:

1. *callback*: debe devolver true si se quiere dar acceso, false en caso contrario.
2. *logged*: true: requiere que el usuario esté autenticado, false: requiere usuario invitado.
3. *roles*: true si el usuario tiene uno de los roles requeridos.
4. *allows*: usa el método Gate::allows.
5. *check*: usa el método Gate::check (alias de allow).
6. *denies*: usa el método Gate::denies.
7. Si ninguna opción es pasada, éste devolverá true (el elemento será renderizado).

*WARNING*: tomar en cuenta que este paquete sólo evitará que los elementos aparezcan en el frontend, aún se necesita proteger el acceso de backend usando middleware, etc.

Uso: 

#### Campos de formulario

```blade
{!! Field::select('user_id', null, ['roles' => 'admin'])
```

#### Elementos de menú

```php
// config/menu.php

return [
    'items' => [
        'account' => [
            'logged' => true
        ],
        'login' => [
            'logged' => false
        ],
        'settings' => [
            'roles' => 'admin'
        ]
    ]
];
```
   
```blade
{!! Menu::make('menu.items') !}}
```

## Autorización y políticas de acceso

Las opciones allows, check y denies aceptan un string o un array como valor.

Si es un string, será el nombre de la habilidad sin argumentos.

Si es un array, la primera posición del array será el nombre de la habilidad y el resto serán los argumentos.

Ejemplos:

```blade
{!! Field::text('change-password', ['allows' => 'change-password']) !!}
```

```blade
{!! Field::select('category', $options, ['allows' => ['change-post-category', $category]]) !!}
```

Si estás contruyendo menús puedes usar parámetros dinámicos para pasar valores a la autorización.

En el siguiente ejemplo definiremos un parámetro dinámico 'post' y pasarlo usando setParam cuando se construya el menú:

```php
// config/menu.php

return [
    'items' => [
        'view-post' => [],
        'edit-post' => [
            'allows' => ['update-post', ':post']
        ]
    ]
];
```
   
```blade
{!! Menu::make('menu.items')->setParam('post', $post)->render() !}}
```
## Personalización

Si estás trabajando en un proyecto complejo con muchas reglas de acceso diferentes, etc... puedes necesitar implementar un AccessHandler propio. Para hacer esto, crea una nueva clase que implemente la interface `Styde\Html\Access\AccessHandler`, entonces extiende el HtmlServiceProvider y sustituye el método `registerAccessHandler`.

## Standalone

Si se quiere usar la clase del access handler como un componente independiente, por favor agrega este alias global en `config/app.php`.

```php
  'aliases' => [
    // ...
    'Access' => Styde\Html\Facades\Access,
    // ...
  ],
```

Luego se puede utilizar la facade donde se quiera:

```blade
@if (Access:check(['roles' => ['admin, 'editor']]))
    <p>
        <a href='{{ url('admin/posts', [$post->id]) }}'>
            Editar esta página
        </a>
    </p>
@endif
```

## Desactivar el access handler

Se puede desactivar este componente en la configuración:

```php
//config/html.php

return [
    //..
    'control_access' => false,
    //..
];
```

Haciendo esto, los atributos callback, logged y roles serán simplemente ignorados, y todos los usuarios serán capaces de ver todos los elementos.
