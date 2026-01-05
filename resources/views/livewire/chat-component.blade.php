<div class="card shadow-sm border-0" style="height: 80vh; max-height: 600px;">

    <div class="card-header bg-white py-3 border-bottom">
        @if ($receiver)
            <div class="d-flex align-items-center gap-3">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">{{ $receiver->username }}</h6>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-success rounded-circle p-1" style="width: 8px; height: 8px;"></span>
                    </div>
                </div>
            </div>
        @else
            <div class="d-flex align-items-center gap-3">
                <div class="skeleton bg-secondary opacity-25 rounded-circle" style="width: 45px; height: 45px;"></div>
                <div class="skeleton bg-secondary opacity-25 rounded" style="width: 100px; height: 20px;"></div>
            </div>
        @endif
    </div>

    <div class="card-body overflow-auto bg-light" id="chat-box" wire:poll.2000ms="$refresh"
        style="scroll-behavior: smooth;">

        <div class="d-flex flex-column gap-3">
            @forelse($messages as $msg)
                @php
                    $isMe = $msg->user_id == auth()->id();
                @endphp

                <div wire:key="msg-{{ $msg->id }}"
                    class="d-flex w-100 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="position-relative px-3 py-2 shadow-sm"
                        style="max-width: 75%; 
                                min-width: 100px;
                                border-radius: 15px; 
                                border-bottom-{{ $isMe ? 'right' : 'left' }}-radius: 2px;
                                background-color: {{ $isMe ? '#0d6efd' : '#ffffff' }}; 
                                color: {{ $isMe ? '#ffffff' : '#212529' }};">

                        <p class="mb-1 text-break">{{ $msg->message }}</p>

                        <div class="d-flex align-items-center justify-content-end gap-1"
                            style="font-size: 0.7rem; opacity: 0.8;">
                            <span x-data
                                x-text="new Date('{{ $msg->created_at->toIso8601String() }}')
                                  .toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false })">
                            </span>
                            @if ($isMe)
                                <i class="bi bi-check2-all"></i>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center mt-5">
                    <div class="bg-white p-3 rounded-circle shadow-sm d-inline-block mb-3">
                        <i class="bi bi-chat-heart text-primary fs-1"></i>
                    </div>
                    <p class="text-muted">
                        Belum ada pesan.<br>
                        Sapa <strong>{{ $receiver->name ?? 'Admin' }}</strong> sekarang!
                    </p>
                </div>
            @endforelse
        </div>
    </div>

<div class="card-footer bg-white py-3 border-top">
    <form wire:submit.prevent="sendMessage" class="d-flex align-items-center gap-2">

        <div class="position-relative w-100">
            <input type="text" wire:model="messageText"
                class="form-control rounded-pill bg-light border-0 py-2 ps-4 pe-5" 
                placeholder="Tulis pesan..."
                autocomplete="off" required>
        </div>

        <button type="submit"
            class="btn btn-primary rounded-pill shadow-sm fw-bold d-flex align-items-center justify-content-center"
            style="height: 45px; min-width: 100px;" 
            wire:loading.attr="disabled"
            wire:target="sendMessage">
            
            <span wire:loading.remove wire:target="sendMessage">
                Kirim <i class="bi bi-send-fill ms-1"></i>
            </span>

            <div wire:loading wire:target="sendMessage">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </div>
        </button>

    </form>
</div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const chatBox = document.getElementById('chat-box');
            let isAtBottom = true;

            const scrollToBottom = () => {
                if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
            }

            scrollToBottom();
            chatBox.addEventListener('scroll', () => {
                const distanceToBottom = chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight;
                isAtBottom = distanceToBottom < 50;
            });

            Livewire.hook('morph.updated', ({
                el,
                component
            }) => {
                if (isAtBottom) scrollToBottom();
            });

            Livewire.on('message-sent', () => {
                scrollToBottom();
                isAtBottom = true;
            });
        });
    </script>
</div>
