<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: "Raleway", Arial, sans-serif;
                background-color: #f5f5f5;
                color: #333;
            }
            
            .header {
                background-color: white;
                border-bottom: 1px solid #e5e5e5;
                padding: 1rem 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .logo {
                font-size: 1.5rem;
                letter-spacing: 3px;
                color: #333;
                text-decoration: none;
            }
            
            .nav {
                display: flex;
                gap: 2rem;
                align-items: center;
            }
            
            .nav a, .nav button, .nav form button {
                text-decoration: none;
                color: #666;
                font-size: 0.95rem;
                transition: color 0.3s;
                background: none;
                border: none;
                cursor: pointer;
                font-family: "Raleway", Arial, sans-serif;
            }
            
            .nav a:hover, .nav button:hover, .nav form button:hover {
                color: #000;
            }
            
            .nav a.active {
                color: #000;
                font-weight: 600;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <header class="header">
            <a href="/dashboard" class="logo">ScheMatch</a>
            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Home</a>
                <a href="{{ route('availabilities.index') }}" class="{{ request()->routeIs('availabilities.*') ? 'active' : '' }}">Availability</a>
                <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">Courses</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </nav>
        </header>

        <div style="min-height: 100vh; background-color: #f3f4f6;">
            @if (isset($header))
                <header style="background-color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); display: none;">
                    <div style="max-width: 80rem; margin: 0 auto; padding: 1.5rem 1rem;">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

        @auth
        <div style="position: fixed; bottom: 1rem; right: 1rem; z-index: 9999;">
            <button id="chatToggle" onclick="toggleChat()" style="background-color: #5f0e0eff; color: white; border-radius: 9999px; width: 3.5rem; height: 3.5rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); position: relative; border: none; cursor: pointer; transition: background-color 0.2s;">
                <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                @php
                    $unreadCount = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->count();
                @endphp
                @if($unreadCount > 0)
                    <span style="position: absolute; top: -0.25rem; right: -0.25rem; background-color: #ef4444; color: white; font-size: 0.75rem; line-height: 1rem; border-radius: 9999px; height: 1.25rem; width: 1.25rem; display: flex; align-items: center; justify-content: center;">
                        {{ $unreadCount }}
                    </span>
                @endif
            </button>

            <div id="chatBox" style="display: none; background-color: white; border-radius: 0.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; position: fixed; bottom: 5rem; right: 1rem; width: 20rem; max-height: 32rem;">
                
                <div id="conversationListView">
                    <div style="background-color: #6e0808ff; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-weight: 600;">Messages</h3>
                        <button onclick="toggleChat()" style="color: white; background: none; border: none; cursor: pointer; font-size: 1.25rem; line-height: 1;" onmouseover="this.style.color='#d1d5db'" onmouseout="this.style.color='white'">✕</button>
                    </div>
                    <div style="height: 24rem; overflow-y: auto;">
                        @php
                            try {
                                $conversations = DB::select("
                                    SELECT DISTINCT
                                        CASE 
                                            WHEN messages.sender_id = ? THEN messages.receiver_id
                                            ELSE messages.sender_id
                                        END as user_id,
                                        users.name,
                                        (SELECT message FROM messages 
                                         WHERE (sender_id = ? AND receiver_id = users.id) 
                                            OR (sender_id = users.id AND receiver_id = ?)
                                         ORDER BY created_at DESC LIMIT 1) as last_message
                                    FROM messages
                                    INNER JOIN users ON users.id = CASE 
                                        WHEN messages.sender_id = ? THEN messages.receiver_id
                                        ELSE messages.sender_id
                                    END
                                    WHERE messages.sender_id = ? OR messages.receiver_id = ?
                                ", [auth()->id(), auth()->id(), auth()->id(), auth()->id(), auth()->id(), auth()->id()]);
                            } catch (\Exception $e) {
                                $conversations = [];
                            }
                        @endphp

                        @if(count($conversations) > 0)
                            @foreach($conversations as $conv)
                                <div onclick="loadConversation({{ $conv->user_id }}, '{{ addslashes($conv->name) }}')" style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb; cursor: pointer;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">
                                    <div style="font-weight: 600; font-size: 0.875rem; line-height: 1.25rem; color: #111827;">{{ $conv->name }}</div>
                                    <div style="font-size: 0.75rem; line-height: 1rem; color: #4b5563; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $conv->last_message }}</div>
                                </div>
                            @endforeach
                        @else
                            <div style="padding: 1rem; text-align: center; color: #6b7280;">No messages yet</div>
                        @endif
                    </div>
                </div>

                <div id="chatView" style="display: none; flex-direction: column; height: 32rem;">
                    <div style="background-color: #5f0e0eff; color: white; padding: 1rem; display: flex; align-items: center;">
                        <button onclick="backToList()" style="color: white; background: none; border: none; cursor: pointer; margin-right: 0.5rem; font-size: 1.25rem;" onmouseover="this.style.color='#d1d5db'" onmouseout="this.style.color='white'">←</button>
                        <h3 id="chatUserName" style="font-weight: 600; flex: 1;"></h3>
                        <button onclick="toggleChat()" style="color: white; background: none; border: none; cursor: pointer; font-size: 1.25rem;" onmouseover="this.style.color='#d1d5db'" onmouseout="this.style.color='white'">✕</button>
                    </div>
                    
                    <div id="messagesContainer" style="flex: 1; overflow-y: auto; padding: 1rem; background-color: #f9fafb;">
                    </div>
                    
                    <form onsubmit="sendMessage(event); return false;" style="border-top: 1px solid #e5e7eb; padding: 0.75rem; background-color: white;">
                        <input type="hidden" id="receiverId">
                        <div style="display: flex; gap: 0.5rem;">
                            <input 
                                type="text" 
                                id="messageInput"
                                placeholder="Type a message..." 
                                style="flex: 1; border: 1px solid #d1d5db; border-radius: 0.25rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; line-height: 1.25rem; color: #111827;"
                                onfocus="this.style.outline='2px solid #5f0e0eff'; this.style.outlineOffset='2px';"
                                onblur="this.style.outline='none';"
                                required
                            >
                            <button type="submit" style="background-color: #5f0e0eff; color: white; padding: 0.5rem 1rem; border-radius: 0.25rem; font-size: 0.875rem; line-height: 1.25rem; border: none; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#2563eb'" onmouseout="this.style.backgroundColor='#3b82f6'">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        // Add hover effect for chat button
        document.addEventListener('DOMContentLoaded', function() {
            const chatToggle = document.getElementById('chatToggle');
            if (chatToggle) {
                chatToggle.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#5f0e0eff';
                });
                chatToggle.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '#5f0e0eff';
                });
            }
        });

        window.openChatWidget = function(userId, userName) {
            document.getElementById('chatBox').style.display = 'block';
            loadConversation(userId, userName);
        }

        window.toggleChat = function() {
            const chatBox = document.getElementById('chatBox');
            if (chatBox.style.display === 'none') {
                chatBox.style.display = 'block';
            } else {
                chatBox.style.display = 'none';
            }
        }

        window.loadConversation = function(userId, userName) {
            document.getElementById('conversationListView').style.display = 'none';
            const chatView = document.getElementById('chatView');
            chatView.style.display = 'flex';
            document.getElementById('chatUserName').textContent = userName;
            document.getElementById('receiverId').value = userId;
            
            fetch(`/messages/${userId}/get`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('messagesContainer');
                    container.innerHTML = '';
                    
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            const isOwn = msg.sender_id == {{ auth()->id() }};
                            const msgDiv = document.createElement('div');
                            msgDiv.style.marginBottom = '0.75rem';
                            msgDiv.style.textAlign = isOwn ? 'right' : 'left';
                            
                            const bubble = document.createElement('div');
                            bubble.style.display = 'inline-block';
                            bubble.style.maxWidth = '20rem';
                            bubble.style.borderRadius = '0.5rem';
                            bubble.style.padding = '0.5rem 0.75rem';
                            bubble.style.fontSize = '0.875rem';
                            bubble.style.lineHeight = '1.25rem';
                            bubble.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
                            
                            if (isOwn) {
                                bubble.style.backgroundColor = '#5f0e0eff';
                                bubble.style.color = 'white';
                            } else {
                                bubble.style.backgroundColor = 'white';
                                bubble.style.color = '#111827';
                                bubble.style.border = '1px solid #e5e7eb';
                            }
                            
                            bubble.textContent = msg.message;
                            msgDiv.appendChild(bubble);
                            container.appendChild(msgDiv);
                        });
                    } else {
                        const noMsg = document.createElement('div');
                        noMsg.style.textAlign = 'center';
                        noMsg.style.color = '#6b7280';
                        noMsg.style.fontSize = '0.875rem';
                        noMsg.style.lineHeight = '1.25rem';
                        noMsg.textContent = 'No messages yet. Start the conversation!';
                        container.appendChild(noMsg);
                    }
                    
                    container.scrollTop = container.scrollHeight;
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                    const container = document.getElementById('messagesContainer');
                    container.innerHTML = '<div style="text-align: center; color: #ef4444; font-size: 0.875rem; line-height: 1.25rem;">Failed to load messages</div>';
                });
        }

        window.backToList = function() {
            document.getElementById('chatView').style.display = 'none';
            document.getElementById('conversationListView').style.display = 'block';
            document.getElementById('messageInput').value = '';
        }

        window.sendMessage = function(event) {
            event.preventDefault();
            
            const receiverId = document.getElementById('receiverId').value;
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (!message) return;
            
            messageInput.disabled = true;
            
            fetch('/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    receiver_id: receiverId,
                    message: message
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to send message');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    messageInput.value = '';
                    const userName = document.getElementById('chatUserName').textContent;
                    loadConversation(receiverId, userName);
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Failed to send message. Please try again.');
            })
            .finally(() => {
                messageInput.disabled = false;
                messageInput.focus();
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        </script>
        @endauth
    </body>
</html>