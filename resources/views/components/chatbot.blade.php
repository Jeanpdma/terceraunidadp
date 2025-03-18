{{-- resources/views/components/chatbot.blade.php --}}
<div class="chatbot-styles">
    <div class="fixed bottom-4 right-4 z-50 max-h-[calc(100vh-2rem)]">
        <!-- Header del chatbot -->
        <div class="bg-gray-800 p-4 rounded-t-lg shadow-md flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="material-icons text-indigo-400">chat</span>
                <h3 class="text-lg font-semibold text-white">Asistente</h3>
            </div>
            <button id="chatbot-toggle" class="text-gray-300 hover:text-white">
                <span class="material-icons">expand_less</span>
            </button>
        </div>

        <!-- Contenedor del chat -->
        <div id="chatbot-container" class="chat-container hidden">
            <div id="chatbox" class="chatbox"></div>
            <div class="input-container">
                <input type="text" id="userInput" placeholder="Escribe un mensaje...">
                <button onclick="sendMessage()" class="send-button">
                    <span class="material-icons">send</span>
                </button>
            </div>
        </div>
    </div>

    <style>
        .chatbot-styles .chat-container {
            width: 320px;
            background: #1f2937;
            border: 1px solid rgba(75, 85, 99, 0.5);
            border-top: none;
            border-radius: 0 0 0.5rem 0.5rem;
        }

        .chatbot-styles .chatbox {
            height: 300px;
            overflow-y: auto;
            padding: 1rem;
            background: #111827;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .chatbot-styles .chatbox::-webkit-scrollbar {
            width: 4px;
        }

        .chatbot-styles .chatbox::-webkit-scrollbar-track {
            background: #1f2937;
        }

        .chatbot-styles .chatbox::-webkit-scrollbar-thumb {
            background-color: #4f46e5;
            border-radius: 4px;
        }

        .chatbot-styles .input-container {
            padding: 0.75rem;
            background: #1f2937;
            border-top: 1px solid rgba(75, 85, 99, 0.5);
            display: flex;
            gap: 0.5rem;
        }

        .chatbot-styles input {
            flex: 1;
            padding: 0.5rem 0.75rem;
            border: 1px solid #4b5563;
            border-radius: 0.375rem;
            background: #374151;
            color: white;
            font-size: 0.875rem;
        }

        .chatbot-styles input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }

        .chatbot-styles input::placeholder {
            color: #9ca3af;
        }

        .chatbot-styles .send-button {
            background: #4f46e5;
            color: white;
            padding: 0.5rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .chatbot-styles .send-button:hover {
            background: #4338ca;
        }

        .chatbot-styles .user-message {
            align-self: flex-end;
            background: #4f46e5;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            border-bottom-right-radius: 0;
            max-width: 80%;
        }

        .chatbot-styles .bot-message {
            align-self: flex-start;
            background: #374151;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            border-bottom-left-radius: 0;
            max-width: 80%;
        }

        .chatbot-styles .typing {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .chatbot-styles .typing span {
            width: 3px;
            height: 3px;
            background: #9ca3af;
            border-radius: 50%;
            animation: bounce 1s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        @media (max-width: 640px) {
            .chatbot-styles .chat-container {
                width: 280px;
            }
            .chatbot-styles .chatbox {
                height: 250px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatbotToggle = document.getElementById('chatbot-toggle');
            const chatbotContainer = document.getElementById('chatbot-container');
            const sendButton = document.querySelector('.send-button');
            const userInput = document.getElementById('userInput');

            chatbotToggle.addEventListener('click', function() {
                chatbotContainer.classList.toggle('hidden');
                const icon = chatbotToggle.querySelector('.material-icons');
                icon.textContent = chatbotContainer.classList.contains('hidden') ? 'expand_less' : 'expand_more';
            });

            function sendMessage() {
                const message = userInput.value.trim();
                if (!message) return;

                addMessage('user', message);
                userInput.value = '';

                const typingIndicator = addTypingIndicator();

                fetch('/chatbot/response', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message })
                })
                .then(response => response.json())
                .then(data => {
                    removeTypingIndicator(typingIndicator);
                    addMessage('bot', data.message);
                    scrollChatbox();
                })
                .catch(error => {
                    removeTypingIndicator(typingIndicator);
                    console.error('Error:', error);
                });
            }

            sendButton.addEventListener('click', sendMessage);
            userInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });

            function addMessage(sender, text) {
                const chatbox = document.getElementById('chatbox');
                const messageElement = document.createElement('div');
                messageElement.className = `message ${sender}-message`;
                messageElement.textContent = text;
                chatbox.appendChild(messageElement);
                scrollChatbox();
            }

            function addTypingIndicator() {
                const chatbox = document.getElementById('chatbox');
                const typingElement = document.createElement('div');
                typingElement.className = 'message bot-message typing';
                typingElement.innerHTML = '<span></span><span></span><span></span>';
                chatbox.appendChild(typingElement);
                scrollChatbox();
                return typingElement;
            }

            function removeTypingIndicator(typingElement) {
                if (typingElement) typingElement.remove();
            }

            function scrollChatbox() {
                const chatbox = document.getElementById('chatbox');
                chatbox.scrollTop = chatbox.scrollHeight;
            }
        });
    </script>
</div>
