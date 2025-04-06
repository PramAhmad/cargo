<footer class="footer">
    <p class="text-sm">
      Copyright © {{now()->year}}
      <a class="text-primary-500 hover:underline" href="https://github.com/anisAronno/laravel-starter" target="_blank">
       {{ 
        hasSettings('copyright') ? getSettings('copyright') : config('app.name') }}
      </a>
    </p>

   
  </footer>