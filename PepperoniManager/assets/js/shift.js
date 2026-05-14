const search = document.getElementById("employeeSearch");

search.addEventListener("keyup", function () {

    const value = this.value.toLowerCase();

    const rows = document.querySelectorAll(".employee-box");

    rows.forEach(row => {

        const text = row.innerText.toLowerCase();

        row.style.display =
            text.includes(value)
            ? "block"
            : "none";
    });
});