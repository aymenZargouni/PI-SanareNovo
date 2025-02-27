// Connexion à Pusher
const pusher = new Pusher("a7bbf59b1b9e2ecf52ce", {
    cluster: "eu",
    encrypted: true
});

// Pour cet exemple, on suppose que l'utilisateur connecté est le médecin (ID = 4)
// Les patients devront s'abonner au canal du médecin
const doctorId = 4;
const userId = doctorId; // Si médecin, userId = 4

// Utilisation d'un canal de diffusion dédié pour le médecin
const channelName = `chat_doctor_${doctorId}`;
const channel = pusher.subscribe(channelName);

// Sélection des éléments HTML
const chatBox = document.getElementById("chat-box");
const messageInput = document.getElementById("message");
const sendButton = document.getElementById("send-button");

// Fonction pour ajouter un message dans l'interface
function appendMessage(sender, message) {
    const messageElement = document.createElement("p");
    let senderName = "";
    
    if (sender === userId) {
        senderName = "Moi (Médecin)";
    } else {
        senderName = `Patient ${sender}`;
    }
    
    messageElement.innerHTML = `<strong>${senderName}:</strong> ${message}`;
    chatBox.appendChild(messageElement);
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Écoute des nouveaux messages via Pusher
channel.bind("new_message", function (data) {
    console.log("Message reçu via Pusher :", data);
    appendMessage(data.sender, data.message);
});

// Fonction pour envoyer un message
async function sendMessage() {
    const message = messageInput.value.trim();
    if (message === "") return;

    console.log("Envoi du message à l'API :", { message, sender: userId });
    try {
        const response = await fetch("/send-message", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ message, sender: userId })
        });

        const result = await response.json();
        console.log("Réponse du serveur :", result);

        if (result.success) {
            appendMessage(userId, message); // Correction ici : envoi du bon ID
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
