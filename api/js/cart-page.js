// Renders the cart from localStorage. Uses textContent / createElement for
// all user-controlled fields (product name comes from scraped data and may
// contain HTML-meaningful characters) to avoid any XSS surface.

(function () {
  var items = window.BV.readCart();
  var $items = document.getElementById('cart-items');
  var $empty = document.getElementById('empty-state');
  var $filled = document.getElementById('cart-filled');
  var $footer = document.getElementById('cart-footer');
  var $tab = document.getElementById('cart-tab-count');

  function inr(n) { return Number(n || 0).toLocaleString('en-IN'); }

  function el(tag, props, children) {
    var node = document.createElement(tag);
    if (props) {
      for (var k in props) {
        if (k === 'className') node.className = props[k];
        else if (k === 'text') node.textContent = props[k];
        else if (k === 'html') node.appendChild(document.createTextNode(props[k]));
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

  function renderItem(i, idx) {
    var qtySelect = el('select', { className: 'qty-select', 'data-idx': String(idx) },
      [1,2,3,4,5].map(function (n) {
        var opt = el('option', { value: String(n), text: String(n) });
        if (n === i.qty) opt.selected = true;
        return opt;
      })
    );

    var row = el('div', { className: 'cart-item-card-inner' }, [
      el('div', { className: 'cart-item-row' }, [
        el('img', { src: i.image || '', alt: '', className: 'cart-item-image' }),
        el('div', { style: 'flex:1' }, [
          el('p', { className: 'product-name', style: 'font-size:14px;line-height:1.4;margin-bottom:8px', text: i.name || '' }),
          el('div', { className: 'price-line' }, [
            el('span', { className: 'selling-price', text: '₹' + inr(i.price) }),
            el('del', { className: 'mrp', text: '₹' + inr(i.mrp || 0) }),
          ]),
          el('div', { className: 'd-flex align-items-center mt-2', style: 'gap:12px' }, [
            el('span', { className: 'text-muted small', text: 'Qty:' }),
            qtySelect,
          ]),
        ]),
      ]),
      el('div', { className: 'action-buttons' }, [
        el('button', { 'data-act': 'remove', 'data-idx': String(idx), text: 'REMOVE' }),
        el('button', { 'data-act': 'view', 'data-pid': String(i.pid), text: 'VIEW' }),
      ]),
    ]);
    return row;
  }

  function renderPriceDetails(items) {
    var subtotal = items.reduce(function (s, i) { return s + i.price * i.qty; }, 0);
    var mrpTotal = items.reduce(function (s, i) { return s + (i.mrp || i.price) * i.qty; }, 0);
    var savings = mrpTotal - subtotal;
    return [
      el('h6', { className: 'fw-bold mb-3', text: 'Price Details' }),
      el('div', { className: 'price-details-row' }, [
        el('span', { text: 'Price (' + items.length + ' items)' }),
        el('span', { text: '₹' + inr(mrpTotal) }),
      ]),
      el('div', { className: 'price-details-row' }, [
        el('span', { text: 'Discount' }),
        el('span', { className: 'text-success', text: '- ₹' + inr(savings) }),
      ]),
      el('div', { className: 'price-details-row' }, [
        el('span', { text: 'Delivery Charges' }),
        el('span', { className: 'text-success', text: 'FREE' }),
      ]),
      el('div', { className: 'price-details-row total-amount-row' }, [
        el('span', { text: 'Total Amount' }),
        el('span', { text: '₹' + inr(subtotal) }),
      ]),
    ];
  }

  function render() {
    $tab.textContent = items.length;
    if (items.length === 0) {
      $empty.style.display = '';
      $filled.style.display = 'none';
      $footer.style.display = 'none';
      return;
    }
    $empty.style.display = 'none';
    $filled.style.display = '';
    $footer.style.display = '';

    $items.replaceChildren.apply($items, items.map(renderItem));

    var subtotal = items.reduce(function (s, i) { return s + i.price * i.qty; }, 0);
    var mrpTotal = items.reduce(function (s, i) { return s + (i.mrp || i.price) * i.qty; }, 0);
    var savings = mrpTotal - subtotal;
    document.getElementById('savings-banner').textContent =
      'You will save ₹' + inr(savings) + ' on this order';
    var pd = document.getElementById('price-details');
    pd.replaceChildren.apply(pd, renderPriceDetails(items));
    document.getElementById('footer-total').textContent = '₹' + inr(subtotal);

    loadRecommendations();
  }

  function renderRecoCard(r) {
    return el('a', { href: '/singlepageview.php?pid=' + encodeURIComponent(r.pid), className: 'cart-reco-card' }, [
      el('img', { src: r.image || '', alt: '', loading: 'lazy' }),
      el('div', { className: 'body' }, [
        el('p', { className: 'name', text: r.name || '' }),
        el('div', { className: 'price-row' }, [
          el('span', { className: 'price', text: '₹' + inr(r.price) }),
          el('span', { className: 'mrp', text: '₹' + inr(r.mrp || 0) }),
        ]),
      ]),
    ]);
  }

  function loadRecommendations() {
    if (items.length === 0) return;
    var exclude = items.map(function (i) { return i.pid; }).join(',');
    var anchor = items[0].pid;
    fetch('/recommendations.php?anchor=' + encodeURIComponent(anchor) + '&exclude=' + encodeURIComponent(exclude) + '&limit=12')
      .then(function (r) { return r.json(); })
      .then(function (j) {
        var products = (j && j.products) || [];
        if (products.length === 0) return;
        document.getElementById('reco-section').style.display = '';
        document.getElementById('reco-title').textContent = 'More in ' + (j.category || 'Electronics');
        var grid = document.getElementById('reco-grid');
        grid.replaceChildren.apply(grid, products.map(renderRecoCard));
      })
      .catch(function () {});
  }

  document.addEventListener('click', function (ev) {
    var btn = ev.target.closest('[data-act]');
    if (!btn) return;
    var act = btn.dataset.act;
    if (act === 'remove') {
      items.splice(parseInt(btn.dataset.idx, 10), 1);
      window.BV.writeCart(items);
      render();
    } else if (act === 'view') {
      location.href = '/singlepageview.php?pid=' + encodeURIComponent(btn.dataset.pid);
    }
  });
  document.addEventListener('change', function (ev) {
    if (ev.target.matches('.qty-select')) {
      var idx = parseInt(ev.target.dataset.idx, 10);
      items[idx].qty = parseInt(ev.target.value, 10);
      window.BV.writeCart(items);
      render();
    }
  });

  render();
})();
