const tipoAcquisizione = document.getElementById("tipoAcquisizione");
const deliveryFields = document.getElementById("deliveryFields");

document.querySelectorAll(".js-search").forEach(search => {
    const container = search.closest("form") || document;

    const list = container.querySelector(".js-list");
    const selectedContainer = container.querySelector(".js-selected");

    const items = list.querySelectorAll(".js-item");

    search.addEventListener("keyup", () => {
        const value = search.value.toLowerCase();

        items.forEach(item => {
            const text = item.innerText.toLowerCase();

            item.style.display =
                text.includes(value) ? "flex" : "none";
        });
    });

    const updateSelected = () => {
        selectedContainer.innerHTML = "";

        items.forEach(item => {
            const checkbox = item.querySelector(".js-checkbox");

            if (checkbox && checkbox.checked) {
                const text = checkbox.nextElementSibling.innerText;
                const tag = document.createElement("div");
                tag.className = "selected-tag";
                tag.innerText = text;

                selectedContainer.appendChild(tag);
            }
        });
    };

    items.forEach(item => {
        const checkbox = item.querySelector(".js-checkbox");

        if (checkbox) {
            checkbox.addEventListener("change", updateSelected);
        }
    });

});

tipoAcquisizione.addEventListener("change", () => {
    if (tipoAcquisizione.value === 'DOMICILIO') {
        deliveryFields.style.display = "block";
    } else {
        deliveryFields.style.display = "none";
    }
});
