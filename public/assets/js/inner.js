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

        // Fixed: Call get_general_data and sync state instead of undefined updateCartCount
        get_general_data(url, method, formData).then(json => {
            if (json) {
                if (typeof syncCartState === 'function') {
                    syncCartState(json);
                }
                if (json.data && typeof CheckoutContent === 'function') {
                    CheckoutContent(json.data);
                }
            }
        });
    }
});


/* --------------------------------------------
   SEND POST/GET REQUEST
-------------------------------------------- */
async function sendFormData(formData, url, method) {
    const cartToken = getCookie("cart_token");
    
    // If it's a FormData object, append cart_token if missing
    if (cartToken && formData instanceof FormData && !formData.has("cart_token")) {
        formData.append("cart_token", cartToken);
    }
    
    const spinner = document.getElementById("loadingSpinner");
    if (spinner) spinner.style.display = "flex";
    
    try {
        // If it's a GET request, append cart_token as a query parameter
        let requestUrl = url;
        const options = {
            method: method.toUpperCase(),
            headers: { 
                "Accept": "application/json",
                ...(cartToken ? { "X-Cart-Token": cartToken } : {}) // Optional custom header support
            }
        };

        if (method.toUpperCase() !== "GET") {
            options.body = formData;
        } else if (cartToken) {
            const separator = url.includes('?') ? '&' : '?';
            requestUrl = `${url}${separator}cart_token=${encodeURIComponent(cartToken)}`;
        }

        const response = await fetch(requestUrl, options);
        const json = await response.json().catch(() => null);

        if (!response.ok) {
            console.error("Server Error:", json);
            return json;
        }

        if (typeof syncCartState === 'function') {
            syncCartState(json);
        }

        if (json.message) {
            toast(json.message, json.status);
        }

        return json;

    } catch (error) {
        console.error("Fetch error:", error);
    } finally {
        if (spinner) spinner.style.display = "none";
    }
}


/* --------------------------------------------
   STANDARD FORM SUBMISSION HANDLER
-------------------------------------------- */
async function Processform(event) {
    event.preventDefault();
    
    const form = event.target.closest("form");
    if (!form) {
        console.error("Button is not inside a form");
        return;
    }

    const url = form.action;
    const method = form.method;
    const formData = new FormData(form);
    
    const countEl = document.getElementById('item_number');
    if (countEl) {
        formData.append("total_quantity", countEl.innerHTML);
    }

    const response = await sendFormData(formData, url, method);
    
    const cartToken = getCookie('cart_token');
    const el = document.getElementById('cart_icon');

    if (!el) return;

    // Update cart link if token exists
    if (cartToken) {
        el.href = el.dataset.cartUrl + cartToken;
    }
    
    if (response && response.count !== undefined && countEl) {
        countEl.textContent = response.count;
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
document.querySelectorAll("form").forEach(form => {
    form.addEventListener("submit", Processform);
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
        const p = item.product || item; 
        const price = parseFloat(p.price || 0);
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
                            <button class="quantity-btn decrease" data-price="${price}"><i class="bi bi-dash"></i></button>
                            <input type="number" class="quantity-input" value="${qty}" min="1" name="quantity[]">
                            <input type="hidden" name="initial_quantity[]" value="${qty}">
                            <input type="hidden" name="product_id[]" value="${p.id}">
                            <input type="hidden" name="total" value="${total.toFixed(2)}">
                            <button class="quantity-btn increase" data-price="${price}"><i class="bi bi-plus"></i></button>
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

            const totalEl = container.querySelector(".item-total strong");
            const itemTotal = (price * qty).toFixed(2);
            totalEl.innerText = `$${itemTotal}`;

            updateSummaryTotals();
        };
    });

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
   PURECOUNTER SUMMARY ANIMATION (SINGLE SAFE VERSION)
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
            
            el.innerText = fields[id].toFixed(2);
        }
    });

    if (typeof PureCounter !== 'undefined') {
        new PureCounter();
    }
}


/* --------------------------------------------
   TOAST NOTIFICATION
-------------------------------------------- */
function toast(message, type = "success") {
    const id = "toast_" + Date.now();

    const bg = {
        success: "bg-success",
        error: "bg-danger",
        warning: "bg-warning text-dark",
        info: "bg-info text-dark"
    }[type] || "bg-primary";

    const toastHTML = `
        <div id="${id}" class="toast align-items-center text-white ${bg} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    document.getElementById("toast-container").insertAdjacentHTML("beforeend", toastHTML);

    const toastElement = document.getElementById(id);
    const bsToast = new bootstrap.Toast(toastElement, { delay: 3000 });
    bsToast.show();

    toastElement.addEventListener("hidden.bs.toast", () => toastElement.remove());
}