/**
 * مسیر فایل: assets/js/global-cart.js
 * نسخه نهایی و اصلاح شده (حذف HTML اضافی تخفیف)
 */

class CartManager {
    constructor() {
        this.cookieName = 'akamode_cart_v1';
        this.expiryDays = 7;
        this.cart = this.getCart();
        
        // المان‌های کانتینر در صفحات داخلی
        this.cartItemsWrapper = document.getElementById('cart-items-wrapper'); 
        this.checkoutItemsWrapper = document.getElementById('checkout-items-wrapper');
        
        this.init();
    }

    init() {
        this.updateHeaderBadge();
        
        // رندر صفحات در صورت وجود
        if (this.cartItemsWrapper) this.renderCartPage();
        if (this.checkoutItemsWrapper) this.renderCheckoutPage();

        // هماهنگی بین تب‌های مرورگر
        window.addEventListener('storage', () => {
            this.cart = this.getCart();
            this.updateHeaderBadge();
            if (this.cartItemsWrapper) this.renderCartPage();
            if (this.checkoutItemsWrapper) this.renderCheckoutPage();
        });
    }

    // --- مدیریت داده‌ها ---
    getCart() {
        const name = this.cookieName + "=";
        const decodedCookie = decodeURIComponent(document.cookie);
        const ca = decodedCookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(name) === 0) {
                try { return JSON.parse(c.substring(name.length, c.length)); } 
                catch (e) { return []; }
            }
        }
        return [];
    }

    saveCart(cart) {
        const d = new Date();
        d.setTime(d.getTime() + (this.expiryDays * 24 * 60 * 60 * 1000));
        const expires = "expires="+ d.toUTCString();
        document.cookie = `${this.cookieName}=${encodeURIComponent(JSON.stringify(cart))};${expires};path=/`;
        this.cart = cart;
        
        this.updateHeaderBadge();
        if (this.cartItemsWrapper) this.renderCartPage();
        if (this.checkoutItemsWrapper) this.renderCheckoutPage();
    }

    addItem(product) {
        const uniqueId = product.variant_id ? `${product.id}-${product.variant_id}` : `${product.id}`;
        const existingIndex = this.cart.findIndex(item => item.unique_id === uniqueId);

        if (existingIndex > -1) {
            this.cart[existingIndex].quantity += product.quantity;
        } else {
            product.unique_id = uniqueId;
            this.cart.push(product);
        }
        
        this.saveCart(this.cart);
        this.showToast('محصول به سبد خرید اضافه شد');
    }

    removeItem(uniqueId) {
        const newCart = this.cart.filter(item => item.unique_id !== uniqueId);
        this.saveCart(newCart);
    }

    updateQuantity(uniqueId, change) {
        const index = this.cart.findIndex(item => item.unique_id === uniqueId);
        if (index > -1) {
            this.cart[index].quantity += change;
            if (this.cart[index].quantity <= 0) {
                this.removeItem(uniqueId);
            } else {
                this.saveCart(this.cart);
            }
        }
    }

    getTotalPrice() {
        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    clearCart() {
        this.saveCart([]);
    }

    // --- مدیریت نمایش (UI) ---

    updateHeaderBadge() {
        const count = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        // سلکتور جدید بر اساس کلاس مشترک
        const badges = document.querySelectorAll('.shopping-bag-icon');
        
        badges.forEach(btn => {
            // حذف بج قبلی اگر هست تا دوباره نسازیم
            let oldBadge = btn.querySelector('.cart-badge-count');
            if(oldBadge) oldBadge.remove();

            if (count > 0) {
                let badge = document.createElement('span');
                badge.className = 'cart-badge-count';
                // استایل بج: قرمز، گرد، گوشه بالا
                badge.style.cssText = "position:absolute; top:-8px; right:-8px; background:#d32f2f; color:#fff; font-size:10px; border-radius:50%; width:18px; height:18px; display:flex; justify-content:center; align-items:center; z-index:10;";
                badge.innerText = count;
                
                // مطمئن شویم پوزیشن والد نسبی است
                if(getComputedStyle(btn).position === 'static') {
                    btn.style.position = 'relative';
                }
                btn.appendChild(badge);
            }
        });
    }

    renderCartPage() {
        if (!this.cartItemsWrapper) return;
        
        if (this.cart.length === 0) {
            this.cartItemsWrapper.innerHTML = '<div class="empty-msg" style="text-align:center; padding:30px;">سبد خرید شما خالی است.</div>';
            this.updateTotals(0);
            return;
        }

        let html = '';
        this.cart.forEach(item => {
            html += `
            <div class="item cart-item-row" data-variant-id="${item.variant_id || item.id}" data-qty="${item.quantity}">
                <div class="img"><img src="${item.image}" alt="${item.name}"></div>
                <div class="item-details">
                    <p class="title">${item.name}</p>
                    <p class="color">${item.variant_title || ''}</p>
                    <p class="price">${parseInt(item.price).toLocaleString()} تومان</p>
                </div>
                <div class="quantity">
                    <div class="counter">
                        <div class="plus" onclick="window.cartManager.updateQuantity('${item.unique_id}', 1)" style="cursor:pointer;">+</div>
                        <div class="number">${item.quantity}</div>
                        <div class="minus" onclick="window.cartManager.updateQuantity('${item.unique_id}', -1)" style="cursor:pointer;">-</div>
                    </div>
                    <div class="remove" onclick="window.cartManager.removeItem('${item.unique_id}')">
                        <i class="icon-trash" style="font-style:normal; cursor:pointer; color:red;">حذف</i>
                    </div>
                </div>
            </div>
            <div class="hr"></div>`;
        });
        
        // *** بخش مزاحم حذف شد: اینجا قبلا کد تخفیف دوم اضافه می‌شد ***

        this.cartItemsWrapper.innerHTML = html;
        this.updateTotals(this.getTotalPrice());
    }

    renderCheckoutPage() {
        if (!this.checkoutItemsWrapper) return;
        
        if (this.cart.length === 0) {
            this.checkoutItemsWrapper.innerHTML = '<p style="padding:10px;">سبد خالی است.</p>';
            return;
        }

        let html = '';
        this.cart.forEach(item => {
             html += `
             <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:12px;">
                <span>${item.name} <span style="color:#888;">x${item.quantity}</span></span>
                <span>${(item.price * item.quantity).toLocaleString()}</span>
             </div>`;
        });
        this.checkoutItemsWrapper.innerHTML = html;
        this.updateTotals(this.getTotalPrice());
    }

    updateTotals(total) {
        const totalEls = document.querySelectorAll('.total-price-display, .total-cart-price');
        totalEls.forEach(el => el.innerText = total.toLocaleString() + ' تومان');
        
        // تریگر کردن ایونت برای اینکه صفحه چک‌اوت بفهمد قیمت عوض شده
        window.dispatchEvent(new Event('cartUpdated'));
    }

    showToast(msg) {
        let toast = document.createElement('div');
        toast.innerText = msg;
        toast.style.cssText = "position:fixed; bottom:20px; left:50%; transform:translateX(-50%); background:#222; color:#fff; padding:12px 24px; border-radius:8px; z-index:99999; box-shadow:0 4px 12px rgba(0,0,0,0.15); font-size:14px;";
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    window.cartManager = new CartManager();
});