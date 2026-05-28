<?php
require_once __DIR__ . '/../../includes/helpers.php';
$page_title = 'Add Delivery Address';
include __DIR__ . '/../../includes/layout-head.php';

$states = [
  ['AP','Andhra Pradesh'],['AR','Arunachal Pradesh'],['AS','Assam'],['BR','Bihar'],
  ['CT','Chhattisgarh'],['GA','Goa'],['GJ','Gujarat'],['HR','Haryana'],
  ['HP','Himachal Pradesh'],['JK','Jammu & Kashmir'],['JH','Jharkhand'],['KA','Karnataka'],
  ['KL','Kerala'],['MP','Madhya Pradesh'],['MH','Maharashtra'],['MN','Manipur'],
  ['ML','Meghalaya'],['MZ','Mizoram'],['NL','Nagaland'],['OR','Odisha'],
  ['PB','Punjab'],['RJ','Rajasthan'],['SK','Sikkim'],['TN','Tamil Nadu'],
  ['TS','Telangana'],['TR','Tripura'],['UK','Uttarakhand'],['UP','Uttar Pradesh'],
  ['WB','West Bengal'],['AN','Andaman & Nicobar'],['CH','Chandigarh'],
  ['DN','Dadra and Nagar Haveli'],['DD','Daman & Diu'],['DL','Delhi'],
  ['LD','Lakshadweep'],['PY','Puducherry'],
];
?>
  <div>
    <div class="container-fluid p-3 header-container">
      <div class="row header py-2">
        <div class="col-1">
          <div class="menu-icon" onclick="window.location.href='/cart'">
            <svg width="19" height="16" viewBox="0 0 19 16" xmlns="http://www.w3.org/2000/svg">
              <path d="M17.556 7.847H1M7.45 1L1 7.877l6.45 6.817" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none" />
            </svg>
          </div>
        </div>
        <div class="col-8"><div class="menu-logo"><h4 class="mb-0 mt-1 ms-2">Add Delivery Address</h4></div></div>
      </div>
    </div>

    <div class="_1fhgRH mb-70" style="width:100%">
      <div class="card py-1 max-height" style="width:100%;max-width:100%;border:none;border-radius:0">
        <div class="checkout-stepper">
          <div class="step active"><div class="circle">1</div><div class="lbl">Address</div></div>
          <div class="bar"></div>
          <div class="step"><div class="circle">2</div><div class="lbl">Order Summary</div></div>
          <div class="bar"></div>
          <div class="step"><div class="circle">3</div><div class="lbl">Payment</div></div>
        </div>

        <form id="addressForm">
          <div class="card-body">
            <div class="form-floating mb-3">
              <input class="form-control" type="text" id="fname" name="fname" placeholder="Full name" required />
              <label for="fname">Full Name (Required)*</label>
            </div>
            <div class="form-floating mb-3">
              <input class="form-control" type="text" id="mobile" name="mobile" placeholder="Mobile number" pattern="[0-9]{10}" required />
              <label for="mobile">Mobile number (Required)*</label>
            </div>
            <div class="form-floating mb-3">
              <input class="form-control" type="number" id="pincode" name="pincode" placeholder="PIN code" required />
              <label for="pincode">Pincode (Required)*</label>
            </div>
            <div class="row">
              <div class="col-6 form-floating mb-3">
                <input class="form-control" type="text" id="city" name="city" placeholder="Town/City" required />
                <label for="city">City (Required)*</label>
              </div>
              <div class="col-6 form-floating mb-3">
                <select class="form-select" id="state" name="state" required>
                  <?php foreach ($states as [$k,$lbl]): ?>
                    <option value="<?= h($k) ?>"><?= h($lbl) ?></option>
                  <?php endforeach; ?>
                </select>
                <label for="state">State (Required)*</label>
              </div>
            </div>
            <div class="form-floating mb-3">
              <input class="form-control" type="text" id="house" name="house" placeholder="Flat, House.no, Building, Company" required />
              <label for="house">House No., Building Name (Required)*</label>
            </div>
            <div class="form-floating mb-3">
              <input class="form-control" type="text" id="colonny" name="colonny" placeholder="Area, Colony, Street, Sector, Village" required />
              <label for="colonny">Road name, Area, Colony (Required)*</label>
            </div>
          </div>
          <div class="card-footer bottom-btn" style="width:100%;padding:0 16px 24px;background:transparent;border:none">
            <button class="common-button" type="submit">Save and Deliver Here</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    (function () {
      var cart = JSON.parse(localStorage.getItem('cart') || '[]');
      if (cart.length === 0) { window.location.replace('/cart'); return; }

      var saved = JSON.parse(localStorage.getItem('user') || 'null');
      if (saved) {
        document.getElementById('fname').value   = saved.name    || saved.fname  || '';
        document.getElementById('mobile').value  = saved.phone   || saved.mobile || '';
        document.getElementById('pincode').value = saved.pincode || '';
        document.getElementById('city').value    = saved.city    || '';
        document.getElementById('state').value   = saved.state   || 'AP';
        document.getElementById('house').value   = saved.house   || saved.address || '';
        document.getElementById('colonny').value = saved.colonny || '';
      }

      document.getElementById('addressForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var v = {
          name:    document.getElementById('fname').value.trim(),
          phone:   document.getElementById('mobile').value.trim(),
          pincode: document.getElementById('pincode').value.trim(),
          city:    document.getElementById('city').value.trim(),
          state:   document.getElementById('state').value,
          house:   document.getElementById('house').value.trim(),
          colonny: document.getElementById('colonny').value.trim()
        };
        v.address = [v.house, v.colonny].filter(Boolean).join(', ');
        localStorage.setItem('user', JSON.stringify(v));
        window.location.href = '/order-summary';
      });
    })();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
