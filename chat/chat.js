document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('message-input');
    const messagesContainer = document.getElementById('messages');
    const userItems = document.querySelectorAll('.user-item');

    userItems.forEach(item => {
        item.addEventListener('click', function() {
            currentChatPartner = this.dataset.userId;
            document.getElementById('current-chat').textContent = 
                'Chat mit ' + this.textContent.trim();
            messageInput.disabled = false;
            loadMessages(currentChatPartner);
        });
    });

    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && this.value.trim() && currentChatPartner) {
            sendMessage(this.value.trim(), currentChatPartner);
            this.value = '';
        }
    });

    function loadMessages(partnerId) {
        fetch(`get_messages.php?partner_id=${partnerId}`)
            .then(response => response.json())
            .then(messages => {
                messagesContainer.innerHTML = '';
                messages.reverse().forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message ${msg.sender_id == currentUserId ? 'sent' : 'received'}`;
                    messageDiv.innerHTML = `
                        <div class="message-content">
                            <strong>${msg.sender_name}</strong>
                            <p>${msg.message}</p>
                            <small>${new Date(msg.timestamp).toLocaleTimeString()}</small>
                        </div>
                    `;
                    messagesContainer.appendChild(messageDiv);
                });
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            });
    }

    function sendMessage(message, partnerId) {
        const formData = new FormData();
        formData.append('message', message);
        formData.append('receiver_id', partnerId);

        fetch('send_message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMessages(partnerId);
            }
        });
    }

    // Automatische Aktualisierung
    setInterval(() => {
        if (currentChatPartner) {
            loadMessages(currentChatPartner);
        }
    }, 3000);
});

// Status-Update alle 30 Sekunden
setInterval(() => {
    fetch('update_status.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateUserStatus();
            }
        });
}, 30000);

function updateUserStatus() {
    fetch('get_status.php')
        .then(response => response.json())
        .then(statuses => {
            document.querySelectorAll('.user-item').forEach(userItem => {
                const userId = userItem.dataset.userId;
                const statusDot = userItem.querySelector('.user-status');
                if (statuses[userId] === 'online') {
                    statusDot.className = 'user-status status-online';
                } else {
                    statusDot.className = 'user-status status-offline';
                }
            });
        });
}

