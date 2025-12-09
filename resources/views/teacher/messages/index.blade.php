@extends('layouts.app')

@section('content')
    @include('layouts.teacher-sidebar')

    <style>
        .messages-root {
            min-height: 100vh;
            padding: 16px;
            padding-top: 80px;
            background: #F3F4F6;
        }
        .messages-container {
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 70vh;
        }
        .messages-sidebar {
            border-right: 1px solid #E5E7EB;
            background: #F9FAFB;
            padding: 16px;
            overflow-y: auto;
        }
        .messages-main {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .messages-header {
            padding: 8px 12px;
            border-bottom: 1px solid #E5E7EB;
            background: #F9FAFB;
        }
        .student-item {
            padding: 12px;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            background: #fff;
            transition: all 0.15s ease;
        }
        .student-item.active {
            background: #EFF6FF;
            border-color: #3B82F6;
        }
        .chat-window {
            flex: 1;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            background: #F9FAFB;
            padding: 12px;
            overflow-y: auto;
            min-height: 360px;
        }
        .message {
            margin-bottom: 12px;
            display: flex;
            flex-direction: column;
        }
        .message.self .bubble {
            background: #1C6EA4;
            color: #fff;
            border-color: #1C6EA4;
            margin-left: auto;
        }
        .bubble {
            max-width: 75%;
            padding: 10px 12px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #E5E7EB;
        }
        .meta {
            font-size: 0.75rem;
            color: #6B7280;
            margin-top: 2px;
        }
        .chat-input {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .chat-input textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            min-height: 70px;
        }
        .chat-input button {
            padding: 10px 14px;
            background: #1C6EA4;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
    </style>

    <div class="messages-root">
        <div class="messages-container">
            <div class="messages-sidebar">
                <div class="messages-header">
                    <h3 style="margin:0;color:#111827;">Students</h3>
                    <p style="margin:4px 0 0;color:#6B7280;font-size:0.9rem;">Select a student to chat</p>
                </div>
                <div style="padding-top:12px;">
                    @foreach ($students as $student)
                        <div class="student-item" data-id="{{ $student->id }}" @class(['active' => $selectedStudentId == $student->id])>
                            <div style="font-weight:700;color:#111827;">{{ $student->name }}</div>
                            <div style="color:#6B7280;font-size:0.9rem;">{{ $student->email }} • {{ $student->student_id ?? 'N/A' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="messages-main">
                <div class="messages-header" style="border:1px solid #E5E7EB;border-radius:10px;">
                    <h3 style="margin:0;color:#111827;">Conversation</h3>
                    <p style="margin:4px 0 0;color:#6B7280;font-size:0.9rem;" id="chatSubtitle">Select a student to view messages.</p>
                </div>
                <div class="chat-window" id="chatWindow">
                    <div style="color:#9CA3AF;">No student selected.</div>
                </div>
                <div class="chat-input">
                    <textarea id="chatMessage" placeholder="Type a message" disabled></textarea>
                    <button id="sendBtn" disabled>Send</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const studentItems = document.querySelectorAll('.student-item');
            const chatWindow = document.getElementById('chatWindow');
            const chatMessage = document.getElementById('chatMessage');
            const sendBtn = document.getElementById('sendBtn');
            const chatSubtitle = document.getElementById('chatSubtitle');
            let selectedStudent = null;

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function renderMessages(messages) {
                chatWindow.innerHTML = '';
                if (!messages.length) {
                    chatWindow.innerHTML = '<div style="color:#9CA3AF;">No messages yet.</div>';
                    return;
                }
                messages.forEach(msg => {
                    const div = document.createElement('div');
                    div.classList.add('message');
                    if (msg.sender_id === {{ auth()->id() }}) {
                        div.classList.add('self');
                    }
                    const bubble = document.createElement('div');
                    bubble.classList.add('bubble');
                    
                    // Display message text if present
                    if (msg.message) {
                        const messageText = document.createElement('div');
                        messageText.style.whiteSpace = 'pre-wrap';
                        messageText.textContent = msg.message;
                        bubble.appendChild(messageText);
                    }
                    
                    // Display file attachment if present
                    if (msg.file_name) {
                        const fileDiv = document.createElement('div');
                        fileDiv.style.marginTop = msg.message ? '8px' : '0';
                        fileDiv.style.paddingTop = msg.message ? '8px' : '0';
                        fileDiv.style.borderTop = msg.message ? '1px solid rgba(0,0,0,0.1)' : 'none';
                        
                        const fileLink = document.createElement('a');
                        fileLink.href = `/teacher/messages/${msg.id}/download`;
                        fileLink.target = '_blank';
                        fileLink.style.display = 'inline-flex';
                        fileLink.style.alignItems = 'center';
                        fileLink.style.gap = '6px';
                        fileLink.style.color = 'inherit';
                        fileLink.style.textDecoration = 'none';
                        fileLink.style.fontWeight = '500';
                        
                        const icon = document.createElement('i');
                        icon.className = 'fas fa-paperclip';
                        fileLink.appendChild(icon);
                        
                        const fileName = document.createElement('span');
                        fileName.textContent = escapeHtml(msg.file_name);
                        fileLink.appendChild(fileName);
                        
                        if (msg.file_size) {
                            const fileSize = document.createElement('span');
                            fileSize.style.fontSize = '0.75rem';
                            fileSize.style.opacity = '0.8';
                            fileSize.textContent = `(${(msg.file_size / 1024).toFixed(2)} KB)`;
                            fileLink.appendChild(fileSize);
                        }
                        
                        fileDiv.appendChild(fileLink);
                        bubble.appendChild(fileDiv);
                    }
                    
                    // If no message and no file, show placeholder
                    if (!msg.message && !msg.file_name) {
                        bubble.textContent = '(No content)';
                    }
                    
                    div.appendChild(bubble);
                    const meta = document.createElement('div');
                    meta.classList.add('meta');
                    meta.textContent = new Date(msg.created_at).toLocaleString();
                    div.appendChild(meta);
                    chatWindow.appendChild(div);
                });
                chatWindow.scrollTop = chatWindow.scrollHeight;
            }

            function fetchMessages(studentId) {
                fetch(`{{ route('teacher.messages.fetch') }}?student_id=${studentId}`)
                    .then(res => res.json())
                    .then(data => renderMessages(data.messages || []))
                    .catch(() => { chatWindow.innerHTML = '<div style="color:red;">Failed to load messages.</div>'; });
            }

            function sendMessage() {
                if (!selectedStudent) return;
                const text = chatMessage.value.trim();
                if (!text) return;
                sendBtn.disabled = true;
                fetch(`{{ route('teacher.messages.send') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ student_id: selectedStudent, message: text }),
                })
                .then(res => res.json())
                .then(() => {
                    chatMessage.value = '';
                    fetchMessages(selectedStudent);
                })
                .catch(() => alert('Failed to send message'))
                .finally(() => { sendBtn.disabled = false; });
            }

            studentItems.forEach(item => {
                item.addEventListener('click', () => {
                    studentItems.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                    selectedStudent = item.dataset.id;
                    chatMessage.disabled = false;
                    sendBtn.disabled = false;
                    chatSubtitle.textContent = 'Chatting with ' + item.querySelector('div').textContent;
                    chatWindow.innerHTML = '<div style="color:#9CA3AF;">Loading...</div>';
                    fetchMessages(selectedStudent);
                });
            });

            sendBtn.addEventListener('click', sendMessage);
            chatMessage.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        })();
    </script>
@endsection

