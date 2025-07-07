<!-- resources/views/components/mobile-footer.blade.php -->
<div id="mobileFooter" class=" fixed bottom-0 left-0 right-0 bg-white border-t flex justify-around py-2 shadow-sm z-50 sm:hidden transition-transform duration-300">
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('home') ? 'text-blue-500' : 'text-gray-400' }}">
        <span class="text-2xl">🏠</span>
        <span class="text-xs font-semibold">Home</span>
    </a>
    <a href="{{ route('historique') }}" class="flex flex-col items-center {{ request()->routeIs('history') ? 'text-blue-500' : 'text-gray-400' }}">
        <span class="text-2xl">📋</span>
        <span class="text-xs font-semibold">Historique</span>
    </a>
    <a href="{{ route('profile.edit') }}" class="flex flex-col items-center {{ request()->routeIs('profile') ? 'text-blue-500' : 'text-gray-400' }}">
        <span class="text-2xl">👤</span>
        <span class="text-xs font-semibold">Profil</span>
    </a>
    <a href="" class="flex flex-col items-center {{ request()->routeIs('settings') ? 'text-blue-500' : 'text-gray-400' }}">
        <span class="text-2xl">⚙️</span>
        <span class="text-xs font-semibold">Réglages</span>
    </a>
</div>
