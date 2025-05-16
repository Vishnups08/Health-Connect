document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown functionality (same as in patient_dashboard.js)
    const profileToggle = document.getElementById('profile-toggle');
    const profileDropdown = document.getElementById('profile-dropdown');

    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        document.addEventListener('click', function(e) {
            if (profileDropdown.classList.contains('show') && !profileToggle.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    }
    
    // Chat functionality
    const chatMessages = document.getElementById('chat-messages');
    const userMessageInput = document.getElementById('user-message');
    const sendMessageBtn = document.getElementById('send-message');
    
    // Function to add a new message to the chat
    function addMessage(text, isUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isUser ? 'user' : 'assistant'}`;
        
        const avatarDiv = document.createElement('div');
        avatarDiv.className = 'message-avatar';
        
        const avatarIcon = document.createElement('i');
        avatarIcon.className = isUser ? 'fas fa-user' : 'fas fa-robot';
        avatarDiv.appendChild(avatarIcon);
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        
        const textDiv = document.createElement('div');
        textDiv.className = 'message-text';
        textDiv.textContent = text;
        
        const timeDiv = document.createElement('div');
        timeDiv.className = 'message-time';
        
        // Get current time
        const now = new Date();
        let hours = now.getHours();
        const minutes = now.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
        timeDiv.textContent = `${hours}:${formattedMinutes} ${ampm}`;
        
        contentDiv.appendChild(textDiv);
        contentDiv.appendChild(timeDiv);
        
        messageDiv.appendChild(avatarDiv);
        messageDiv.appendChild(contentDiv);
        
        chatMessages.appendChild(messageDiv);
        
        // Scroll to the bottom of the chat
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Sample responses for demo purposes
    const sampleResponses = [
        "I understand your concern. Based on the symptoms you've described, it could be a common cold or seasonal allergies. Make sure to stay hydrated and get plenty of rest.",
        "It's important to maintain a balanced diet rich in fruits, vegetables, and whole grains. This helps support your immune system and overall health.",
        "Regular exercise is key to maintaining good health. Even 30 minutes of moderate activity most days of the week can make a significant difference.",
        "If you're experiencing persistent symptoms, I would recommend consulting with a healthcare professional for a proper diagnosis.",
        "Sleep plays a crucial role in your health. Adults should aim for 7-9 hours of quality sleep each night."
    ];
    
    // Function to get a random response
    function getRandomResponse() {
        const randomIndex = Math.floor(Math.random() * sampleResponses.length);
        return sampleResponses[randomIndex];
    }
    
    // Handle sending a message
    function sendMessage() {
        const messageText = userMessageInput.value.trim();
        if (messageText) {
            // Add user message
            addMessage(messageText, true);
            userMessageInput.value = '';
            
            // Simulate assistant response after a short delay
            setTimeout(() => {
                addMessage(getRandomResponse());
            }, 1000);
        }
    }
    
    // Event listeners for sending messages
    sendMessageBtn.addEventListener('click', sendMessage);
    
    userMessageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    });
});