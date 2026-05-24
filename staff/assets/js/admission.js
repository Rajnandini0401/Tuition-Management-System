document.addEventListener("DOMContentLoaded", function () {

    const dateField = document.getElementById("date");
    if (dateField) {
        dateField.innerText = new Date().toLocaleDateString("en-GB");
    }

    const serialField = document.getElementById("serial");
    if (serialField) {
        serialField.innerText = "UC-" + Math.floor(1000 + Math.random() * 9000);
    }

    const totalInput = document.getElementById("total");
    const paidInput = document.getElementById("paid");
    const balanceInput = document.getElementById("balance");

    function calculateBalance() {
        let total = parseFloat(totalInput.value) || 0;
        let paid = parseFloat(paidInput.value) || 0;
        let balance = total - paid;
        if (balance < 0) balance = 0;

        balanceInput.value = balance;

        if (balance > 0) {
            balanceInput.style.background = "#ffe5e5";
            balanceInput.style.color = "red";
        } else {
            balanceInput.style.background = "#e6ffe6";
            balanceInput.style.color = "green";
        }
    }

    totalInput.addEventListener("input", calculateBalance);
    paidInput.addEventListener("input", calculateBalance);

    const paymentMode = document.getElementById("payment_mode");
    const transactionBox = document.getElementById("transaction_box");

    paymentMode.addEventListener("change", function () {
        transactionBox.style.display =
            this.value === "Online" ? "block" : "none";
    });
    

});