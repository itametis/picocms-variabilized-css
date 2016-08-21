# What is VARIABILIZED CSS LOADER ?
It's a [PICO CMS](http://picocms.org/) plugin which allow you to use variables inside yours CSS files.


# How to install it ?
In three steps:
1. Copy-paste the _**VariabilizedCssLoader.php**_ inside the plugin directory of PICO.

2. Update the file 'config/config.php' of PICO and add the following line to enable the plugin:
```php
$config['VariabilizedCssLoader.enabled'] = true;
```
3. Create the _**css-variables.ini**_ at the root of your theme folder. For instance, with the default theme, this file will be at _./theme/default/css-variables.ini_.

N.B: this file contains all the value of the variables to substitute in CSS.


# What does it do ?
This plugin processes in three steps :
1. It reads and loads all the variables contained into the file _**css-variables.ini**_
2. Then it will parse the CSS filse you indicate in your Twig template.
3. It generates another CSS with all variables substituted.


# Example
In the Twig template use this to load your CSS:
```twig
<head>
    ...
    <!-- The following line will load and substitue the variable inside the CSS file named 'color.css' and place in the folder
        './themes/<TheThemeYouDefineInThePicoConfig/styles/color.css' -->
    <link rel="stylesheet" href="{{ variabilizedCssLoader.loadCss('/styles/color.css')  }}" type="text/css" />
</head>
```

Below an extract of the _css-variables.ini_:
```ini
; Setup the first variable
red = #F95700

; do not forget the quotes with special characters like parenthesis
rgba_teal = "rgba(0,164,115,1)"
```

Below an example of CSS file with variables:
```css
#header {
    color: $(rgba_teal)
    border: thin solid $(red)
}

```
**And that it ! Substitution are done.**

# Configuration of cache
You ave two choice :
* Either you are in development mode and you want this plugin to regenerate file at each request.
* Either you are in production mode and want this plugin to generate CSS once for all in order to preserve performances.

The cache keep the file on disk and wont regenerate them.

To enable the cache add this inside the 'config.php' file of PICO:
```php
$config['VariabilizedCssLoader.activeCssCache'] = true;
```

Or add this or don't declare this line to enable the development mode (default):
```php
$config['VariabilizedCssLoader.activeCssCache'] = false;
```
