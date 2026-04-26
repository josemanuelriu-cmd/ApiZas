<!doctype html>
<html>
  <head>
    <title>{{ config('app.name') }} API - Scalar</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body>
    <div id="app"></div>
    <script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference"></script>
    <script>
      Scalar.createApiReference('#app', {
        url: '{{ url("/docs/openapi.yaml") }}',
        theme: 'moon',
      })
    </script>
  </body>
</html>
