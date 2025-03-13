const cookieBox = document.querySelector(".cookie-wrapper"),
  buttons = document.querySelectorAll(".button");

const executeCodes = () => {
  console.log("Cookie-Skript wird ausgeführt!"); // Debugging
  if (document.cookie.includes("magrochat")) {
    console.log("Cookie gefunden, Box wird nicht angezeigt.");
    return;
  }

  console.log("Kein Cookie gefunden, zeige Box an!");
  cookieBox.classList.add("show");

  buttons.forEach((button) => {
    button.addEventListener("click", () => {
      cookieBox.classList.remove("show");

      if (button.id === "acceptBtn") {
        document.cookie = "cookieBy=magrochat; max-age=" + 60 * 60 * 24 * 30;
        console.log("Cookie gesetzt: magrochat");
      }
    });
  });
};

window.addEventListener("load", executeCodes);
