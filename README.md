# 💬 th23 Easy Translation

Easily add multi language capabilities to your PHP scripts with minimal effort and overhead


## 🚀 Introduction

Working on smaller PHP projects I realized a need to have a **simple and easy** to integrate option for internationalization (i18n). PHP defaults capabilities with locales, `.po-files` and `.mo-files` can sometime be overwhelming - and leading to a lot of time spent on the "side-task" of translation instead on the main script functionality.

`th23 Easy Translation` is built with some few goals in mind:

* Keep it simple while providing **key features** and maintaining performance
* Small footprint and **easy to integrate** into small(er) PHP projects
* Acceptable limitation to core capabilities to **reduce overhead and maintenance** effort
* **Easy handling of translation files** compared to sometimes cumbersome po-/mo-files
* Clear selection of available languages via files and **not relying on installed locales on hosting systems**


## ⚙️ Setup

The folder / file structure of your PHP project when adding `th23 Easy Translation` should look something like this:
```
inc/
   i18n.php
lang/
   de_DE.php
   ...
tools/
   .htaccess
   i18n-tools.php
config.php
index.php
```

The `/inc` folder contains the core script picking and showing the translations according to the chosen language.

All language files have to be named according to the locale of the language they are replresenting. The language files have to be placed in the `/lang` subfolder of your project.

Additonal language files can be created using the `i18n-tools.php` script in the `/tools` folder.

In the `config.php` file you have to defined a default language.

The `index.php` gives an example of how it all works together and can be included into your PHP project file(s).

> [!TIP]
> Copy all the included files into an empty folder on your server to have a first simple working example to switch languages


## 🖐️ Usage

In the source code of your PHP application you can simply use the following syntax on every string that should be translatable:
`__('English language text')`

To parse in data into language strings use the following syntax, allowing for multiple additional parameters passed to the translation function and replacing placeholder (`%s` for strings, `%i` for integers, `%d` for doubles):
`__('Here you see %s at work...', 'th23 Easy Translation')`

To start translation into a new language or update an existing translation file eg in case new language strings got added to your source code enable access to `/tools/i18n-tools.php` (see `/tools/.htaccess`) and open it in your webbrowser. Save generated / updated language file in the `/lang` subfolder.


## 🤝 Contributors

Feel free to [raise issues](/issues) or [contribute code](/pulls) for improvements via GitHub.


## ©️ License

You are free to use this code in your projects as per the `GNU General Public License v3.0`. References to this repository are of course very welcome in return for my work 😉
