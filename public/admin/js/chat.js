// Connexion à Pusher
const pusher = new Pusher("a7bbf59b1b9e2ecf52ce", {
    cluster: "eu",
    encrypted: true
});

// Récupérer les données de l'utilisateur et de la consultation
const userRole = document.body.getAttribute('data-user-role'); // Rôle de l'utilisateur
const userId = document.body.getAttribute('data-user-id'); // ID de l'utilisateur
const consultationId = document.body.getAttribute('data-consultation-id'); // ID du patient (consultation)

// Définir le canal en fonction du rôle
let channelName = `chat_consultation_${consultationId}`;
let receiverId=consultationId; // ID du patient pour le récepteur du message
// S'abonner au canal
const channel = pusher.subscribe(channelName);

// Sélection des éléments HTML
const chatBox = document.getElementById("chat-box");
const messageInput = document.getElementById("message");
const sendButton = document.getElementById("send-button");

// Fonction pour ajouter un message dans l'interface
function appendMessage(sender, message, isMe) {
    const messageElement = document.createElement("p");

    // Vérifier si le message existe déjà pour éviter les doublons
    if (document.querySelector(`[data-message="${message}"]`)) {
        return; // Ne pas afficher le même message deux fois
    }

    const senderName = isMe 
        ? "Moi" 
        : (userRole === 'ROLE_MEDECIN' ? `Patient ${sender}` : `Médecin ${sender}`);

    messageElement.innerHTML = `<strong>${senderName}:</strong> ${message}`;
    messageElement.setAttribute("data-message", message); // Ajouter un attribut unique
    chatBox.appendChild(messageElement);
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Écoute des nouveaux messages via Pusher
channel.bind("new_message", function (data) {
    console.log("Message reçu via Pusher :", data);
    appendMessage(data.sender, data.message, false);
});

// Fonction pour envoyer un message
async function sendMessage() {
    const message = messageInput.value.trim();
    if (message === "") return;

    console.log("Envoi du message à l'API :", { message, sender: userId, receiver: receiverId, role: userRole });
    try {
        const response = await fetch("/send-message", {
            method: "POST",
            message, 
            sender: userId, 
            receiver: receiverId, 
            role: userRole,
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ message, sender: userId, receiver: receiverId, role: userRole })
        });

        const result = await response.json();
        console.log("Réponse du serveur :", result);

        if (result.success) {
            appendMessage(userId, message, true); // Afficher le message envoyé
            messageInput.value = "";
        } else {
            console.error("Erreur lors de l'envoi :", result.error);
        }
    } catch (error) {
        console.error("Erreur de connexion :", error);
    }
}

// Événements pour l'envoi de messages
sendButton.addEventListener("click", sendMessage);
messageInput.addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
        event.preventDefault();
        sendMessage();
    }
}); 