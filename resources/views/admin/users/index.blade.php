@extends('layouts.admin')
@section('title','Manajemen User Admin')
@section('page-title','Manajemen User Admin')
@section('content')
<div class="w-full space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <p class="text-sm text-gray-500">Kelola akun admin dan role hierarki</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#3553A8] px-4 py-2 text-sm font-semibold text-white hover:bg-[#2a4290]">
      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd"/></svg>
      Tambah User Admin
    </a>
  </div>

  <!-- Role Hierarchy Info -->
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
    @foreach($roles as $key => $role)
    <div class="rounded-xl border border-gray-200 bg-white p-4 text-center">
      <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $role['color'] }}">{{ $role['label'] }}</span>
      <p class="mt-2 text-[10px] text-gray-400">{{ $role['desc'] }}</p>
    </div>
    @endforeach
  </div>

  <!-- Users Table -->
  <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
    <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
      <h2 class="text-sm font-bold text-gray-800">Daftar User Admin ({{ $users->total() }})</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Nama</th>
            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Email</th>
            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Role</th>
            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Departemen</th>
            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Dibuat</th>
            <th class="text-right py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($users as $user)
          @php $roleInfo = $roles[$user->role] ?? ['label' => $user->role, 'color' => 'bg-gray-100 text-gray-600']; @endphp
          <tr class="hover:bg-gray-50">
            <td class="py-3 px-4 font-medium text-gray-800">{{ $user->name }}</td>
            <td class="py-3 px-4 text-gray-500">{{ $user->email }}</td>
            <td class="py-3 px-4">
              <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $roleInfo['color'] }}">{{ $roleInfo['label'] }}</span>
            </td>
            <td class="py-3 px-4 text-gray-500">{{ $user->department ?? '-' }}</td>
            <td class="py-3 px-4 text-gray-400 text-xs">{{ $user->created_at->format('d M Y') }}</td>
            <td class="py-3 px-4 text-right">
              <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus user ini?')">
                  @csrf @method('DELETE')
                  <button class="text-xs text-red-500 hover:underline">Hapus</button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada user admin</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">{{ $users->links() }}</div>
  </div>
</div>
@endsection
