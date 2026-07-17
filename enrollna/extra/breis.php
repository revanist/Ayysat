<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AYYSAT Application & Registration Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #0b2545;
      --accent: #ffc72c;
      --light: #f8f9fa;
      --border: #dee2e6;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: var(--light); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

    .navbar-ayysat {
      background-color: var(--primary);
      color: var(--accent);
      padding: 20px;
      text-align: center;
    }

    .navbar-ayysat h1 { margin: 0; font-size: 32px; font-weight: bold; }
    .navbar-ayysat .tagline { font-size: 14px; color: #fff; margin-top: 5px; }

    .form-panel {
      max-width: 900px;
      margin: 40px auto;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      overflow: hidden;
    }

    .wizard-steps {
      display: flex;
      list-style: none;
      background-color: var(--primary);
      padding: 20px;
      color: white;
      margin: 0;
    }

    .wizard-steps li {
      flex: 1;
      text-align: center;
      position: relative;
      font-size: 12px;
      font-weight: 500;
      color: #ccc;
    }

    .wizard-steps li.active {
      color: var(--accent);
      font-weight: bold;
    }

    .wizard-steps li.done {
      color: #4caf50;
    }

    .wizard-steps li:not(:last-child)::after {
      content: '';
      position: absolute;
      right: -50%;
      top: 50%;
      width: 100%;
      height: 2px;
      background: #555;
      transform: translateY(-50%);
    }

    .wizard-pane {
      display: none;
      padding: 40px;
      animation: fadeIn 0.3s ease-in;
    }

    .wizard-pane.active { display: block; }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .form-group { margin-bottom: 20px; }
    .form-group label { font-weight: 600; margin-bottom: 8px; color: var(--primary); }
    .form-control { border: 1px solid var(--border); padding: 10px 12px; border-radius: 4px; }
    .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 0.2rem rgba(255,199,44,0.25); }
    .form-control.is-invalid { border-color: #dc3545; }
    .form-control.is-valid { border-color: #28a745; }

    .pay-method {
      border: 2px solid var(--border);
      border-radius: 6px;
      padding: 15px;
      margin-bottom: 12px;
      cursor: pointer;
      transition: all 0.3s;
    }

    .pay-method:hover { border-color: var(--accent); }
    .pay-method.selected { border-color: var(--accent); background-color: #fffbef; }
    .pay-method input[type="radio"] { margin-right: 10px; }

    .pay-detail-box {
      border: 1px solid var(--border);
      border-radius: 6px;
      padding: 15px;
      margin-top: 10px;
      background: #f5f5f5;
    }

    .pay-detail-box.d-none { display: none !important; }

    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 10px 0;
      border-bottom: 1px solid var(--border);
    }

    .summary-row:last-child { border-bottom: none; }
    .summary-row span:first-child { font-weight: 600; color: var(--primary); }

    .btn-group-wizard {
      display: flex;
      gap: 10px;
      justify-content: space-between;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid var(--border);
    }

    .btn {
      padding: 10px 20px;
      border-radius: 4px;
      font-weight: 600;
      cursor: pointer;
      border: none;
      transition: all 0.3s;
    }

    .btn-primary {
      background-color: var(--primary);
      color: white;
    }

    .btn-primary:hover { background-color: #051a35; color: white; }
    .btn-primary:disabled { background-color: #ccc; cursor: not-allowed; }

    .btn-warning { background-color: var(--accent); color: var(--primary); }
    .btn-warning:hover { background-color: #ffb900; }

    .btn-success { background-color: #28a745; color: white; }
    .btn-secondary { background-color: #6c757d; color: white; }

    #pageLoader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(11, 37, 69, 0.9);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      opacity: 1;
      transition: opacity 0.3s;
    }

    #pageLoader.loaded {
      opacity: 0;
      pointer-events: none;
    }

    .spinner-border {
      border: 4px solid rgba(255,255,255,0.3);
      border-radius: 50%;
      border-top: 4px solid white;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      border-radius: 4px;
      padding: 12px 16px;
      margin-bottom: 20px;
    }

    .success-content {
      text-align: center;
      padding: 40px 20px;
    }

    .success-content h2 { color: #28a745; margin-bottom: 10px; }
    .success-content p { font-size: 16px; margin-bottom: 5px; }

    #paymentStatus {
      background-color: #e7f3ff;
      border: 1px solid #b3d9ff;
      border-radius: 4px;
      padding: 15px;
      margin-top: 15px;
      color: #004085;
    }

    .spinner-border-sm {
      width: 16px;
      height: 16px;
      border-width: 2px;
    }
  </style>
</head>
<body>
  <div id="pageLoader">
    <div style="text-align: center;">
      <div class="spinner-border" style="width: 60px; height: 60px; border-width: 5px; color: white;"></div>
      <p style="color: white; margin-top: 20px; font-size: 18px;">Loading Portal...</p>
    </div>
  </div>

  <div class="navbar-ayysat">
    <h1>AYYSAT</h1>
    <div class="tagline">Application & Registration Portal</div>
  </div>

  <div class="form-panel">
    <ul class="wizard-steps">
      <li class="active">1. Personal Info</li>
      <li>2. Program Selection</li>
      <li>3. Schedule</li>
      <li>4. Payment</li>
      <li>5. Review & Submit</li>
    </ul>

    <!-- STEP 1: Personal Information -->
    <div class="wizard-pane active" data-step="1">
      <h3 style="color: var(--primary); margin-bottom: 25px;">Step 1: Personal Information</h3>
      <div class="row">
        <div class="col-md-6 form-group">
          <label>First Name *</label>
          <input type="text" class="form-control" name="firstName" required>
        </div>
        <div class="col-md-6 form-group">
          <label>Last Name *</label>
          <input type="text" class="form-control" name="lastName" required>
        </div>
      </div>
      <div class="form-group">
        <label>Email Address *</label>
        <input type="email" class="form-control" name="email" required>
      </div>
      <div class="form-group">
        <label>Phone Number *</label>
        <input type="tel" class="form-control" name="phone" required>
      </div>
      <div class="form-group">
        <label>Address *</label>
        <textarea class="form-control" name="address" rows="3" required></textarea>
      </div>
      <div class="row">
        <div class="col-md-6 form-group">
          <label>Date of Birth *</label>
          <input type="date" class="form-control" name="dob" required>
        </div>
        <div class="col-md-6 form-group">
          <label>Gender *</label>
          <select class="form-control" name="gender" required>
            <option value="" selected disabled>Select...</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
          </select>
        </div>
      </div>
      <div class="btn-group-wizard">
        <div></div>
        <button class="btn btn-primary btn-next">Next →</button>
      </div>
    </div>

    <!-- STEP 2: Program Selection -->
    <div class="wizard-pane" data-step="2">
      <h3 style="color: var(--primary); margin-bottom: 25px;">Step 2: Program Selection</h3>
      <div class="form-group">
        <label>Education Level *</label>
        <select class="form-control" id="level" name="level" required>
          <option value="" selected disabled>Select an education level</option>
          <option value="shs">Senior High School</option>
          <option value="college">College</option>
          <option value="tesda">TESDA Program</option>
        </select>
      </div>
      <div class="form-group">
        <label>Preferred Program *</label>
        <select class="form-control" id="program" name="program" required disabled>
          <option value="" selected disabled>Select a program</option>
        </select>
      </div>
      <div class="form-group">
        <label>Preferred Schedule *</label>
        <select class="form-control" name="schedule" required>
          <option value="" selected disabled>Select a schedule</option>
          <option value="morning">Morning (6:00 AM - 12:00 PM)</option>
          <option value="afternoon">Afternoon (12:00 PM - 5:00 PM)</option>
          <option value="evening">Evening (5:00 PM - 9:00 PM)</option>
          <option value="weekend">Weekend</option>
        </select>
      </div>
      <div class="btn-group-wizard">
        <button class="btn btn-secondary btn-prev">← Previous</button>
        <button class="btn btn-primary btn-next">Next →</button>
      </div>
    </div>

    <!-- STEP 3: Guardian & Emergency Contact -->
    <div class="wizard-pane" data-step="3">
      <h3 style="color: var(--primary); margin-bottom: 25px;">Step 3: Emergency Contacts</h3>
      <div class="form-group">
        <label>Guardian Name *</label>
        <input type="text" class="form-control" name="guardianName" required>
      </div>
      <div class="form-group">
        <label>Guardian Contact Number *</label>
        <input type="tel" class="form-control" name="guardianPhone" required>
      </div>
      <div class="form-group">
        <label>Emergency Contact Name *</label>
        <input type="text" class="form-control" name="emergencyName" required>
      </div>
      <div class="form-group">
        <label>Emergency Contact Number *</label>
        <input type="tel" class="form-control" name="emergencyPhone" required>
      </div>
      <div class="btn-group-wizard">
        <button class="btn btn-secondary btn-prev">← Previous</button>
        <button class="btn btn-primary btn-next">Next →</button>
      </div>
    </div>

    <!-- STEP 4: Payment -->
    <div class="wizard-pane" data-step="4">
      <h3 style="color: var(--primary); margin-bottom: 25px;">Step 4: Payment (₱750.00)</h3>
      <p style="color: #666; margin-bottom: 20px;">Select your preferred payment method:</p>

      <div class="pay-method" data-method="gcash">
        <input type="radio" name="paymentMethod" value="gcash">
        <strong>GCash</strong>
        <p style="font-size: 12px; color: #666; margin: 5px 0 0 26px;">Fast and secure mobile payment</p>
      </div>

      <div class="pay-method" data-method="paymaya">
        <input type="radio" name="paymentMethod" value="paymaya">
        <strong>PayMaya</strong>
        <p style="font-size: 12px; color: #666; margin: 5px 0 0 26px;">Digital wallet payment</p>
      </div>

      <div class="pay-method" data-method="card">
        <input type="radio" name="paymentMethod" value="card">
        <strong>Credit / Debit Card</strong>
        <p style="font-size: 12px; color: #666; margin: 5px 0 0 26px;">Visa, MasterCard, JCB</p>
      </div>

      <div class="pay-method" data-method="bank">
        <input type="radio" name="paymentMethod" value="bank">
        <strong>Bank Transfer</strong>
        <p style="font-size: 12px; color: #666; margin: 5px 0 0 26px;">Direct bank deposit</p>
      </div>

      <!-- Payment Detail Boxes -->
      <div class="pay-detail-box d-none" data-method="card">
        <div class="form-group">
          <label>Card Number *</label>
          <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456" required>
        </div>
        <div class="row">
          <div class="col-md-6 form-group">
            <label>Expiry Date *</label>
            <input type="text" class="form-control" id="cardExpiry" placeholder="MM / YY" required>
          </div>
          <div class="col-md-6 form-group">
            <label>CVV *</label>
            <input type="text" class="form-control" placeholder="123" maxlength="3" required>
          </div>
        </div>
      </div>

      <div id="paymentStatus" class="d-none alert-success">
        <strong>✓ Payment Confirmed!</strong><br>
        Reference No: <strong class="ref-code"></strong>
      </div>

      <div class="btn-group-wizard">
        <button class="btn btn-secondary btn-prev">← Previous</button>
        <button class="btn btn-warning" id="confirmPaymentBtn" disabled>Confirm Payment</button>
        <button class="btn btn-primary btn-next" data-step="4" disabled>Next →</button>
      </div>
    </div>

    <!-- STEP 5: Review & Submit -->
    <div id="reviewPane" class="wizard-pane" data-step="5">
      <h3 style="color: var(--primary); margin-bottom: 25px;">Step 5: Review Your Application</h3>
      <p style="color: #666; margin-bottom: 20px;">Please review your information before submitting:</p>
      <div id="summaryBox" style="background: #f5f5f5; padding: 20px; border-radius: 6px; margin-bottom: 20px;">
        <!-- Summary will be generated here -->
      </div>
      <div class="btn-group-wizard">
        <button class="btn btn-secondary btn-prev">← Previous</button>
        <div style="display: flex; gap: 10px;">
          <button class="btn btn-success" id="downloadFilledPdfBtn">📄 Download Form</button>
          <button class="btn btn-primary" id="submitApplicationBtn">✓ Submit Application</button>
        </div>
      </div>
    </div>

    <!-- Success Message -->
    <div id="successPane" class="d-none">
      <div class="success-content">
        <h2>✓ Application Submitted Successfully!</h2>
        <p>Thank you for applying to AYYSAT!</p>
        <p style="font-size: 18px; margin-top: 20px;"><strong>Reference No: <span id="finalRefCode"></span></strong></p>
        <p style="color: #666; margin-top: 15px; font-size: 14px;">Please save this reference number for your records.</p>
        <button class="btn btn-primary" onclick="location.href='#'" style="margin-top: 20px;">← Back to Home</button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <script>
    /* ===================================================
       AYYSAT Application & Registration Portal
       Multi-step wizard + simulated payment + PDF export
    =================================================== */
    document.addEventListener('DOMContentLoaded', function () {

  const state = {
    step: 1,
    totalSteps: 5,
    data: {},
    paymentMethod: null,
    paid: false,
    referenceNo: null
  };

  const panes = document.querySelectorAll('.wizard-pane');
  const stepItems = document.querySelectorAll('.wizard-steps li');
  const feeAmount = 750; // PHP reservation / application fee

  /* ---------- Loader ---------- */
  const loader = document.getElementById('pageLoader');
  window.addEventListener('load', function () {
    setTimeout(function () { if (loader) loader.classList.add('loaded'); }, 300);
  });

  /* ---------- Navigation helpers ---------- */
  function showStep(n) {
    panes.forEach(function (p) {
      p.classList.toggle('active', parseInt(p.dataset.step, 10) === n);
    });
    stepItems.forEach(function (li, idx) {
      li.classList.remove('active', 'done');
      if (idx + 1 < n) li.classList.add('done');
      if (idx + 1 === n) li.classList.add('active');
    });
    state.step = n;
    window.scrollTo({ top: document.querySelector('.form-panel').offsetTop - 100, behavior: 'smooth' });

    if (n === 5) buildSummary();
  }

  function validatePane(pane) {
    const fields = pane.querySelectorAll('input[required], select[required], textarea[required]');
    let valid = true;
    fields.forEach(function (f) {
      if (!f.checkValidity()) {
        valid = false;
        f.classList.add('is-invalid');
      } else {
        f.classList.remove('is-invalid');
        f.classList.add('is-valid');
      }
    });
    return valid;
  }

  function collectPane(pane) {
    pane.querySelectorAll('[name]').forEach(function (f) {
      if (f.type === 'radio') {
        if (f.checked) state.data[f.name] = f.value;
      } else {
        state.data[f.name] = f.value;
      }
    });
  }

  document.querySelectorAll('.btn-next').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const currentPane = document.querySelector('.wizard-pane.active');
      if (!validatePane(currentPane)) return;
      collectPane(currentPane);
      if (state.step < state.totalSteps) showStep(state.step + 1);
    });
  });

  document.querySelectorAll('.btn-prev').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (state.step > 1) showStep(state.step - 1);
    });
  });

  /* ---------- Step 2: dynamic program list ---------- */
  const levelSelect = document.getElementById('level');
  const programSelect = document.getElementById('program');

  const programsByLevel = {
    shs: ['STEM', 'ABM', 'HUMSS', 'ICT'],
    college: ['BS Information Technology', 'BS Business Administration', 'BS Education', 'BS Engineering'],
    tesda: ['Computer Systems Servicing NC II', 'Bookkeeping NC III', 'Food & Beverage Services NC II']
  };

  if (levelSelect) {
    levelSelect.addEventListener('change', function () {
      const list = programsByLevel[this.value] || [];
      programSelect.innerHTML = '<option value="" selected disabled>Select a program</option>' +
        list.map(function (p) { return '<option value="' + p + '">' + p + '</option>'; }).join('');
      programSelect.disabled = list.length === 0;
    });
  }

  /* ---------- Step 4: payment method selection ---------- */
  const payMethods = document.querySelectorAll('.pay-method');
  const payDetailBoxes = document.querySelectorAll('.pay-detail-box');
  const payConfirmBtn = document.getElementById('confirmPaymentBtn');
  const paymentStatus = document.getElementById('paymentStatus');

  payMethods.forEach(function (card) {
    card.addEventListener('click', function () {
      const method = this.dataset.method;
      payMethods.forEach(function (c) { c.classList.remove('selected'); });
      this.classList.add('selected');
      this.querySelector('input[type="radio"]').checked = true;
      state.paymentMethod = method;

      payDetailBoxes.forEach(function (box) {
        box.classList.toggle('d-none', box.dataset.method !== method);
      });

      if (payConfirmBtn) payConfirmBtn.disabled = false;
    });
  });

  if (payConfirmBtn) {
    payConfirmBtn.addEventListener('click', function () {
      if (!state.paymentMethod) return;

      // Card details validation (only when card method chosen)
      if (state.paymentMethod === 'card') {
        const cardPane = document.querySelector('[data-method="card"].pay-detail-box');
        const cardFields = cardPane.querySelectorAll('input[required]');
        let valid = true;
        cardFields.forEach(function (f) {
          if (!f.checkValidity()) { valid = false; f.classList.add('is-invalid'); }
          else { f.classList.remove('is-invalid'); }
        });
        if (!valid) return;
      }

      payConfirmBtn.disabled = true;
      payConfirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing…';

      // Simulated payment processing delay
      setTimeout(function () {
        state.paid = true;
        state.referenceNo = 'AYY-' + Date.now().toString().slice(-8);
        state.data.paymentMethod = state.paymentMethod;
        state.data.referenceNo = state.referenceNo;
        state.data.amountPaid = feeAmount;

        if (paymentStatus) {
          paymentStatus.classList.remove('d-none');
          paymentStatus.querySelector('.ref-code').textContent = state.referenceNo;
        }

        payConfirmBtn.innerHTML = '✓ Payment Confirmed';
        document.querySelectorAll('.btn-next[data-step="4"]').forEach(function (b) { b.disabled = false; });
      }, 1400);
    });
  }

  /* ---------- Step 5: build review summary ---------- */
  function buildSummary() {
    const box = document.getElementById('summaryBox');
    if (!box) return;

    const d = state.data;
    const rows = [
      ['Full Name', [d.firstName, d.lastName].filter(Boolean).join(' ')],
      ['Email', d.email],
      ['Phone', d.phone],
      ['Address', d.address],
      ['Education Level', labelForLevel(d.level)],
      ['Program', d.program],
      ['Preferred Schedule', d.schedule],
      ['Guardian Name', d.guardianName],
      ['Guardian Contact', d.guardianPhone],
      ['Emergency Contact', d.emergencyName + (d.emergencyPhone ? ' (' + d.emergencyPhone + ')' : '')],
      ['Payment Method', labelForPayment(d.paymentMethod)],
      ['Amount Paid', d.amountPaid ? '₱' + d.amountPaid + '.00' : '—'],
      ['Reference No.', d.referenceNo || '—']
    ];

    box.innerHTML = rows.map(function (r) {
      return '<div class="summary-row"><span>' + r[0] + '</span><span>' + (r[1] || '—') + '</span></div>';
    }).join('');
  }

  function labelForLevel(v) {
    return { shs: 'Senior High School', college: 'College', tesda: 'TESDA Program' }[v] || v || '—';
  }

  function labelForPayment(v) {
    return { gcash: 'GCash', paymaya: 'Maya', card: 'Credit / Debit Card', bank: 'Bank Transfer' }[v] || '—';
  }

  /* ---------- Final submit ---------- */
  const submitBtn = document.getElementById('submitApplicationBtn');
  const successPane = document.getElementById('successPane');
  const reviewPane = document.getElementById('reviewPane');

  if (submitBtn) {
    submitBtn.addEventListener('click', function () {
      if (!state.paid) {
        alert('Please complete the reservation fee payment in Step 4 before submitting.');
        showStep(4);
        return;
      }
      if (successPane && reviewPane) {
        reviewPane.classList.add('d-none');
        successPane.classList.remove('d-none');
        document.getElementById('finalRefCode').textContent = state.referenceNo;
      }
    });
  }

  /* ---------- PDF generation: filled application ---------- */
  function drawHeader(doc, title) {
    doc.setFillColor(11, 37, 69);
    doc.rect(0, 0, 210, 28, 'F');
    doc.setTextColor(255, 199, 44);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(18);
    doc.text('AYYSAT', 14, 18);
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text('Admission Portal', 14, 24);
    doc.setTextColor(20, 20, 20);
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text(title, 14, 40);
    doc.setDrawColor(255, 199, 44);
    doc.setLineWidth(1);
    doc.line(14, 44, 196, 44);
  }

  function downloadFilledPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const d = state.data;

    drawHeader(doc, 'Student Application Form');

    let y = 56;
    const line = function (label, value) {
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(10);
      doc.text(label + ':', 14, y);
      doc.setFont('helvetica', 'normal');
      doc.text(String(value || '—'), 70, y);
      y += 9;
    };

    line('Full Name', [d.firstName, d.lastName].filter(Boolean).join(' '));
    line('Email', d.email);
    line('Phone', d.phone);
    line('Address', d.address);
    line('Date of Birth', d.dob);
    line('Gender', d.gender);
    y += 2;
    line('Education Level', labelForLevel(d.level));
    line('Program', d.program);
    line('Preferred Schedule', d.schedule);
    y += 2;
    line('Guardian Name', d.guardianName);
    line('Guardian Contact', d.guardianPhone);
    line('Emergency Contact', d.emergencyName);
    line('Emergency Phone', d.emergencyPhone);
    y += 2;
    line('Payment Method', labelForPayment(d.paymentMethod));
    line('Amount Paid', d.amountPaid ? 'PHP ' + d.amountPaid + '.00' : '—');
    line('Reference No.', d.referenceNo);
    line('Date Submitted', new Date().toLocaleString());

    doc.setFontSize(9);
    doc.setTextColor(120, 120, 120);
    doc.text('This document confirms your submitted application details. Please bring a printed copy on your assessment date.', 14, 280);

    doc.save('AYYSAT-Application-' + (state.referenceNo || 'form') + '.pdf');
  }

  /* ---------- PDF generation: blank downloadable form ---------- */
  function downloadBlankPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    drawHeader(doc, 'Student Application Form (Blank)');

    let y = 58;
    const field = function (label, width) {
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(10);
      doc.text(label, 14, y);
      doc.setDrawColor(180, 180, 180);
      doc.line(14, y + 12, 14 + (width || 182), y + 12);
      y += 20;
    };

    field('Full Name (Last, First, Middle)');
    field('Date of Birth');
    field('Gender');
    field('Address');
    field('Email Address');
    field('Phone Number');
    field('Education Level (Senior High / College / TESDA)');
    field('Preferred Program');
    field('Preferred Schedule');
    field('Guardian Name & Contact Number');
    field('Emergency Contact Name & Number');

    doc.setFontSize(9);
    doc.setTextColor(120, 120, 120);
    doc.text('Print this form, fill it out by hand, and submit it with your requirements to the Admissions Office.', 14, 285);

    doc.save('AYYSAT-Application-Blank-Form.pdf');
  }

  const downloadFilledBtn = document.getElementById('downloadFilledPdfBtn');
  const downloadBlankBtn = document.getElementById('downloadBlankPdfBtn');

  if (downloadFilledBtn) downloadFilledBtn.addEventListener('click', downloadFilledPDF);
  if (downloadBlankBtn) downloadBlankBtn.addEventListener('click', downloadBlankPDF);

  /* ---------- Card number formatting (cosmetic only) ---------- */
  const cardNumberInput = document.getElementById('cardNumber');
  if (cardNumberInput) {
    cardNumberInput.addEventListener('input', function () {
      let v = this.value.replace(/\D/g, '').slice(0, 16);
      this.value = v.replace(/(.{4})/g, '$1 ').trim();
    });
  }
  const cardExpiryInput = document.getElementById('cardExpiry');
  if (cardExpiryInput) {
    cardExpiryInput.addEventListener('input', function () {
      let v = this.value.replace(/\D/g, '').slice(0, 4);
      if (v.length > 2) v = v.slice(0, 2) + ' / ' + v.slice(2);
      this.value = v;
    });
  }

  showStep(1);
    });
  </script>
</body>
</html>