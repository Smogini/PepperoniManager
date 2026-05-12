document.getElementById("pizzaSearch")
    .addEventListener("input", function () {

    let filter =
        this.value.toLowerCase();

    let rows =
        document.querySelectorAll("#pizzaList .ingredient-row");

    rows.forEach(row => {

        let text =
            row.innerText.toLowerCase();

        row.style.display =
            text.includes(filter)
                ? "flex"
                : "none";
    });
});