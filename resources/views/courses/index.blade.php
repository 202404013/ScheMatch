<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Courses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">My Courses</h3>
                        <a href="{{ route('courses.create') }}" class="text-blue-600 hover:text-blue-800">Add New Course</a>
                    </div>
                    
                    @foreach($myCourses as $course)
                        <div class="border-b py-3">
                            <div class="font-semibold">{{ $course->subject }}</div>
                            <div>{{ $course->class_code }}</div>
                            <div class="text-sm text-gray-600">{{ $course->section }}</div>
                            <div class="text-sm text-gray-600">{{ $course->professor }}</div>
                            <div class="text-xs text-gray-500">{{ $course->school_year }} - {{ $course->semester }}</div>
                            <div class="mt-2">
                                <a href="{{ route('courses.edit', $course->id) }}" class="text-sm text-blue-600">Edit</a>
                                <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 ml-2">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Matches</h3>
                    
                    @if(count($matches['classmates']) > 0)
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-700 mb-2">Classmates</h4>
                            @foreach($matches['classmates'] as $match)
                                <div class="border-b py-2">
                                    <div>
                                        <a href="javascript:void(0)" onclick="openChatWidget({{ $match->user_id }}, '{{ $match->user_name }}')" ...>
                                            {{ $match->user_name }}
                                        </a>
                                        <span class="text-sm text-gray-600">classmate</span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ $match->class_code }} - {{ $match->subject }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Section: {{ $match->section }} (You: {{ $match->my_section }})
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    @if(count($matches['same']) > 0)
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">Same Subject/Professor</h4>
                            @foreach($matches['same'] as $match)
                                <div class="border-b py-2">
                                    <div>
                                        <a href="javascript:void(0)" onclick="openChatWidget({{ $match->user_id }}, '{{ $match->user_name }}')" ...>
                                            {{ $match->user_name }}
                                        </a>
                                        <span class="text-sm text-gray-600">same {{ $match->match_type }}</span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        @if(str_contains($match->match_type, 'subject'))
                                            {{ $match->subject }}
                                        @endif
                                        @if(str_contains($match->match_type, '&'))
                                            - 
                                        @endif
                                        @if(str_contains($match->match_type, 'professor'))
                                            {{ $match->professor }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    @if(count($matches['classmates']) == 0 && count($matches['same']) == 0)
                        <p class="text-gray-500">No matches found yet.</p>
                    @endif
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>