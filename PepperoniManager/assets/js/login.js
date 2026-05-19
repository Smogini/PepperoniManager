function setMode(mode) {

    document.getElementById("modeInput").value = mode;

    document.body.setAttribute("data-mode", mode);

    const card = document.getElementById("card");
    card.style.transform = "scale(0.98)";

    setTimeout(() => {
        card.style.transform = "scale(1)";
    }, 150);
}
