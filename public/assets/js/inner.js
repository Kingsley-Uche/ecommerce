document.addEventListener('DOMContentLoaded', function () {

    const cartToken = getCookie('cart_token');
    const el = document.getElementById('cart_icon');

    if (!el) return;

    // Update cart link if token exists
    if (cartToken) {
        el.href = el.dataset.cartUrl + cartToken;

        const url = window.location.origin + "/api/cart";
        const method = "POST";

        const formData = new FormData();
        formData.append("cart_token", cartToken);

        updateCartCount(url, method, formData);
    }
});


/* --------------------------------------------
   SEND POST/GET REQUEST
-------------------------------------------- */
async function sendFormData(formData, url, method) {

    // Always attach cart_token (DO NOT append twice)
    const cartToken = getCookie("cart_token");
    if (cartToken && !formData.has("cart_token")) {
        formData.append("cart_token", cartToken);
    }
    const spinner = document.getElementById("loadingSpinner");
    spinner.style.display = "flex"; // Show spinner

    try {
        const response = await fetch(url, {
            method: method.toUpperCase(),
            body: formData,
            headers: { "Accept": "application/json" }
        });

        const json = await response.json().catch(() => null);

        if (!response.ok) {
            console.error("Server Error:", json);
            return json;
        }

        // Save token if server sends a new one
        if (json.data.cart_token) {
            document.cookie = `cart_token=${json.data.cart_token}; path=/; max-age=${30 * 24 * 60 * 60}`;
        }
             const data = json
    if (!data) return;

    const countEl = document.getElementById('item_number');

    if (data.count !== undefined) {
        if (countEl) {
            countEl.classList.add('bg-warning');
            countEl.innerHTML = data.count;
        }
    }

            toast(json.message, json.status);

        return json;

    } catch (error) {
        console.error("Fetch error:", error);
    } finally {
        spinner.style.display = "none"; // Hide spinner
    }
}



/* --------------------------------------------
   STANDARD FORM SUBMISSION HANDLER
-------------------------------------------- */
async function Processform(event) {
    event.preventDefault();
    

    const form = event.target.closest("form")
    if (!form) {
        console.error("Button is not inside a form");
        return;
    }

    const url = form.action;
    const method = form.method;
    const formData = new FormData(form);
    const countEl = document.getElementById('item_number').innerHTML;
    formData.append("total_quantity", countEl);
    


    await sendFormData(formData, url, method);
     const cartToken = getCookie('cart_token');
    const el = document.getElementById('cart_icon');

    if (!el) return;

    // Update cart link if token exists
    if (cartToken) {
        el.href = el.dataset.cartUrl + cartToken;

        const url = window.location.origin + "/api/cart";
        const method = "POST";

        const formData = new FormData();
        formData.append("cart_token", cartToken);

        updateCartCount(url, method, formData);
    }
}


/* --------------------------------------------
   GET COOKIE
-------------------------------------------- */
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(";").shift();
    return null;
}


/* --------------------------------------------
   ADD BUTTON EVENT LISTENERS
-------------------------------------------- */
document.querySelectorAll('.form_button').forEach(btn => {
    btn.addEventListener('click', Processform);
});


/* --------------------------------------------
   GENERIC GET/POST
-------------------------------------------- */
async function get_general_data(url, method = "GET", formData = null) {

    const options = {
        method: method.toUpperCase(),
        headers: { "Accept": "application/json" }
    };

    if (method !== "GET" && formData) {
        options.body = formData;
    }

    const response = await fetch(url, options);
    const json = await response.json().catch(() => null);

    if (!response.ok) {
        console.error("Request failed", json);
        return json;
    }

    return json;
}


/* --------------------------------------------
   LOAD CART COUNT + CALL CART CONTENT LOADER
-------------------------------------------- */
async function updateCartCount(url, method, formData) {

    const data = await get_general_data(url, method, formData);
    if (!data) return;

    const countEl = document.getElementById('item_number');

    if (data.count !== undefined) {
        if (countEl) {
            countEl.classList.add('bg-warning');
            countEl.innerHTML = data.count;
        }
    }

    // Load items inside checkout/cart page
    await CheckoutContent(data.data);

    // Run animated summary counters (purecounter)
    //updateCartSummary(data);
}


/* --------------------------------------------
   RENDER CART CONTENT
-------------------------------------------- */
async function CheckoutContent(data) {
    const container = document.getElementById("cart_items_container");


     
    if (!container) return;

    container.innerHTML = "";

    if (!data || data.length === 0) {
        container.innerHTML = `<div class="text-center py-4 text-muted">No items in cart</div>`;
        updateCartSummary({ subtotal: 0, tax: 0, discount: 0, total: 0 });
        return;
    }
    let subtotal = 0;

    data.forEach(item => {
        const p = item.product;
        const price = parseFloat(p.price);
        const qty = item.quantity ?? 1;
        const image = p.images?.length
            ? `/storage/${p.images[0].image_path}`
            : "/default.png";

        const total = price * qty;
        subtotal += total;

        container.insertAdjacentHTML("beforeend", 
            `<div class="cart-item border-bottom py-3">
                <div class="row align-items-center">

                    <div class="col-lg-6 col-12">
                        <div class="product-info d-flex align-items-center">
                            <img src="${image}" width="70" class="img-fluid me-3">
                            <div>
                                <h6>${p.name}</h6>
                                <small>${p.description ?? ""}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-4 text-center">
                        <span class="current-price"><strong>$${price.toFixed(2)}</strong></span>
                    </div>

                    <div class="col-lg-2 col-4 text-center m-1">
                        <div class="quantity-selector">
                       
                            <button class="quantity-btn decrease" data-price="${price} text-small "><i class="bi bi-dash"></i></button>
                            <input type="number" class="quantity-input" value="${qty}" min="1" name='quantity[]'>
                            <input type ="hidden" name ="initial_quantity[]" value="${qty}">
                            <input type ="hidden" name ="product_id[]" value ="${p.id}">
                             <input type ="hidden" name ="total" value ="${total.toFixed(2)}">
                        
                            <button class="quantity-btn increase" data-price="${price} txt-small"><i class="bi bi-plus"></i></button>
                        </div>
                
                    </div>

                    <div class="col-lg-2 col-4 text-center item-total">
                        <strong>$${total.toFixed(2)}</strong>
                    </div>
                
                </div>
            </div>
        `);
    });

    attachQuantityListeners();
    updateCartSummary({ subtotal: subtotal, tax: subtotal * 0.1, discount: 0, total: subtotal * 1.1 });
}

/* --------------------------------------------
   DYNAMIC QUANTITY UPDATE
-------------------------------------------- */
function attachQuantityListeners() {
    document.querySelectorAll(".quantity-btn").forEach(button => {
        button.onclick = function (evt) {
            evt.preventDefault();
            const container = button.closest(".cart-item");
            const input = container.querySelector(".quantity-input");
            let qty = parseInt(input.value);
            const price = parseFloat(button.dataset.price);

            if (button.classList.contains("increase")) qty++;
            if (button.classList.contains("decrease") && qty > 1) qty--;

            input.value = qty;

            // Update item total
            const totalEl = container.querySelector(".item-total strong");
            const itemTotal = (price * qty).toFixed(2);
            totalEl.innerText = `$${itemTotal}`;

            // Update cart summary
            updateSummaryTotals();
        };
    });

    // Optional: update total if user manually changes input
    document.querySelectorAll(".quantity-input").forEach(input => {
        input.onchange = function () {
            let qty = parseInt(input.value) || 1;
            input.value = qty;
            const container = input.closest(".cart-item");
            const price = parseFloat(container.querySelector(".quantity-btn").dataset.price);

            const totalEl = container.querySelector(".item-total strong");
            const itemTotal = (price * qty).toFixed(2);
            totalEl.innerText = `$${itemTotal}`;

            updateSummaryTotals();
        };
    });
}

/* --------------------------------------------
   UPDATE SUMMARY DYNAMICALLY
-------------------------------------------- */
function updateSummaryTotals() {
    let subtotal = 0;

    document.querySelectorAll(".cart-item").forEach(item => {
        const total = parseFloat(item.querySelector(".item-total strong").innerText.replace("$", ""));
        subtotal += total;
    });

    const tax = subtotal * 0.1;
    const discount = 0;
    const total = subtotal + tax - discount;

    updateCartSummary({ subtotal, tax, discount, total });
}

/* --------------------------------------------
   PURECOUNTER SUMMARY ANIMATION
-------------------------------------------- */
function updateCartSummary(data) {
    if (!data) return;

    const fields = {
        cart_subtotal: data.subtotal ?? 0,
        cart_tax: data.tax ?? 0,
        cart_discount: data.discount ?? 0,
        cart_total: data.total ?? 0
    };

    Object.keys(fields).forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.setAttribute("data-purecounter-start", el.innerText.replace(/[^0-9.]/g, "") || 0);
            el.setAttribute("data-purecounter-end", fields[id].toFixed(2));
            el.setAttribute("data-purecounter-duration", 1);
        }
    });

    new PureCounter();
}

function toast(message, type = "success") {
    // Create unique ID for each toast
    const id = "toast_" + Date.now();

    // Bootstrap color types
    const bg = {
        success: "bg-success",
        error: "bg-danger",
        warning: "bg-warning text-dark",
        info: "bg-info text-dark"
    }[type] || "bg-primary";

    // Toast HTML element
    const toastHTML = `
        <div id="${id}" class="toast align-items-center text-white ${bg} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    // Add toast to container
    document.getElementById("toast-container").insertAdjacentHTML("beforeend", toastHTML);

    // Show toast
    const toastElement = document.getElementById(id);
    const bsToast = new bootstrap.Toast(toastElement, { delay: 3000 });
    bsToast.show();

    // Auto-remove from DOM after it hides
    toastElement.addEventListener("hidden.bs.toast", () => toastElement.remove());
}

