<div id="ai-assistant" 
    x-data="aiAssistant()"
    class="fixed bottom-6 right-6 z-50">
    <!-- Floating Button -->
    <button @click="toggleOpen"
        class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-full shadow-lg transition rounded-full">
        💬 Ask AI
    </button>

    <!-- Chat Modal -->
    <div x-show="open" x-cloak
        @click.outside="isDragging ? null : (open = window.innerWidth >= 640 ? open : false)"
        id="ai-modal"
        class="fixed bg-white rounded-lg shadow-lg border flex flex-col z-50"
        style="width: 350px; height: 500px; bottom: 100px; right: 24px;">

        <!-- Header -->
        <div id="drag-handle"
            @mousedown="startDrag"
            class="bg-blue-600 text-white p-3 flex justify-between items-center cursor-move sm:cursor-default">
            <h2 class="text-sm font-semibold">AI Assistant</h2>
            <button @click="open = false" class="text-white hover:text-gray-200">✖</button>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 p-3 flex flex-col overflow-hidden" x-ref="chatArea">
            <div id="chat-box" class="flex-1 overflow-y-auto space-y-2 mb-2 p-2 border rounded bg-gray-50">
                <template x-for="(message, index) in messages" :key="index">
                    <div class="p-2 rounded-lg text-sm max-w-[75%] break-words"
                         :class="{
                            'bg-blue-100 ml-auto text-right text-gray-900': message.sender === 'user',
                            'bg-gray-100 mr-auto text-left text-gray-900': message.sender === 'ai'
                         }">
                        <div x-html="message.text"></div>
                    </div>
                </template>
            </div>

            <!-- Input -->
            <form @submit.prevent="handleSubmit" id="chat-form" class="mt-auto flex gap-2">
                @csrf
                <input type="text" x-model="userInput" name="question" placeholder="Type a message..."
                    class="flex-1 border rounded-lg p-2 text-sm text-gray-900 placeholder-gray-400 bg-white focus:ring-blue-500 focus:border-blue-500" required>
                <button type="submit" :disabled="isLoading"
                    class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm disabled:bg-blue-400 disabled:cursor-not-allowed">
                    <span x-show="!isLoading">Send</span>
                    <span x-show="isLoading">...</span>
                </button>
            </form>

            <!-- Clear Button -->
            <button @click="clearChat" class="mt-2 text-xs text-red-500 hover:underline">Clear Chat</button>
        </div>
    </div>
</div>

{{-- Script for chat, draggable, and persistence --}}
<script>
function aiAssistant() {
    return {
        open: false,
        userInput: '',
        messages: JSON.parse(localStorage.getItem('ai_chat')) || [],
        isLoading: false,
        isDragging: false,
        offsetX: 0,
        offsetY: 0,

        init() {
            this.$watch('messages', () => this.scrollToBottom());
            this.handleResize();
            this.loadPosition();

            // Add mousemove and mouseup listeners to the document
            document.addEventListener('mousemove', this.drag.bind(this));
            document.addEventListener('mouseup', this.stopDrag.bind(this));
        },

        toggleOpen() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        scrollToBottom() {
            const chatBox = this.$refs.chatArea.querySelector('#chat-box');
            chatBox.scrollTop = chatBox.scrollHeight;
        },

        async handleSubmit() {
            const question = this.userInput.trim();
            if (!question || this.isLoading) return;

            this.isLoading = true;
            this.addMessage('user', question);
            this.userInput = '';
            
            const loadingText = '<span class="loading-dots">AI is typing<span>.</span><span>.</span><span>.</span></span>';
            this.addMessage('ai', loadingText);

            try {
                const response = await fetch("{{ route('ai.analyze') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ question })
                });
                const data = await response.json();
                this.updateLastMessage(data.answer ?? '⚠️ No response');
            } catch (error) {
                console.error('AI Assistant Error:', error);
                this.updateLastMessage('⚠️ Failed to connect to the server.');
            } finally {
                this.isLoading = false;
            }
        },

        addMessage(sender, text) {
            this.messages.push({ sender, text });
            localStorage.setItem('ai_chat', JSON.stringify(this.messages));
        },

        updateLastMessage(text) {
            this.messages[this.messages.length - 1].text = text;
            localStorage.setItem('ai_chat', JSON.stringify(this.messages));
        },

        clearChat() {
            this.messages = [];
            localStorage.removeItem('ai_chat');
        },

        // --- Draggable Logic ---
        loadPosition() {
            if (window.innerWidth < 640) return;
            const savedPos = JSON.parse(localStorage.getItem('ai_position'));
            if (savedPos) {
                const modal = this.$el.querySelector('#ai-modal');
                modal.style.left = savedPos.left;
                modal.style.top = savedPos.top;
                modal.style.right = 'auto';
                modal.style.bottom = 'auto';
            }
        },

        startDrag(e) {
            if (window.innerWidth < 640) return;
            this.isDragging = true;
            const modal = this.$el.querySelector('#ai-modal');
            this.offsetX = e.clientX - modal.getBoundingClientRect().left;
            this.offsetY = e.clientY - modal.getBoundingClientRect().top;
            document.body.style.userSelect = 'none';
        },

        drag(e) {
            if (!this.isDragging) return;
            const modal = this.$el.querySelector('#ai-modal');
            const left = (e.clientX - this.offsetX) + 'px';
            const top = (e.clientY - this.offsetY) + 'px';
            modal.style.left = left;
            modal.style.top = top;
            modal.style.right = 'auto';
            modal.style.bottom = 'auto';
            localStorage.setItem('ai_position', JSON.stringify({ left, top }));
        },

        stopDrag() {
            this.isDragging = false;
            document.body.style.userSelect = '';
        },

        handleResize() {
            const modal = this.$el.querySelector('#ai-modal');
            if (window.innerWidth < 640) {
                modal.style.width = '100%';
                modal.style.height = '100%';
                modal.style.top = '0';
                modal.style.left = '0';
                modal.style.right = '0';
                modal.style.bottom = '0';
                modal.style.borderRadius = '0';
            } else {
                // Restore default styles if needed
                modal.style.width = '350px';
                modal.style.height = '500px';
                modal.style.borderRadius = ''; // Or your default 'rounded-lg'
                this.loadPosition(); // Re-apply saved position
            }
        }
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.data('aiAssistant', aiAssistant);
});
</script>

<style>
.loading-dots span {
    animation: blink 1.5s infinite;
    opacity: 0;
}
.loading-dots span:nth-child(1) { animation-delay: 0s; }
.loading-dots span:nth-child(2) { animation-delay: 0.3s; }
.loading-dots span:nth-child(3) { animation-delay: 0.6s; }

@keyframes blink {
    0%, 20% { opacity: 0; }
    50% { opacity: 1; }
    100% { opacity: 0; }
}
</style>
