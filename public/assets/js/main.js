/**
* Template Name: NiceShop
* Template URL: https://bootstrapmade.com/niceshop-bootstrap-ecommerce-template/
* Updated: Jul 25 2025 with Bootstrap v5.3.7
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/

(function() {
  "use strict";

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  if (mobileNavToggleBtn) {
    mobileNavToggleBtn.addEventListener('click', mobileNavToogle);
  }

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });
  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Preloader
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  if (scrollTop) {
    scrollTop.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 600,
        easing: 'ease-in-out',
        once: true,
        mirror: false
      });
    }
  }
  window.addEventListener('load', aosInit);

  /**
   * Countdown timer
   */
  function updateCountDown(countDownItem) {
    const timeleft = new Date(countDownItem.getAttribute('data-count')).getTime() - new Date().getTime();

    const days = Math.floor(timeleft / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeleft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeleft % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeleft % (1000 * 60)) / 1000);

    const daysElement = countDownItem.querySelector('.count-days');
    const hoursElement = countDownItem.querySelector('.count-hours');
    const minutesElement = countDownItem.querySelector('.count-minutes');
    const secondsElement = countDownItem.querySelector('.count-seconds');

    if (daysElement) daysElement.innerHTML = days;
    if (hoursElement) hoursElement.innerHTML = hours;
    if (minutesElement) minutesElement.innerHTML = minutes;
    if (secondsElement) secondsElement.innerHTML = seconds;
  }

  document.querySelectorAll('.countdown').forEach(function(countDownItem) {
    updateCountDown(countDownItem);
    setInterval(function() {
      updateCountDown(countDownItem);
    }, 1000);
  });

  /**
   * Ecommerce Cart Functionality
   */
  function ecommerceCartTools() {
    "use strict";

    /* ============================================================
       CONFIG & VARIABLES
    ============================================================ */
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const sizeUrl = window.CART_SIZE_URL ?? null;

    /* ============================================================
       EVENT DELEGATION
    ============================================================ */
    document.addEventListener('click', handleClick);
    document.addEventListener('change', handleChange);

    // Initial summary calc on page load
    recalculateSummary();

    /* ============================================================
       CLICK HANDLER
    ============================================================ */
    async function handleClick(e) {
      /* Decrease quantity */
      const decreaseBtn = e.target.closest('.quantity-btn.decrease');
      if (decreaseBtn) {
        const item = decreaseBtn.closest('.cart-item');
        const input = item?.querySelector('.quantity-input');
        if (!input) return;

        let quantity = parseInt(input.value) || 1;
        if (quantity > 1) {
          input.value = quantity - 1;
          updateItemTotal(item);
          recalculateSummary();
        }
        return;
      }

      /* Increase quantity */
      const increaseBtn = e.target.closest('.quantity-btn.increase');
      if (increaseBtn) {
        const item = increaseBtn.closest('.cart-item');
        const input = item?.querySelector('.quantity-input');
        if (!input) return;

        let quantity = parseInt(input.value) || 1;
        const max = parseInt(input.getAttribute('max')) || 999;

        if (quantity < max) {
          input.value = quantity + 1;
          updateItemTotal(item);
          recalculateSummary();
        }
        return;
      }

      /* Remove Item */
      const removeBtn = e.target.closest('.remove-item');
      if (removeBtn) {
        const item = removeBtn.closest('.cart-item');
        if (!item) return;

        item.remove();
        recalculateSummary();
        return;
      }

      /* Update Cart / Checkout Button */
      const updateBtn = e.target.closest('.cart_update');
      if (updateBtn) {
        e.preventDefault();
        await submitCart(updateBtn);
        return;
      }

      /* Place Order */
      const orderBtn = e.target.closest('#place_order_btn');
      if (orderBtn) {
        e.preventDefault();
        await placeOrder();
        return;
      }
    }

    /* ============================================================
       CHANGE HANDLER
    ============================================================ */
    async function handleChange(e) {
      /* Manual quantity change */
      if (e.target.matches('.quantity-input')) {
        const input = e.target;
        const item = input.closest('.cart-item');
        let value = parseInt(input.value);
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || 999;

        if (isNaN(value) || value < min) {
          value = min;
          input.value = min;
        } else if (value > max) {
          value = max;
          input.value = max;
        }

        updateItemTotal(item);
        recalculateSummary();
        return;
      }

      /* Size change */
      if (e.target.matches('.size-selector')) {
        await updateCartItemSize(e.target);
        return;
      }
    }

    /* ============================================================
       ITEM TOTAL CALCULATION
    ============================================================ */
    function updateItemTotal(item) {
      if (!item) return;
      const qty = parseInt(item.querySelector('.quantity-input')?.value) || 1;
      const priceBtn = item.querySelector('.quantity-btn') || item.querySelector('.size-selector');
      const price = parseFloat(priceBtn?.dataset.price ?? item.dataset.price ?? 0);
      const total = qty * price;
      
      const totalLabel = item.querySelector('.item-total-badge');
      if (totalLabel) {
        totalLabel.textContent = formatMoney(total);
      }

      const hiddenTotal = item.querySelector('input[name="total"]');
      if (hiddenTotal) {
        hiddenTotal.value = total.toFixed(2);
      }
    }

    /* ============================================================
       COOKIE HELPER
    ============================================================ */
    function getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      return parts.length === 2 ? parts.pop().split(';')[0] : null;
    }

    /* ============================================================
       CART REQUEST
    ============================================================ */
    async function updateCart(url, method, formData) {
      const options = {
        method: method.toUpperCase(),
        headers: {
          "Accept": "application/json"
        }
      };

      if (method.toUpperCase() !== "GET" && formData) {
        options.body = formData;
      }

      try {
        const response = await fetch(url, options);
        const json = await response.json().catch(() => null);

        if (!response.ok) {
          console.error("Cart update failed", json);
          return json;
        }

        if (json?.count !== undefined) {
          const countEl = document.getElementById('item_number');
          if (countEl) {
            countEl.textContent = json.count;
          }
        }

        return json;
      } catch (error) {
        console.error("Cart request error:", error);
        return {
          status: "error",
          message: "Network error"
        };
      }
    }

    /* ============================================================
       SUBMIT CART
    ============================================================ */
    async function submitCart(button) {
      const form = document.getElementById('update_cart_form');
      if (!form) return;
      const formData = new FormData(form);
      const cartToken = getCookie('cart_token');
      if (cartToken) {
        formData.append('cart_token', cartToken);
      }

      const json = await updateCart(form.action, form.method, formData);
      if (!json) return;
      
      if (typeof window.toast === 'function') {
        window.toast(json.message, json.status);
      }

      if (button.dataset.info === "checkout") {
        const token = getCookie('cart_token');
        window.location.href = `/payment/checkout/${encodeURIComponent(token)}`;
        return;
      }

      const modal = document.getElementById('cart_modal');
      if (modal && typeof bootstrap !== 'undefined') {
        const instance = bootstrap.Modal.getInstance(modal);
        instance?.hide();
      }
    }

    /* ============================================================
       SIZE UPDATE AJAX
    ============================================================ */
    async function updateCartItemSize(select) {
      if (!sizeUrl) return;

      const cartItem = select.closest('.cart-item');
      if (!cartItem) return;

      const productId = select.dataset.productId;
      const cartItemId = select.dataset.cartItemId;
      const size = select.value;
      const originalHTML = select.innerHTML;

      select.disabled = true;
      select.innerHTML = `<option>Updating...</option>`;

      try {
        const response = await fetch(sizeUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken
          },
          body: JSON.stringify({
            cart_item_id: cartItemId,
            product_id: productId,
            size: size
          })
        });

        const json = await response.json().catch(() => null);

        if (!response.ok) {
          throw new Error(json?.message ?? "Unable to update size");
        }

        select.innerHTML = originalHTML;
        select.value = size;

        if (json?.new_price) {
          const price = parseFloat(json.new_price);
          const qty = parseInt(cartItem.querySelector('.quantity-input')?.value) || 1;
          const total = price * qty;

          const priceLabel = cartItem.querySelector('.current-price strong') ?? cartItem.querySelector('.col-lg-2 strong');
          if (priceLabel) {
            priceLabel.textContent = formatMoney(price);
          }

          const totalLabel = cartItem.querySelector('.item-total strong') ?? cartItem.querySelector('.col-lg-1 strong');
          if (totalLabel) {
            totalLabel.textContent = formatMoney(total);
          }

          cartItem.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.dataset.price = price;
          });

          const hiddenTotal = cartItem.querySelector('input[name="total"]');
          if (hiddenTotal) {
            hiddenTotal.value = total.toFixed(2);
          }
        }

        recalculateSummary();

        if (typeof window.toast === 'function') {
          window.toast(json.message ?? "Size updated", "success");
        }
      } catch (error) {
        console.error(error);
        select.innerHTML = originalHTML;
        select.value = size;

        if (typeof window.toast === 'function') {
          window.toast(error.message, "error");
        }
      } finally {
        select.disabled = false;
      }
    }

    /* ============================================================
       SUMMARY CALCULATION
    ============================================================ */
/* ============================================================
       SUMMARY CALCULATION
    ============================================================ */
    function recalculateSummary() {
      let subtotal = 0;
      let totalCount = 0;

      document.querySelectorAll('.cart-item').forEach(item => {
        const qtyInput = item.querySelector('.quantity-input');
        const qty = parseInt(qtyInput?.value) || 1;
        totalCount += qty;

        const totalInput = item.querySelector('input[name="total"]');
        if (totalInput) {
          subtotal += parseFloat(totalInput.value) || 0;
        } else {
          const priceBtn = item.querySelector('.quantity-btn') || item.querySelector('.size-selector');
          const price = parseFloat(priceBtn?.dataset.price ?? item.dataset.price ?? 0);
          subtotal += price * qty;
        }
      });

      const countEl = document.getElementById('item_number');
      if (countEl) {
        if (totalCount > 0) {
          countEl.classList.add('bg-warning');
          countEl.textContent = totalCount;
          countEl.style.display = 'inline-block';
        } else {
          countEl.textContent = '';
          countEl.style.display = 'none';
        }
      }

      const tax = 0;
      const discount = 0;
      const total = subtotal + tax - discount;

      updateSummaryDisplay(subtotal, tax, discount, total);
    }

    function updateSummaryDisplay(subtotal, tax, discount, total) {
      const subtotalEl = document.getElementById('cart_subtotal');
      const taxEl = document.getElementById('cart_tax');
      const discountEl = document.getElementById('cart_discount');
      const totalEl = document.getElementById('cart_total');

      if (subtotalEl) subtotalEl.textContent = formatMoney(subtotal);
      if (taxEl) taxEl.textContent = formatMoney(tax);
      if (discountEl) discountEl.textContent = formatMoney(discount);
      if (totalEl) totalEl.textContent = formatMoney(total);
    }

    function formatMoney(value) {
      return '₦' + Number(value).toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    }

    /* ============================================================
       CLEAR CART MODAL HANDLER
    ============================================================ */
    const confirmClearBtn = document.getElementById('confirmClearCartBtn');
    const cartContainer = document.querySelector('.cart-items-container');
    const clearCartModalEl = document.getElementById('clearCartModal');

    if (confirmClearBtn) {
      confirmClearBtn.addEventListener('click', async function () {
        try {
          confirmClearBtn.disabled = true;
          confirmClearBtn.textContent = 'Clearing...';

          const response = await fetch('/cart/clear', {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            }
          });

          const data = await response.json();

          if (response.ok && data.status === 'success') {
            if (window.bootstrap && clearCartModalEl) {
              const modalInstance = bootstrap.Modal.getInstance(clearCartModalEl);
              if (modalInstance) modalInstance.hide();
            }

            if (cartContainer) {
              cartContainer.innerHTML = `
                <div class="text-center py-5">
                    <p class="text-muted fs-5">Your cart is empty.</p>
                    <a href="/shop" class="btn btn-dark mt-2">Continue Shopping</a>
                </div>
              `;
            }

            recalculateSummary();
          } else {
            alert(data.message || 'Failed to clear cart.');
          }
        } catch (error) {
          console.error('Error clearing cart:', error);
          alert('An error occurred. Please try again.');
        } finally {
          confirmClearBtn.disabled = false;
          confirmClearBtn.textContent = 'Yes, Clear Cart';
        }
      });
    }

    /* ============================================================
       CHECKOUT & PLACE ORDER & HELPERS
    ============================================================ */
    function activateSpinner() {
      const spinner = document.getElementById("loadingSpinner");
      if (spinner) spinner.style.display = "flex";
    }

    function deactivateSpinner() {
      const spinner = document.getElementById("loadingSpinner");
      if (spinner) spinner.style.display = "none";
    }

    async function handleSuccess(response) {
      activateSpinner();

      const form = new FormData();
      form.append("reference", response.reference);

      try {
        const verifyResponse = await get_general_data(
          "/api/payment/verify",
          "POST",
          form
        );

        if (verifyResponse && verifyResponse.status === "success") {
          deactivateSpinner();
          const orderBtn = document.getElementById("place_order_btn");
          if (orderBtn) orderBtn.disabled = true;

          // Delete cart_token cookie
          document.cookie = "cart_token=; max-age=0; path=/;";

          if (typeof window.toast === 'function') {
            window.toast("Payment successful!", "success");
          }

          let countdown = 3;
          const interval = setInterval(() => {
            if (typeof window.toast === 'function') {
              window.toast(`Redirecting in ${countdown}...`, "info");
            }
            countdown--;

            if (countdown < 0) {
              clearInterval(interval);
              if (orderBtn) orderBtn.disabled = false;
              window.location.href = "/";
            }
          }, 1000);
        } else {
          deactivateSpinner();
          if (typeof window.toast === 'function') {
            window.toast(verifyResponse?.message || "Payment verification failed.", "error");
          }
        }
      } catch (error) {
        console.error(error);
        deactivateSpinner();
        if (typeof window.toast === 'function') {
          window.toast("Payment verification network error.", "error");
        }
      }
    }

    function handleCancel() {
      if (typeof window.toast === 'function') {
        window.toast('Transaction canceled by the user.', 'error');
      }
    }

    function handleLoad() {
      if (typeof window.toast === 'function') {
        window.toast('Transaction loading...', 'info');
      }
    }

    function handleError(error) {
      console.error(error);
      if (typeof window.toast === 'function') {
        window.toast(`Paystack Error: ${error.message}`, 'error');
      }
    }

    async function placeOrder() {
      const form = document.getElementById('checkout_form');
      if (!form) return;

      const inputs = form.querySelectorAll('input,select,textarea');
      for (const input of inputs) {
        if (input.hasAttribute('required') && !input.value.trim()) {
          input.classList.add('is-invalid');
          if (typeof window.toast === 'function') {
            window.toast("Please fill all required fields.", "error");
          }
          input.focus();
          return;
        } else {
          input.classList.remove('is-invalid');
        }
      }

      const formData = new FormData(form);
      const cartToken = getCookie('cart_token');
      if (cartToken) {
        formData.append('cart_token', cartToken);
      }

      try {
        activateSpinner();

        const response = await sendFormData(formData, form.action, form.method);

        if (response.status === "success") {
          await paymentConfirmation(response.data.access_code);
        } else {
          if (typeof window.toast === 'function') window.toast(response.message, response.status);
          deactivateSpinner();
        }
      } catch (error) {
        console.error(error);
        if (typeof window.toast === 'function') window.toast("Checkout failed", "error");
        deactivateSpinner();
      }
    }

    async function paymentConfirmation(accessCode) {
      if (!accessCode) {
        if (typeof window.toast === 'function') window.toast("Payment access code missing", "error");
        deactivateSpinner();
        return;
      }

      await loadPaystackScript();

      try {
        const popup = new PaystackPop();
        popup.resumeTransaction(accessCode, {
          onSuccess: handleSuccess,
          onCancel: handleCancel,
          onLoad: handleLoad,
          onError: handleError
        });
      } catch (error) {
        console.error(error);
        if (typeof window.toast === 'function') window.toast("Could not initialize payment", "error");
        deactivateSpinner();
      }
    }

    function loadPaystackScript() {
      return new Promise((resolve, reject) => {
        if (window.PaystackPop) {
          resolve();
          return;
        }
        const script = document.createElement("script");
        script.src = "https://js.paystack.co/v2/inline.js";
        script.async = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error("Failed to load Paystack script"));
        document.head.appendChild(script);
      });
    }
  }

  // Initialize ecommerce cart tools if function is defined
  if (typeof ecommerceCartTools === 'function') {
    ecommerceCartTools();
  }

})();