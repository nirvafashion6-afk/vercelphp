<?php
$bv_title = 'Add Delivery Address | DMLinan';
$bv_show_search = false;
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
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid p-3 header-container">
    <div class="row header py-2">
        <div class="col-1">
            <div class="menu-icon" onclick="location.href='/cart.php'" style="cursor:pointer">
                <svg width="19" height="16" viewBox="0 0 19 16" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.556 7.847H1M7.45 1L1 7.877l6.45 6.817" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
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
            <div class="step"><div class="circle">3</div><div class="lbl">Place Order</div></div>
        </div>

        <form id="address-form">
            <div class="card-body">
                <div class="form-floating mb-3">
                    <input class="form-control" type="text" id="fname" name="fname" placeholder="Full name" required>
                    <label for="fname">Full Name (Required)*</label>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" type="text" id="mobile" name="mobile" placeholder="Mobile number" pattern="[0-9]{10}" required>
                    <label for="mobile">Mobile number (Required)*</label>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" type="number" id="pincode" name="pincode" placeholder="PIN code" required>
                    <label for="pincode">Pincode (Required)*</label>
                </div>
                <div class="row">
                    <div class="col-6 form-floating mb-3">
                        <input class="form-control" type="text" id="city" name="city" placeholder="Town/City" required>
                        <label for="city">City (Required)*</label>
                    </div>
                    <div class="col-6 form-floating mb-3">
                        <select class="form-select" id="state" name="state" required>
                            <?php foreach ($states as $st): ?>
                                <option value="<?= bv_e($st[0]) ?>"><?= bv_e($st[1]) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="state">State (Required)*</label>
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" type="text" id="house" name="house" placeholder="Flat, House.no, Building, Company" required>
                    <label for="house">House No., Building Name (Required)*</label>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" type="text" id="colonny" name="colonny" placeholder="Area, Colony, Street, Sector, Village" required>
                    <label for="colonny">Road name, Area, Colony (Required)*</label>
                </div>
            </div>
            <div class="card-footer bottom-btn" style="width:100%;padding:0 16px 24px;background:transparent;border:none">
                <button class="common-button" type="submit">Save and Deliver Here</button>
            </div>
        </form>
    </div>
</div>

<script>
function bvCheckoutInit() {
    var cart = (window.BV && window.BV.readCart) ? window.BV.readCart() : JSON.parse(localStorage.getItem('cart') || '[]');
    if (!cart || cart.length === 0) { location.replace('/cart.php'); return; }

    var saved = {};
    try { saved = JSON.parse(localStorage.getItem('user') || '{}') || {}; } catch (e) {}
    if (saved) {
        ['fname','mobile','pincode','city','state','house','colonny'].forEach(function (k) {
            var v = saved[k] || (k === 'fname' ? saved.name : k === 'mobile' ? saved.phone : '') || '';
            var el = document.getElementById(k);
            if (el && v) el.value = v;
        });
    }

    var form = document.getElementById('address-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var data = {
            name:    document.getElementById('fname').value,
            phone:   document.getElementById('mobile').value,
            pincode: document.getElementById('pincode').value,
            city:    document.getElementById('city').value,
            state:   document.getElementById('state').value,
            house:   document.getElementById('house').value,
            colonny: document.getElementById('colonny').value,
        };
        data.fname = data.name;
        data.mobile = data.phone;
        data.address = [data.house, data.colonny].filter(Boolean).join(', ');
        localStorage.setItem('user', JSON.stringify(data));
        location.href = '/order_summary.php';
    });
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bvCheckoutInit);
} else {
    bvCheckoutInit();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
