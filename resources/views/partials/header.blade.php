<header>
  <div class="logo">Dopix</div>
 <nav id="nav" class="full-flex flex-col md:flex-row  md:items-center justify-between px-4 py-2 bg-white shadow hidden md:flex">
  <!-- Liens de navigation -->
    <div class="flex flex-col md:flex-row items-start md:items-center space-y-2 md:space-y-0 md:space-x-4 w-full">
        <a href="{{ route('dashboard') }}">Accueil</a>
        <a href="{{ route('historique') }}">Historique</a>
        <a href="{{ route('verification') }}">Vérifier</a>
        <a href="{{ route('profile.edit') }}">Profil</a>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-blue-600 hover:underline">
        Déconnexion
        </a>

        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>

    </div> 
</nav>

<!-- Burger visible en mobile -->
<button id="burger" class="burger md:hidden">
  <span></span>
</button>

</header>
<script>
  // Burger menu JS
  const burger = document.getElementById('burger');
  const nav = document.getElementById('nav');
  function checkWidth() {
    if(window.innerWidth < 600) {
      burger.style.display = "block";
      nav.classList.remove('open');
    } else {
      burger.style.display = "none";
      nav.classList.remove('open');
    }
  }
  window.addEventListener('resize', checkWidth);
  checkWidth();
  burger.addEventListener('click', function(){
    nav.classList.toggle('open');
  });
</script>