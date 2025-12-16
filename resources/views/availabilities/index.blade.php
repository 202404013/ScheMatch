<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Availability Calendar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <div class="flex justify-between items-center mb-4">
                    <button onclick="loadPublicUsers()" class="bg-purple-500 hover:bg-purple-700 text-black font-bold py-2 px-4 rounded text-sm flex items-center gap-2">
                        <span>🌐</span>
                        <span>Public Users</span>
                    </button>
                </div>

                <form method="GET" action="{{ route('availabilities.index') }}" id="filterForm">
                    @if(count($selectedPublicUserIds ?? []) > 0)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600 mb-2">Selected Public Users: <span class="font-semibold">{{ count($selectedPublicUserIds) }}</span></p>
                            <button type="button" onclick="loadPublicUsers()" class="text-sm text-purple-600 hover:text-purple-800 underline">
                                View/Edit Selection
                            </button>
                        </div>
                    @endif
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <h3 class="text-lg font-semibold mb-4">👁️Visibility Settings for All Dates</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <button onclick="changeAllVisibility('public')" class="flex items-center justify-center gap-2 px-6 py-3 border-2 border-blue-500 text-blue-700 rounded hover:bg-blue-50 transition font-semibold">
                        <span>🌐</span>
                        <span>Public</span>
                    </button>
                    
                    <button onclick="changeAllVisibility('private')" class="flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-500 text-gray-700 rounded hover:bg-gray-50 transition font-semibold">
                        <span>🔐</span>
                        <span>Private</span>
                    </button>
                    
                    <button onclick="deleteAllAvailabilities()" class="flex items-center justify-center gap-2 px-6 py-3 border-2 border-red-500 text-red-700 rounded hover:bg-red-50 transition font-semibold">
                        <span>🗑️</span>
                        <span>Delete</span>
                    </button>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div id="calendar"></div>
            </div>

            <div id="publicUsersModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Public Users</h3>
                        <button onclick="closePublicUsersModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="mb-4">
                        <input 
                            type="text" 
                            id="userSearchInput"
                            placeholder="Search by name or email..."
                            oninput="filterPublicUsers()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    
                    <div id="publicUsersContent" class="max-h-96 overflow-y-auto border rounded p-2">
                        <p class="text-gray-400 text-center py-4">Loading...</p>
                    </div>
                    
                    <div class="mt-4 flex justify-between items-center">
                        <span id="selectedCount" class="text-sm text-gray-600">0 selected</span>
                        <div class="flex gap-2">
                            <button onclick="clearPublicUserFilter()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Clear All
                            </button>
                            <button onclick="applyPublicUserFilter()" class="text-black font-bold py-2 px-4 rounded">
                                Find
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--template-->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    
    <style>
        .fc-event-title { display: none !important; }
        .fc-event { cursor: pointer; border-radius: 4px; }
        .fc-event:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.3); transform: translateY(-2px); transition: all 0.2s; }
    
        .fc-timegrid-now-indicator-line { display: none !important; }
        .fc-timegrid-now-indicator-arrow { display: none !important; }
        .fc-day-today { background-color: transparent !important; }
        .fc-col-header-cell-cushion { background-color: transparent !important; }
        
        .tippy-box[data-theme~='availability'] {
            background-color: white;
            color: #333;
            border: 2px solid #22c55e;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .tippy-box[data-theme~='availability'][data-placement^='top'] > .tippy-arrow::before {
            border-top-color: white;
        }
        .availability-tooltip {
            padding: 12px;
            min-width: 200px;
        }
        .availability-tooltip .title {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #22c55e;
            border-bottom: 2px solid #22c55e;
            padding-bottom: 6px;
        }
        .availability-tooltip .person {
            padding: 5px 0;
            font-size: 0.95rem;
        }
        .availability-tooltip .person.you {
            font-weight: 600;
            color: #094b00ff;
        }
    </style>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/tippy.js@6.3.7/dist/tippy-bundle.umd.min.js'></script>

    <script>
        let calendar = null;
        let allMyAvailabilityIds = [];

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEventsData = @json($calendarEvents ?? []);
            const currentUserName = '{{ auth()->user()->name }}';
            const currentUserId = {{ auth()->id() }};
            
            const idSet = new Set();
            calendarEventsData.forEach(event => {
                if (event.extendedProps.userIds && event.extendedProps.userIds.includes(currentUserId)) {
                    if (event.extendedProps.availabilityIds) {
                        event.extendedProps.availabilityIds.forEach(id => idSet.add(id));
                    }
                }
            });
            allMyAvailabilityIds = Array.from(idSet);
            
            calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                slotMinTime: '00:00:00',
                slotMaxTime: '24:00:00',
                slotDuration: '00:30:00',
                snapDuration: '00:30:00',
                height: 'auto',
                editable: false,
                selectable: true,
                selectMirror: true,
                events: calendarEventsData,
                
                eventDidMount: function(info) {
                    const props = info.event.extendedProps;
                    const userNamesList = props.userNames.map(name => {
                        const isYou = name === currentUserName;
                        return `<div class="person ${isYou ? 'you' : ''}">${name}${isYou ? ' (You)' : ''}</div>`;
                    }).join('');
                    
                    const tooltipContent = `
                        <div class="availability-tooltip">
                            <div class="title">${props.count} Available</div>
                            ${userNamesList}
                        </div>
                    `;
                    
                    tippy(info.el, {
                        content: tooltipContent,
                        allowHTML: true,
                        theme: 'availability',
                        placement: 'top',
                        arrow: true,
                        interactive: false,
                        delay: [100, 0],
                    });
                },
                
                select: function(info) {
                    const date = info.startStr.split('T')[0];
                    const startTime = info.start.toTimeString().slice(0, 8);
                    const endTime = info.end.toTimeString().slice(0, 8);
                    
                    fetch('/availabilities', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            date: date,
                            start_time: startTime,
                            end_time: endTime,
                            visibility: 'public'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
                    
                    calendar.unselect();
                }
            });
            
            calendar.render();
        });

        function changeAllVisibility(newVisibility) {
            if (allMyAvailabilityIds.length === 0) {
                alert('You have no availabilities to update.');
                return;
            }

            const visibilityNames = {
                'public': 'Public',
                'private': 'Private'
            };

            if (!confirm(`Make ALL your availabilities ${visibilityNames[newVisibility]}?\n\nThis will update all ${allMyAvailabilityIds.length} of your availability blocks.`)) {
                return;
            }

            const promises = allMyAvailabilityIds.map(id =>
                fetch(`/availabilities/${id}/visibility`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ visibility: newVisibility })
                }).then(r => r.json())
            );

            Promise.all(promises)
                .then(() => {
                    alert(`All availabilities changed to ${visibilityNames[newVisibility]}!`);
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update some availabilities. Please try again.');
                });
        }

        function deleteAllAvailabilities() {
            if (allMyAvailabilityIds.length === 0) {
                alert('You have no availabilities to delete.');
                return;
            }

            if (!confirm(`⚠️ Are you sure?\n\nThis will PERMANENTLY REMOVE all ${allMyAvailabilityIds.length} of your availability blocks.`)) {
                return;
            }

            const promises = allMyAvailabilityIds.map(id =>
                fetch(`/availabilities/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(r => r.json())
            );

            Promise.all(promises)
                .then(() => {
                    alert('All availabilities deleted successfully!');
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete some availabilities. Please try again.');
                });
        }

        function loadPublicUsers() {
            fetch('/availabilities/users/public')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayPublicUsers(data.users);
                }
            });
        }

        function displayPublicUsers(users) {
            const content = document.getElementById('publicUsersContent');
            const selectedPublicUserIds = @json($selectedPublicUserIds ?? []);
            
            content.innerHTML = users.map(user => {
                const isChecked = selectedPublicUserIds.includes(user.id);
                return `
                    <label class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded cursor-pointer user-item" data-name="${user.name.toLowerCase()}" data-email="${user.email.toLowerCase()}">
                        <input type="checkbox" value="${user.id}" class="public-user-checkbox rounded" ${isChecked ? 'checked' : ''} onchange="updateSelectedCount()">
                        <div>
                            <div class="font-semibold">${user.name}</div>
                            <div class="text-sm text-gray-500">${user.email}</div>
                        </div>
                    </label>
                `;
            }).join('');
            
            updateSelectedCount();
            document.getElementById('publicUsersModal').classList.remove('hidden');
        }

        function updateSelectedCount() {
            const count = document.querySelectorAll('.public-user-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = `${count} selected`;
        }

        function closePublicUsersModal() {
            document.getElementById('publicUsersModal').classList.add('hidden');
        }

        function filterPublicUsers() {
            const search = document.getElementById('userSearchInput').value.toLowerCase();
            document.querySelectorAll('.user-item').forEach(item => {
                const name = item.dataset.name;
                const email = item.dataset.email;
                const matches = name.includes(search) || email.includes(search);
                item.style.display = matches ? '' : 'none';
            });
        }

        function applyPublicUserFilter() {
            const checkboxes = document.querySelectorAll('.public-user-checkbox:checked');
            const selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            closePublicUsersModal();
            
            const form = document.getElementById('filterForm');
            form.querySelectorAll('input[name="public_users[]"]').forEach(input => input.remove());
            
            selectedIds.forEach(userId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'public_users[]';
                input.value = userId;
                form.appendChild(input);
            });
            
            form.submit();
        }

        function clearPublicUserFilter() {
            closePublicUsersModal();
            
            const form = document.getElementById('filterForm');
            form.querySelectorAll('input[name="public_users[]"]').forEach(input => input.remove());
            
            form.submit();
        }
    </script>
</x-app-layout>