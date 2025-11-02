const messageInput = document.getElementById("message");
const messageForm = document.getElementById("message-form");

messageForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const message = messageInput.value;
  const request = await fetch("/discussion.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `message=${message}`,
  });

  const response = await request.json();
  if (response.erreur) {
    alert(response.erreur);
  } else {
    messageInput.value = "";
    const messageDiv = document.createElement("div");
    const date = new Date();
    const heure = date.getHours();
    const minutes = date.getMinutes();
    const secondes = date.getSeconds();
    messageDiv.classList.add(message);
    messageDiv.innerText = `[${heure}:${minutes}:${secondes}] ${message}`;
    document.getElementById("historique").appendChild(messageDiv);
  }
});

setInterval(async () => {
  const lastMessageId = parseInt(document.getElementById("historique-message").querySelectorAll("input.id").value);
  const url = new URL(window.location.href);
  url.searchParams.set("strategy", "polling");
  url.searchParams.set("lastMessageId", lastMessageId);
  const request = await fetch(url.toString());
  const response = await request.json();
  if (response.length > 0) {
    for (const message of response) {
      const messageDiv = document.createElement("div");
      const date = new Date(message.date);
      const heure = date.getHours();
      const minutes = date.getMinutes();
      const secondes = date.getSeconds();
      messageDiv.classList.add(message.message);
      messageDiv.innerText = `[${heure}:${minutes}:${secondes}] ${message.message}`;
      document.getElementById("historique").appendChild(messageDiv);
      lastMessageId = message.id;
    }
  }
}, 1000);
