const search = document.getElementById("ingredientSearch");

const rows = document.querySelectorAll(".ingredient-row");

const selectedContainer = document.getElementById("selectedIngredients");

search.addEventListener("keyup", () => {
    const value = search.value.toLowerCase();

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();

        row.style.display = text.includes(value) ? "flex" : "none";
    });
});

function updateSelected() {
    selectedContainer.innerHTML = "";

    document
        .querySelectorAll(".ingredient-row input:checked")
        .forEach(checkbox => {
            const text = checkbox.nextElementSibling.innerText;
            const tag = document.createElement("div");
            tag.className = "selected-tag";
            tag.innerText = text;
            selectedContainer.appendChild(tag);
        });
}

document
    .querySelectorAll(".ingredient-row input")
    .forEach(checkbox => {
        checkbox.addEventListener(
            "change",
            updateSelected
        );
    });