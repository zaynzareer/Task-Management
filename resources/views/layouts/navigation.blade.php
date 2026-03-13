<nav class="bg-white border-b border-orange-100">
    <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto">
        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-orange-500">TaskFlow</a>

        <div class="flex gap-4 items-center">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-orange-500">Dashboard</a>
            <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-orange-500">Profile</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</nav>
