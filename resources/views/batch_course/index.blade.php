@extends('layouts.app')

@section('title', 'Batch & Course Management')
@section('heading', 'Batch & Course Management')

@section('content')
<div x-data="batchCourseManager()" x-cloak>
    <div class="w-full flex flex-col space-y-6 md:space-y-8">

        {{-- ✅ Success Message --}}
        @if(session('success'))
            <div class="w-full mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- ✅ Create Batch --}}
        <div class="bg-white rounded shadow p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold mb-4">Create Batch</h2>
            <div class="flex flex-col md:flex-row gap-4">
                <form method="POST" action="{{ route('batch.create') }}" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="batch_name" placeholder="Type Batch Name"
                        class="border-2 border-blue-600 rounded px-3 py-1 focus:border-blue-700 focus:ring-blue-700">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Create Batch</button>
                </form>
            </div>
        </div>

        {{-- ✅ Create Course --}}
        <div class="bg-white rounded shadow p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold mb-4">Create Course</h2>
            <div class="flex flex-col md:flex-row gap-4">
                <form method="POST" action="{{ route('course.create') }}" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="course_name" placeholder="Type Course Name"
                        class="border-2 border-blue-600 rounded px-3 py-1 focus:border-blue-700 focus:ring-blue-700">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Create Course</button>
                </form>
            </div>
        </div>

        {{-- ✅ Batch List --}}
        <div class="bg-white rounded shadow p-4 md:p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Batch List</h2>
                <a href="{{ route('batch.download.pdf') }}" 
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
                Download PDF
                </a>
            </div>
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="px-3 py-2 text-left">SL</th>
                        <th class="px-3 py-2 text-left">Batch Name</th>
                        <th class="px-3 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batches as $key => $batch)
                        <tr class="border-b">
                            <td class="px-3 py-2">{{ $key+1 }}</td>
                            <td class="px-3 py-2">{{ $batch->batch_name }}</td>
                            <td class="px-3 py-2">
                                <button 
                                    @click="openEditModal('batch', {{ $batch->id }}, '{{ $batch->batch_name }}')" 
                                    class="text-blue-600 hover:underline mr-2">Edit</button>
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                <button 
                                    @click="deleteItem('batch', {{ $batch->id }})"
                                    class="text-red-600 hover:underline">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ✅ Course List --}}
        <div class="bg-white rounded shadow p-4 md:p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Course List</h2>
                <a href="{{ route('course.download.pdf') }}" 
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
                Download PDF
                </a>
            </div>
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="px-3 py-2 text-left">SL</th>
                        <th class="px-3 py-2 text-left">Course Name</th>
                        <th class="px-3 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $key => $course)
                        <tr class="border-b">
                            <td class="px-3 py-2">{{ $key+1 }}</td>
                            <td class="px-3 py-2">{{ $course->course_name }}</td>
                            <td class="px-3 py-2">
                                <button 
                                    @click="openEditModal('course', {{ $course->id }}, '{{ $course->course_name }}')" 
                                    class="text-blue-600 hover:underline mr-2">Edit</button>
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                <button 
                                    @click="deleteItem('course', {{ $course->id }})"
                                    class="text-red-600 hover:underline">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ✅ Edit Modal --}}
        <div x-show="showModal"
             class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h3 class="text-lg font-semibold mb-4" x-text="editType === 'batch' ? 'Edit Batch' : 'Edit Course'"></h3>
                <input type="text" x-model="editName" class="w-full border rounded px-3 py-2 mb-4" />
                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button @click="updateItem()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ✅ Alpine.js + AJAX --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('batchCourseManager', () => ({
        showModal: false,
        editType: '',
        editId: null,
        editName: '',

        openEditModal(type, id, name) {
            this.editType = type;
            this.editId = id;
            this.editName = name;
            this.showModal = true;
        },

        updateItem() {
            const url = this.editType === 'batch' 
                ? `/batch/update/${this.editId}`
                : `/course/update/${this.editId}`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name: this.editName })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Updated successfully');
                    window.location.reload();
                } else {
                    alert('Update failed');
                }
            })
            .catch(err => console.error(err));

            this.showModal = false;
        },

        deleteItem(type, id) {
            if (!confirm('Are you sure you want to delete this item?')) return;

            const url = type === 'batch'
                ? `/batch/delete/${id}`
                : `/course/delete/${id}`;

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Deleted successfully');
                    window.location.reload();
                } else {
                    alert('Delete failed');
                }
            })
            .catch(err => console.error(err));
        }
    }));
});
</script>
@endsection



