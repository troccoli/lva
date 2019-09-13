## Form Elements

In the past I used to use the excellent `laravelcollective/html` package which provides
a lot of very useful reusable components. Unfortunately, this package is not maintained
any longer, and I believe is not included in Laravel as default any more.
Instead of looking for a replacement I decided to create my own components and extend
`Blade` so I could use them anywhere I liked.

All the fields are in the `resources/views/partials/form` folder and then I use them
as new `Blade` directive in the `app/Providers/BladeServiceProvider`. I can then add
them to any form like this:

```blade
@textField([
  'label' => __("Season's year"),
  'fieldName' => 'year',
  'required' => true,
  'defaultValue' => isset($season) ? $season->getYear() : ''
])
```

But I have also used another method to reuse components that `Blade` allows: the `@component`
directive. This components are in the `resources/views/components` folder and, as I said, can
be used with the `@component` directive, for example:

```blade
@component('components.crud.view-button')
  {{ route('competitions.index', ['season_id' => $season->getId()]) }}
@endcomponent 
```

## Translations

All the text in any of the views used in this project are "translatable", i.e. the views have been
built in a way that just by providing the translations in a new language file(s) in the `resources/lang`
folder, everything will be translated automatically (when the locale is set accordingly).

To do so I use the `__()` Laravel helper method. It's easy to use and short to type.
