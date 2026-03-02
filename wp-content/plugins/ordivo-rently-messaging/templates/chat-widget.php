<div id="rently-chat-widget">
    <button id="rently-chat-toggle" class="rently-chat-toggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span class="rently-unread-badge" style="display:none;">0</span>
    </button>
    
    <div id="rently-chat-modal" class="rently-chat-modal" style="display:none;">
        <div class="rently-chat-header">
            <h3>Messages</h3>
            <button id="rently-chat-close" class="rently-chat-close">&times;</button>
        </div>
        
        <div class="rently-chat-body">
            <div id="rently-conversations-list" class="rently-conversations-list">
                <p class="rently-no-messages">No conversations yet</p>
            </div>
            
            <div id="rently-chat-window" class="rently-chat-window" style="display:none;">
                <div class="rently-chat-window-header">
                    <button id="rently-back-to-list" class="rently-back-btn">&larr;</button>
                    <span id="rently-chat-user-name"></span>
                </div>
                
                <div id="rently-messages-container" class="rently-messages-container"></div>
                
                <div class="rently-message-input">
                    <textarea id="rently-message-text" placeholder="Type a message..." rows="2"></textarea>
                    <button id="rently-send-message" class="rently-send-btn">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>
