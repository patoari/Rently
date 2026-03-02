jQuery(document).ready(function($) {
    let currentChatUserId = null;
    let lastMessageId = 0;
    let pollInterval = null;
    
    // Toggle chat modal
    $('#rently-chat-toggle').on('click', function() {
        $('#rently-chat-modal').toggle();
        if ($('#rently-chat-modal').is(':visible')) {
            loadConversations();
        }
    });
    
    $('#rently-chat-close').on('click', function() {
        $('#rently-chat-modal').hide();
    });
    
    // Load conversations
    function loadConversations() {
        $.ajax({
            url: rentlyMessaging.ajaxUrl,
            type: 'POST',
            data: {
                action: 'rently_get_conversations',
                nonce: rentlyMessaging.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayConversations(response.data);
                }
            }
        });
    }
    
    function displayConversations(conversations) {
        const $list = $('#rently-conversations-list');
        $list.empty();
        
        if (conversations.length === 0) {
            $list.html('<p class="rently-no-messages">No conversations yet</p>');
            return;
        }
        
        conversations.forEach(function(conv) {
            const $item = $('<div class="rently-conversation-item"></div>');
            $item.html(
                '<div class="rently-conversation-name">' + conv.display_name + '</div>' +
                '<div class="rently-conversation-preview">' + (conv.last_message || '') + '</div>'
            );
            $item.data('user-id', conv.user_id);
            $item.data('user-name', conv.display_name);
            $list.append($item);
        });
    }
    
    // Open chat with user
    $(document).on('click', '.rently-conversation-item', function() {
        currentChatUserId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        
        $('#rently-chat-user-name').text(userName);
        $('#rently-conversations-list').hide();
        $('#rently-chat-window').show();
        
        lastMessageId = 0;
        $('#rently-messages-container').empty();
        loadMessages();
        startPolling();
    });
    
    // Back to conversations
    $('#rently-back-to-list').on('click', function() {
        stopPolling();
        $('#rently-chat-window').hide();
        $('#rently-conversations-list').show();
        currentChatUserId = null;
    });
    
    // Load messages
    function loadMessages() {
        if (!currentChatUserId) return;
        
        $.ajax({
            url: rentlyMessaging.ajaxUrl,
            type: 'POST',
            data: {
                action: 'rently_get_messages',
                nonce: rentlyMessaging.nonce,
                user_id: currentChatUserId,
                last_id: lastMessageId
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    displayMessages(response.data);
                    lastMessageId = response.data[response.data.length - 1].id;
                }
            }
        });
    }
    
    function displayMessages(messages) {
        const $container = $('#rently-messages-container');
        
        messages.forEach(function(msg) {
            const isSent = msg.sender_id == rentlyMessaging.currentUserId;
            const $msg = $('<div class="rently-message ' + (isSent ? 'sent' : 'received') + '"></div>');
            $msg.html('<div class="rently-message-bubble">' + escapeHtml(msg.message) + '</div>');
            $container.append($msg);
        });
        
        scrollToBottom();
    }
    
    // Send message
    $('#rently-send-message').on('click', sendMessage);
    $('#rently-message-text').on('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    function sendMessage() {
        const message = $('#rently-message-text').val().trim();
        if (!message || !currentChatUserId) return;
        
        $.ajax({
            url: rentlyMessaging.ajaxUrl,
            type: 'POST',
            data: {
                action: 'rently_send_message',
                nonce: rentlyMessaging.nonce,
                receiver_id: currentChatUserId,
                message: message
            },
            success: function(response) {
                if (response.success) {
                    $('#rently-message-text').val('');
                    loadMessages();
                }
            }
        });
    }
    
    // Polling for new messages
    function startPolling() {
        pollInterval = setInterval(loadMessages, 3000);
    }
    
    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }
    
    function scrollToBottom() {
        const $container = $('#rently-messages-container');
        $container.scrollTop($container[0].scrollHeight);
    }
    
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
