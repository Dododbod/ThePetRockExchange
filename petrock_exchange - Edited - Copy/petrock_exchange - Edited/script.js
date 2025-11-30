// Toggle forms
document.getElementById("toggleRegister").addEventListener("click", () => {
    document.getElementById("authForm").style.display = "none";
    document.getElementById("registerForm").style.display = "block";
    document.getElementById("message").textContent = "";
});
document.getElementById("toggleLogin").addEventListener("click", () => {
    document.getElementById("authForm").style.display = "block";
    document.getElementById("registerForm").style.display = "none";
    document.getElementById("message").textContent = "";
});


// Login form
document.getElementById("authForm").addEventListener("submit", async e => {
    e.preventDefault();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const messageEl = document.getElementById("message");

    try {
        const res = await fetch("php/login.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password })
        });
        const result = await res.json();

        messageEl.textContent = result.message;
        messageEl.style.color = result.success ? "green" : "red";

        if (result.success) {
            window.location.href = "main.php"; // session will handle login
        }
    } catch (err) {
        console.error(err);
        messageEl.textContent = "Login error";
        messageEl.style.color = "red";
    }
});

// Register form
document.getElementById("registerForm").addEventListener("submit", async e => {
    e.preventDefault();
    const data = {
        fname: document.getElementById("fname").value.trim(),
        lname: document.getElementById("lname").value.trim(),
        dob: document.getElementById("dob").value,
        address: document.getElementById("address").value.trim(),
        email: document.getElementById("regEmail").value.trim(),
        password: document.getElementById("regPassword").value
    };
    const messageEl = document.getElementById("message");

    try {
        const res = await fetch("php/register.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        messageEl.textContent = result.message;
        messageEl.style.color = result.success ? "green" : "red";

        if (result.success) {
            setTimeout(() => {
                document.getElementById("authForm").style.display = "block";
                document.getElementById("registerForm").style.display = "none";
                messageEl.textContent = "";
            }, 1500);
        }
    } catch (err) {
        console.error(err);
        messageEl.textContent = "Registration error";
        messageEl.style.color = "red";
    }
});
/*
document.addEventListener("DOMContentLoaded", () => {
    const cardName = document.getElementById("cardName");
    if (!cardName) return;

    fetch("php/getCardInfo.php")
        .then(res => res.json())
        .then(data => {
            if (!data || Object.keys(data).length === 0) {
                console.log("No saved card found.");
                return;
            }

            document.getElementById("cardName").value = data.CardName || "";
            document.getElementById("cardNumber").value = data.CardNum || "";
            
            // Convert expiration date to YYYY-MM for month field
            let exp = data.ExpDate || "";
            if (exp.includes("/")) {
                let [mm, yy] = exp.split("/");
                exp = `20${yy}-${mm}`;
            }
            document.getElementById("expiration").value = exp;

            document.getElementById("ccv").value = data.CCV || "";
            document.getElementById("billingAddress").value = data.BillingZip || "";
        })
        .catch(err => console.error("Fetch error:", err));
});
*/