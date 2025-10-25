<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'HRM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
<link rel= "stylesheet" href= "https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" >        <!-- Scripts -->
       @routes()

        @vite('resources/js/app.js')
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
         <!-- Google Translate widget -->
    <div id="google_translate_element" class="fixed bottom-4 right-4 z-50"></div>

    <script type="text/javascript">

      function googleTranslateElementInit() {
        new google.translate.TranslateElement(
          {
            pageLanguage: 'en',
            includedLanguages: 'en,bn',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
          },
          'google_translate_element'
        );
      }
    </script>
    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    </body>
</html>
