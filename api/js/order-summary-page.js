// Renders the Order Summary page from localStorage cart + user data. Uses
// textContent + createElement to avoid any XSS surface from product names
// or address fields. Place Order shows an inline confirmation modal — no
// payment flow.

(function () {
  var cart = window.BV.readCart();
  var user = {};
  try { user = JSON.parse(localStorage.getItem('user') || '{}') || {}; } catch (e) {}

  if (cart.length === 0) { location.replace('/cart.php'); return; }
  if (!user.name) { location.replace('/checkout.php'); return; }

  function inr(n) { return Number(n || 0).toLocaleString('en-IN'); }

  function el(tag, props, children) {
    var node = document.createElement(tag);
    if (props) {
      for (var k in props) {
        if (k === 'className') node.className = props[k];
        else if (k === 'text') node.textContent = props[k];
        else if (k.indexOf('data-') === 0) node.setAttribute(k, props[k]);
        else if (k === 'style') node.setAttribute('style', props[k]);
        else node[k] = props[k];
      }
    }
    (children || []).forEach(function (c) {
      if (c == null) return;
      if (typeof c === 'string') node.appendChild(document.createTextNode(c));
      else node.appendChild(c);
    });
    return node;
  }

  // ── Delivered to: ──────────────────────────────────────────────
  var addr = [user.house, user.colonny, user.city, user.state, user.pincode].filter(Boolean).join(', ');
  var $dt = document.getElementById('deliver-to');
  $dt.replaceChildren(
    el('h4', { className: 'customer-name', style: 'font-size:14px;margin:4px 0', text: 'name: ' + (user.name || '') }),
    el('div', { className: 'mb-2 customer-address', style: 'font-size:13px;color:#555', text: 'address: ' + addr }),
    el('div', { className: 'customer-contact', style: 'font-size:13px;color:#555', text: 'mobile: ' + (user.phone || '') })
  );

  // ── Order items ───────────────────────────────────────────────
  var sellingTotal = cart.reduce(function (s, i) { return s + i.price * i.qty; }, 0);
  var mrpTotal = cart.reduce(function (s, i) { return s + (i.mrp || i.price) * i.qty; }, 0);
  var discount = mrpTotal - sellingTotal;
  var itemCount = cart.reduce(function (s, i) { return s + i.qty; }, 0);

  function renderOrderItem(it) {
    var off = it.mrp ? Math.round(((it.mrp - it.price) / it.mrp) * 100) : 0;
    var priceLine = el('div', { className: 'price flex' }, []);
    if (off > 0) {
      priceLine.appendChild(el('span', { className: 'discount', text: off + '% Off' }));
      priceLine.appendChild(document.createTextNode('  '));
    }
    priceLine.appendChild(el('span', { className: 'strike mrp', text: '₹ ' + inr(it.mrp || 0) }));
    priceLine.appendChild(document.createTextNode('  '));
    priceLine.appendChild(el('span', { className: 'selling_price', text: '₹ ' + inr(it.price * it.qty) }));

    return el('li', { className: 'list-group-item px-0', style: 'border-bottom:1px solid #f0f0f0;padding-bottom:12px;margin-bottom:12px' }, [
      el('div', { className: 'flex recommended-product' }, [
        el('img', { src: it.image || '', alt: '' }),
        el('div', { className: 'description' }, [
          el('div', { className: 'product-title mb-1', text: it.name || '' }),
          el('img', { src: '/assets/catogary/assured.png', width: '77', className: 'img-fluid', alt: 'F-Assured' }),
        ]),
      ]),
      el('div', { className: 'flex recommended-product mt-3' }, [
        el('div', { className: 'timer qty mx-4', text: 'Qty: ' + it.qty }),
        el('div', { className: 'description' }, [priceLine]),
      ]),
    ]);
  }

  var $items = document.getElementById('order-items');
  $items.replaceChildren.apply($items, cart.map(renderOrderItem));

  // ── Price details ─────────────────────────────────────────────
  function priceRow(title, value, valueClass, titleClass, rowClass) {
    return el('div', { className: 'product-price-list my-3 ' + (rowClass || '') }, [
      el('span', { className: 'title ' + (titleClass || ''), text: title }),
      el('span', { className: 'data ' + (valueClass || ''), text: value }),
    ]);
  }
  var mrpRowValueStyle = el('span', { className: 'data', style: 'text-decoration:line-through;color:#878787', text: '₹ ' + inr(mrpTotal) });
  var mrpRow = el('div', { className: 'product-price-list my-3' }, [
    el('span', { className: 'title', text: 'Price (' + itemCount + ' item' + (itemCount === 1 ? '' : 's') + ')' }),
    mrpRowValueStyle,
  ]);
  var savedRow = el('div', { className: 'product-price-list mt-3 pt-3 saved-div' }, [
    el('span', { className: 'text-success' }, [
      'You will save ',
      el('span', { className: 'discount-amt', text: '- ₹ ' + inr(discount) }),
      ' on this order',
    ]),
  ]);

  var $pr = document.getElementById('price-rows');
  $pr.replaceChildren(
    mrpRow,
    priceRow('Discount', '- ₹ ' + inr(discount), 'text-success'),
    priceRow('Delivery Charges', 'FREE Delivery', 'text-success'),
    priceRow('Total Amount', '₹ ' + inr(sellingTotal), 'selling_price', '', 'pt-3 total'),
    savedRow
  );

  document.getElementById('footer-strike').textContent = '- ₹ ' + inr(discount);
  document.getElementById('footer-selling').textContent = '₹ ' + inr(sellingTotal);

  // ── Recommendations ───────────────────────────────────────────
  var exclude = cart.map(function (i) { return i.pid; }).join(',');
  fetch('/recommendations.php?anchor=' + encodeURIComponent(cart[0].pid) + '&exclude=' + encodeURIComponent(exclude) + '&limit=10')
    .then(function (r) { return r.json(); })
    .then(function (j) {
      var products = (j && j.products) || [];
      if (products.length === 0) return;
      document.getElementById('reco-section').style.display = '';
      if (j.category) {
        document.querySelector('#reco-section .subtitle').textContent = "From '" + j.category + "' category";
      }
      var $grid = document.getElementById('reco-grid');
      $grid.replaceChildren.apply($grid, products.map(function (r) {
        return el('a', { href: '/singlepageview.php?pid=' + encodeURIComponent(r.pid), className: 'cart-reco-card' }, [
          el('img', { src: r.image || '', loading: 'lazy', alt: '' }),
          el('div', { className: 'body' }, [
            el('p', { className: 'name', text: r.name || '' }),
            el('div', { className: 'price-row' }, [
              el('span', { className: 'price', text: '₹' + inr(r.price) }),
              el('span', { className: 'mrp', text: '₹' + inr(r.mrp || 0) }),
            ]),
          ]),
        ]);
      }));
    })
    .catch(function () {});

  // ── Place Order — no payment, inline confirmation modal ───────
  document.getElementById('place-order-btn').addEventListener('click', function () {
    // var orderId = 'ORD' + Date.now() + Math.floor(Math.random() * 9999);
    // try {
    //   var orders = JSON.parse(localStorage.getItem('orders') || '[]');
    //   orders.push({ id: orderId, items: cart, address: user, total: sellingTotal, ts: Date.now() });
    //   localStorage.setItem('orders', JSON.stringify(orders));
    //   localStorage.setItem('cart', '[]');
    // } catch (e) {}
   console.log("============== PLACE ORDER CLICKED ==============");

    /* =========================
       DEBUG START
    ========================= */

    console.log("Cart:", cart);

    console.log("User:", user);

    console.log("Selling Total:", sellingTotal);

    /* =========================
       VALIDATION
    ========================= */

    if (!sellingTotal || sellingTotal <= 0) {

        console.error("INVALID SELLING TOTAL");
        alert("Invalid order amount");
        return;
    }

    if (!user.name) {
        console.error("USER NAME MISSING");
        alert("User data missing");
        return;
    }

    /* =========================
       ORDER ID
    ========================= */

    var orderId = 'ORD' + Date.now() + Math.floor(Math.random() * 9999);
    console.log("Generated Order ID:", orderId);

    /* =========================
       SAVE ORDER
    ========================= */

    try {

        var orders = JSON.parse(localStorage.getItem('orders') || '[]');
        orders.push({
            id: orderId,
            items: cart,
            address: user,
            total: sellingTotal,
            ts: Date.now()
        });

        localStorage.setItem('orders', JSON.stringify(orders));
        console.log("Order Saved To LocalStorage");

    } catch (e) {

        console.error("LOCAL STORAGE ERROR:", e);
    }

    /* =========================
       CREATE FORM
    ========================= */

    const form = document.createElement("form");

    form.method = "POST";

    form.action = "/place-order.php";

    /* IMPORTANT */
    form.style.display = "none";

    console.log("Form Action:", form.action);

    /* =========================
       FORM DATA
    ========================= */

    const fields = {

        final_amount: String(sellingTotal),

        customer_name: String(user.name || ''),

        customer_email: String(user.email || ''),

        customer_contact: String(user.phone || ''),

        order_id: String(orderId)

    };

    console.log("FIELDS:", fields);

    /* =========================
       APPEND INPUTS
    ========================= */

    Object.keys(fields).forEach(function(key) {

        const input = document.createElement("input");

        input.type = "hidden";

        input.name = key;

        input.value = fields[key];

        form.appendChild(input);

        console.log("Added Input:", key, "=", fields[key]);

    });

    /* =========================
       ADD FORM TO BODY
    ========================= */

    document.body.appendChild(form);

    console.log("FORM HTML:");
    console.log(form.outerHTML);

    console.log("Submitting Form...");

    /* =========================
       SUBMIT FORM
    ========================= */

    form.submit();

    /* =========================
       CLEAR CART AFTER SUBMIT
    ========================= */

    setTimeout(function () {
        localStorage.setItem('cart', '[]');
        console.log("Cart Cleared");

    }, 3000);


  });
})();
