@extends('layouts.admin')
@section('title','Edit User Admin')
@section('page-title','Edit User Admin')
@section('content')
<div class="max-w-2xl">
  <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
    <form method="POST" action=" route('admin.users.update', $user) " class="space-y-5">
      @csrf @method('PUT')
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
        <input type="text" name="name" value=" old('name', $user->name) " required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3553A8]">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-gray-400">(tidak bisa diubah)</span></label>
        <input type="email" value=" $user->email " disabled class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-400">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
        <select name="role" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3553A8]">
          @foreach($roles as $key => $role)
          <option value=" $key "  (old('role', $user->role) === $key) ? 'selected' : '' > $role['label']  —  $role['desc'] </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Departemen <span class="text-gray-400">(opsional)</span></label>
        <input type="text" name="department" value=" old('department', $user->department) " class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3553A8]">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru <span class="text-gray-400">(kosongkan jika tidak diubah)</span></label>
        <input type="password" name="password" minlength="8" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3553A8]">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
        <input type="password" name="password_confirmation" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3553A8]">
      </div>
      <div class="flex gap-3 pt-2">
        <button type="submit" class="rounded-lg bg-[#3553A8] px-5 py-2 text-sm font-semibold text-white hover:bg-[#2a4290]">Update</button>
        <a href=" route('admin.users.index') " class="rounded-lg border border-gray-300 px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
