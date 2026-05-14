// Helper to get cookie
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';')[0];
    return null;
}

// Function to update cart
async function updatecart(url, method, formData) {
    const options = {
        method: method.toUpperCase(),
        headers: { "Accept": "application/json" }
    };

    if (method.toUpperCase() !== "GET" && formData) {
        options.body = formData;
    }

    try {
        const response = await fetch(url, options);
        const json = await response.json().catch(() => null);

        if (!response.ok) {
            console.error("Request failed", json);
            return json;
        }
         const data = json;
    if (!data) return;

    const countEl = document.getElementById('item_number');

    if (json.count !== undefined) {
        if (countEl) {
            countEl.innerHTML = json.count;
        }
    }
        return json;
    } catch (err) {
        console.error("Fetch error:", err);
        return { message: "Network error", status: "error" };
    }
}

// Attach event listeners
Array.from(document.getElementsByClassName('cart_update')).forEach(btn => {
    btn.addEventListener('click', async () => {

        const form = document.getElementById("update_cart_form");
        if (!form) return;

        const url = form.action;
        const method = form.method;
        const formData = new FormData(form);

        const cartToken = getCookie('cart_token');
        formData.append("cart_token", cartToken);

        const json = await updatecart(url, method, formData);
        //update cart modal

        toast(json.message, json.status);

        if (btn.dataset.info === "checkout") {
                    //add cart_token to the request from cookie;
 const cartToken = getCookie('cart_token');

window.location.href =
    `${window.location.origin}/payment/checkout/${encodeURIComponent(cartToken)}`;


        } else {
            const modalEl = document.getElementById('cart_modal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();
        }
    });
});


// document.getElementById("cart_modal").addEventListener('click', async (evt) => {
//     if (evt.target && evt.target.id === 'pay') {
//         evt.preventDefault();
//         const form = document.getElementById("update_cart_form");
//         form.action ="/payment/initiate";
//         if (!form) return;
//         const url = form.action;
//         const method = form.method;
//         const formData = new FormData(form);

//         const cartToken = getCookie('cart_token');
//         formData.append("cart_token", cartToken);
//         $response = await get_general_data(url, method ,formData);
//         console.log($response);
    
//     }
// });
document.getElementById("place_order_btn").addEventListener('click', async (evt) => {
    evt.preventDefault();
    const form = document.getElementById("checkout_form");
    if (!form) return;
    //ensure all inputs are filled
    const inputs = form.querySelectorAll("input, select, textarea");
    for (let input of inputs) {
        if (input.hasAttribute("required") && !input.value) {
            //mark all empty required fields
            input.classList.add("is-invalid");
            toast("Please fill all required fields.", "error");
            return;
        }
    }

    const url = form.action;
    const method = form.method;
    const formData = new FormData(form);
    
    const cartToken = getCookie('cart_token');
    formData.append("cart_token", cartToken);
    const  response = await sendFormData(formData, url, method);

    if(response.status === "success"){
        const access_code = response.data.access_code;
        const confirm = await PaymentConfirmation({ access_code });
    }else{
        toast(response.message, response.status);
    }
    
    // const json = await updatecart(url, method, formData);
    // toast(json.message, json.status);
});
async function PaymentConfirmation({ access_code }) {
    if (!access_code) {
        toast("Access code is missing.", "error");
        return;
    }

    // Load Paystack script dynamically
    await loadPaystackScript();

    try {
        const popup = new PaystackPop();

        popup.resumeTransaction(access_code, {
            onSuccess: handleSuccess,
            onCancel: handleCancel,
            onLoad: handleLoad,
            onError: handleError,
        });

    } catch (error) {
        console.error(error);
        alert("Could not initialize Paystack transaction.");
    }
}

/* ------------------------
   DYNAMIC SCRIPT LOADER
------------------------ */
function loadPaystackScript() {
    return new Promise((resolve, reject) => {

        // Check if already loaded
        if (window.PaystackPop) {
            return resolve();
        }

        const script = document.createElement("script");
        script.src = "https://js.paystack.co/v2/inline.js";
        script.async = true;

        script.onload = () => resolve();
        script.onerror = () => reject("Failed to load Paystack script");

        document.body.appendChild(script);
    });
}

/* ------------------------
   CALLBACKS
------------------------ */
async function handleSuccess(response) {
    activateSpinner();

    const form = new FormData();
    form.append("reference", response.reference);

    const verifyResponse = await get_general_data(
        "/api/payment/verify",
        "POST",
        form,
    );

    if (verifyResponse.status === "success") {

        deactivateSpinner();
        document.getElementById("place_order_btn").disabled = true;
        //delete cart_token cookie
        document.cookie = "cart_token=; max-age=0; path=/;";

        toast("Payment successful!", "success");

        let countdown = 3;
        const interval = setInterval(() => {
            toast(`Redirecting in ${countdown}...`, "info");
            countdown--;

            if (countdown < 0) {
                clearInterval(interval);
                document.getElementById("place_order_btn").disabled =false;
                window.location.href = "/";
            }
        }, 1000);

    } else {
        deactivateSpinner();
        toast(verifyResponse.message || "Payment verification failed.", "error");
    }
}


function handleCancel() {
     toast('Transaction canceled by the user.', 'error');
}

function handleLoad() {
    toast('Transaction loading...', 'info');
}

function handleError(error) {
    console.error(error);
    toast(`Paystack Error: ${error.message}`, 'error');
}

function activateSpinner(){
     const spinner = document.getElementById("loadingSpinner");
    spinner.style.display = "flex"; // Show spinner
}
function deactivateSpinner(){
    const spinner = document.getElementById("loadingSpinner");
    spinner.style.display = "none";
}


