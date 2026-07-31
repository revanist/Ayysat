<?php
require_once __DIR__ . '/functions/student_auth.php';
start_student_session();
require "db/dbconn.php";
require_once "functions/enrollment_setup.php";

ensure_enrollment_schema($conn);
seed_course_data($conn);


$courses = mysqli_query($conn, "SELECT id, course_code, course_name FROM courses ORDER BY course_code ASC");
$course_catalog = [];

while ($course = mysqli_fetch_assoc($courses)) {
  $course_id = (int) $course['id'];

  $sections_result = mysqli_query($conn, "SELECT id, section_name FROM sections WHERE course_id = $course_id ORDER BY section_name ASC");
  $sections = [];
  while ($section = mysqli_fetch_assoc($sections_result)) {
    $sections[] = $section;
  }

  $subjects_result = mysqli_query($conn, "SELECT id, subject_code, subject_name, schedule_day, schedule_time, room_number FROM subjects WHERE course_id = $course_id AND is_available = 1 ORDER BY subject_code ASC");
  $subjects = [];
  while ($subject = mysqli_fetch_assoc($subjects_result)) {
    $subjects[] = $subject;
  }

  $course_catalog[] = [
    'course' => $course,
    'sections' => $sections,
    'subjects' => $subjects,
  ];
}

$enrollmentError = $_SESSION['enrollment_error'] ?? '';
unset($_SESSION['enrollment_error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Enrollment & Registration Portal</title>
  <link href="css/bootstrap.css" rel="stylesheet">
  <link href="css/enrollment.css" rel="stylesheet">
</head>

<body>
  <div id="pageLoader">
    <div class="loader-content">
      <div class="spinner-border loader-spinner"></div>
      <p>Loading portal…</p>
    </div>
  </div>

  <div class="navbar-ayysat">
    <h1>EYYSAT</h1>
    <div class="tagline">Application & Registration Portal</div>
  </div>

  <div class="form-panel">
    <?php if ($enrollmentError !== ''): ?>
      <div id="enrollmentError" data-message="<?= htmlspecialchars($enrollmentError, ENT_QUOTES, 'UTF-8'); ?>" hidden></div>
    <?php endif; ?>
    <ul class="wizard-steps">
      <li class="active">1. Personal Info</li>
      <li>2. Program Selection</li>
      <li>3. Emergency Contact</li>
      <li>4. Review & Submit</li>
    </ul>

    <form id="enrollmentForm" action="functions/process_application.php" method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(student_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="semester" value="1">
      <input type="hidden" name="school_year" value="2026-2027">

      <!-- STEP 1: Personal Information -->
      <div class="wizard-pane active" data-step="1">
        <h3>Step 1: Personal Information</h3>
        <div class="row">
          <div class="col-md-4 form-group">
            <label>First Name *</label>
            <input type="text" class="form-control" name="first_name" required>
          </div>
          <div class="col-md-4 form-group">
            <label>Middle Initial(s)</label>
            <input type="text" class="form-control" name="middle_name" maxlength="5"
              pattern="[A-Za-z. ]{1,5}" title="Use initials only, for example A. or M. J.">
          </div>
          <div class="col-md-4 form-group">
            <label>Last Name *</label>
            <input type="text" class="form-control" name="last_name" required>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group">
            <label>Email Address *</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="col-md-6 form-group">
            <label>Contact Number *</label>
            <input type="tel" class="form-control" name="contact" maxlength="11" placeholder="09XXXXXXXXX" required>
          </div>
        </div>
        <div class="form-group">
          <label>Complete Address *</label>
          <textarea class="form-control" name="address" rows="3" required></textarea>
        </div>
        <div class="row">
          <div class="col-md-6 form-group">
            <label>Date of Birth *</label>
            <input type="date" class="form-control" name="birthdate" id="birthdate" required aria-describedby="birthdateError">
            <div id="birthdateError" class="invalid-feedback">You must be 18 years old to apply.</div>
          </div>
          <div class="col-md-6 form-group">
            <label>Sex *</label>
            <select class="form-select" name="sex" required>
              <option value="" selected disabled>Select Sex...</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
        </div>
        <div class="btn-group-wizard">
          <a href="webhome.php" class="btn btn-secondary"><- Back to Home</a>
              <button type="button" class="btn btn-primary btn-next">Next →</button>
        </div>
      </div>

      <!-- STEP 2: Program Selection -->
      <div class="wizard-pane" data-step="2">
        <h3>Step 2: Program Selection</h3>

        <div class="row">
          <div class="col-md-6 form-group">
            <label>Course *</label>
            <select class="form-select" name="course_id" required>
              <option value="" selected disabled>Select Course</option>
              <?php foreach ($course_catalog as $entry) : $course = $entry['course']; ?>
                <option value="<?= (int) $course['id']; ?>" data-name="<?= htmlspecialchars($course['course_code']); ?>">
                  <?= htmlspecialchars($course['course_code']); ?> - <?= htmlspecialchars($course['course_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6 form-group">
            <label>Year Level *</label>
            <select class="form-select" name="year_level" required>
              <option value="" selected disabled>Select Year</option>
              <option value="1">1st Year</option>
              <option value="2">2nd Year</option>
              <option value="3">3rd Year</option>
              <option value="4">4th Year</option>
            </select>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-6 form-group">
            <label>Section *</label>
            <select id="section_select" name="section_id" class="form-select" required>
              <option value="">Select Section</option>
            </select>
          </div>
          <div class="col-md-6 form-group">
            <label>Subjects *</label>
            <div id="subjectContainer" class="border rounded p-3 bg-light">
              <p class="text-muted small mb-0">Select a course to load subjects.</p>
            </div>
            <div class="invalid-feedback d-none" id="subjectError">Please select at least one subject.</div>
          </div>
        </div>

        <div class="btn-group-wizard">
          <button type="button" class="btn btn-secondary btn-prev">← Previous</button>
          <button type="button" class="btn btn-primary btn-next">Next →</button>
        </div>
      </div>

      <!-- STEP 3: Guardian & Emergency Contact -->
      <div class="wizard-pane" data-step="3">
        <h3>Step 3: Emergency Contacts</h3>
        <div class="row">
          <div class="col-md-6 form-group">
            <label>Guardian Name *</label>
            <input type="text" class="form-control" name="guardian" required>
          </div>
          <div class="col-md-6 form-group">
            <label>Guardian Contact Number *</label>
            <input type="tel" class="form-control" name="guardian_contact" maxlength="11" required>
          </div>
        </div>
        <div class="btn-group-wizard">
          <button type="button" class="btn btn-secondary btn-prev">← Previous</button>
          <button type="button" class="btn btn-primary btn-next">Next →</button>
        </div>
      </div>

      <!-- STEP 4: Payment -->
      <template>
        <h3>Step 4: Payment Preference</h3>
        <p class="step-intro">Select your preferred payment option. Payments are verified by staff after application review.</p>

        <div class="form-group mb-4">
          <label>Payment Option *</label>
          <select class="form-select" name="payment_option" id="paymentOption" required>
            <option value="" selected disabled>Select Payment Option</option>
            <option value="downpayment">Downpayment (₱ 2,500.00)</option>
            <option value="full">Full Payment (₱ 15,000.00)</option>
          </select>
        </div>

        <div id="paymentMethodsContainer" class="d-none">
          <div class="row">
            <div class="col-md-6">
              <div class="pay-method" data-method="gcash">
                <input type="radio" name="payment_method" value="gcash">
                <strong>GCash</strong>
                <p>Fast and secure mobile payment</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="pay-method" data-method="paymaya">
                <input type="radio" name="payment_method" value="paymaya">
                <strong>PayMaya</strong>
                <p>Digital wallet payment</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="pay-method" data-method="card">
                <input type="radio" name="payment_method" value="card">
                <strong>Credit / Debit Card</strong>
                <p>Visa, MasterCard, JCB</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="pay-method" data-method="bank">
                <input type="radio" name="payment_method" value="bank">
                <strong>Bank Transfer</strong>
                <p>Direct bank deposit</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="pay-method" data-method="cash">
                <input type="radio" name="payment_method" value="cash">
                <strong>Cash Payment</strong>
                <p>Pay at the cashier after your enrollment is approved</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Detail Boxes -->
        <div class="pay-detail-box d-none" data-method="card">
          <div class="form-group">
            <label>Card Number *</label>
            <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456">
          </div>
          <div class="row">
            <div class="col-md-6 form-group">
              <label>Expiry Date *</label>
              <input type="text" class="form-control" id="cardExpiry" placeholder="MM / YY">
            </div>
            <div class="col-md-6 form-group">
              <label>CVV *</label>
              <input type="text" class="form-control" placeholder="123" maxlength="3">
            </div>
          </div>
        </div>

        <div id="paymentStatus" class="d-none alert-success">
          <strong>✓ Payment Confirmed!</strong><br>
          Amount: <strong class="pay-amount"></strong><br>
          Reference No: <strong class="ref-code"></strong>
        </div>

        <div class="btn-group-wizard">
          <button type="button" class="btn btn-secondary btn-prev">← Previous</button>
          <button type="button" class="btn btn-warning" id="confirmPaymentBtn" disabled>Confirm Selection</button>
          <button type="button" class="btn btn-primary btn-next" data-step="4" disabled>Next →</button>
        </div>
      </template>

      <!-- STEP 4: Review & Submit -->
      <div id="reviewPane" class="wizard-pane" data-step="4">
        <h3>Step 4: Review Your Application</h3>
        <p class="step-intro">Please review your information before submitting:</p>
        <div id="summaryBox">
          <!-- Summary will be generated here -->
        </div>
        <div class="btn-group-wizard">
          <button type="button" class="btn btn-secondary btn-prev">← Previous</button>
          <div class="review-actions">
            <button type="button" class="btn btn-success" id="downloadFilledPdfBtn">📄 Download PDF Copy</button>
            <button type="submit" class="btn btn-primary" id="submitApplicationBtn">✓ Submit Enrollment</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <script src="js/bootstrap.bundle.js"></script>
  <script src="js/sweetalert2.all.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <script>
    const courseCatalog = <?= json_encode(array_map(function ($entry) {
                            return [
                              'id' => (int) $entry['course']['id'],
                              'course_code' => $entry['course']['course_code'],
                              'sections' => $entry['sections'],
                              'subjects' => $entry['subjects'],
                            ];
                          }, $course_catalog), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

    function escapeHtml(value) {
      return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    document.addEventListener('DOMContentLoaded', function() {
      const errorMessage = document.getElementById('enrollmentError')?.dataset.message;
      if (errorMessage) {
        Swal.fire({
          icon: 'error',
          title: 'Enrollment incomplete',
          text: errorMessage,
          confirmButtonColor: '#1e5a96'
        });
      }

      const state = {
        step: 1,
        totalSteps: 4,
        data: {},
        paymentMethod: null,
        paymentOptionAmount: 0,
        paid: false,
        referenceNo: null
      };

      const panes = document.querySelectorAll('.wizard-pane');
      const stepItems = document.querySelectorAll('.wizard-steps li');
      const birthdateInput = document.getElementById('birthdate');
      const birthdateError = document.getElementById('birthdateError');

      function validateBirthdate() {
        if (!birthdateInput?.value) return;

        const selectedDate = new Date(birthdateInput.value + 'T00:00:00');
        const latestAllowed = new Date();
        latestAllowed.setHours(0, 0, 0, 0);
        latestAllowed.setFullYear(latestAllowed.getFullYear() - 18);
        const isUnderage = selectedDate > latestAllowed;

        birthdateInput.setCustomValidity(isUnderage ? 'Applicants must be at least 18 years old.' : '');
        birthdateInput.classList.toggle('is-invalid', isUnderage);
        birthdateError?.classList.toggle('d-block', isUnderage);
      }

      birthdateInput?.addEventListener('change', validateBirthdate);

      /* ---------- Loader ---------- */
      const loader = document.getElementById('pageLoader');
      setTimeout(function() {
        if (loader) loader.classList.add('loaded');
      }, 300);

      /* ---------- Navigation helpers ---------- */
      function showStep(n) {
        panes.forEach(function(p) {
          p.classList.toggle('active', parseInt(p.dataset.step, 10) === n);
        });
        stepItems.forEach(function(li, idx) {
          li.classList.remove('active', 'done');
          if (idx + 1 < n) li.classList.add('done');
          if (idx + 1 === n) li.classList.add('active');
        });
        state.step = n;
        window.scrollTo({
          top: document.querySelector('.form-panel').offsetTop - 100,
          behavior: 'smooth'
        });

        if (n === 4) buildSummary();
      }

      function validatePane(pane) {
        const fields = pane.querySelectorAll('input[required], select[required], textarea[required]');
        let valid = true;
        fields.forEach(function(f) {
          if (!f.checkValidity()) {
            valid = false;
            f.classList.add('is-invalid');
          } else {
            f.classList.remove('is-invalid');
            f.classList.add('is-valid');
          }
        });

        // Subject Checkbox Validation
        if (pane.dataset.step === "2") {
          const checkedSubjects = pane.querySelectorAll('input[name="subject_ids[]"]:checked');
          const subjectError = document.getElementById('subjectError');
          if (checkedSubjects.length === 0) {
            valid = false;
            if (subjectError) {
              subjectError.classList.remove('d-none');
              subjectError.style.display = 'block';
            }
          } else {
            if (subjectError) {
              subjectError.classList.add('d-none');
              subjectError.style.display = 'none';
            }
          }
        }

        return valid;
      }

      function collectPane(pane) {
        pane.querySelectorAll('input, select, textarea').forEach(function(f) {
          if (f.name) {
            if (f.type === 'radio') {
              if (f.checked) state.data[f.name] = f.value;
            } else if (f.type === 'checkbox') {
              if (!state.data[f.name]) state.data[f.name] = [];
              if (f.checked) state.data[f.name].push(f.value);
            } else {
              state.data[f.name] = f.value;
            }
          }
        });
      }

      document.querySelectorAll('.btn-next').forEach(function(btn) {
        btn.addEventListener('click', function() {
          const currentPane = document.querySelector('.wizard-pane.active');
          if (!validatePane(currentPane)) return;
          collectPane(currentPane);
          if (state.step < state.totalSteps) showStep(state.step + 1);
        });
      });

      document.querySelectorAll('.btn-prev').forEach(function(btn) {
        btn.addEventListener('click', function() {
          if (state.step > 1) showStep(state.step - 1);
        });
      });

      /* ---------- Step 2: dynamic program list ---------- */
      const courseSelect = document.querySelector('select[name="course_id"]');
      const sectionSelect = document.getElementById('section_select');
      const subjectContainer = document.getElementById('subjectContainer');

      function renderCourseDetails() {
        const selectedCourseId = courseSelect ? courseSelect.value : '';
        const selectedCourse = courseCatalog.find(function(course) {
          return String(course.id) === String(selectedCourseId);
        });

        sectionSelect.innerHTML = '<option value="">Select Section</option>';
        subjectContainer.innerHTML = '<p class="text-muted small mb-0">Select a course to load subjects.</p>';

        if (!selectedCourse) return;

        if (selectedCourse.sections && selectedCourse.sections.length > 0) {
          selectedCourse.sections.forEach(function(section) {
            const option = document.createElement('option');
            option.value = section.id;
            option.textContent = section.section_name;
            sectionSelect.appendChild(option);
          });
        } else {
          sectionSelect.innerHTML = '<option value="">No sections available</option>';
        }

        if (selectedCourse.subjects && selectedCourse.subjects.length > 0) {
          const subjectList = document.createElement('div');
          selectedCourse.subjects.forEach(function(subject) {
            const subjectItem = document.createElement('div');
            subjectItem.className = 'form-check mb-2';
            subjectItem.innerHTML = `
                      <input class="form-check-input" type="checkbox" name="subject_ids[]" value="${subject.id}" id="subject_${subject.id}">
                      <label class="form-check-label w-100 subject-option-label" for="subject_${subject.id}">
                          <strong>${escapeHtml(subject.subject_code)}</strong> - ${escapeHtml(subject.subject_name)}<br>
                          <small class="text-muted">${escapeHtml(subject.schedule_day)} ${escapeHtml(subject.schedule_time)}, Rm ${escapeHtml(subject.room_number)}</small>
                      </label>
                  `;
            subjectList.appendChild(subjectItem);
          });
          subjectContainer.innerHTML = '';
          subjectContainer.appendChild(subjectList);
        } else {
          subjectContainer.innerHTML = '<p class="text-muted small mb-0">No subjects have been added for this course yet.</p>';
        }
      }

      if (courseSelect) {
        courseSelect.addEventListener('change', renderCourseDetails);
      }

      /* ---------- Step 4: payment method selection ---------- */
      const paymentOption = document.getElementById('paymentOption');
      const payMethodsContainer = document.getElementById('paymentMethodsContainer');
      const payMethods = document.querySelectorAll('.pay-method');
      const payDetailBoxes = document.querySelectorAll('.pay-detail-box');
      const payConfirmBtn = document.getElementById('confirmPaymentBtn');
      const paymentStatus = document.getElementById('paymentStatus');

      paymentOption?.addEventListener('change', function() {
        if (this.value === 'downpayment') {
          state.paymentOptionAmount = 2500;
        } else if (this.value === 'full') {
          state.paymentOptionAmount = 15000;
        } else {
          state.paymentOptionAmount = 0;
        }

        if (this.value) {
          payMethodsContainer.classList.remove('d-none');
        } else {
          payMethodsContainer.classList.add('d-none');
        }
      });

      payMethods.forEach(function(card) {
        card.addEventListener('click', function() {
          const method = this.dataset.method;
          payMethods.forEach(function(c) {
            c.classList.remove('selected');
          });
          this.classList.add('selected');
          this.querySelector('input[type="radio"]').checked = true;
          state.paymentMethod = method;

          payDetailBoxes.forEach(function(box) {
            box.classList.toggle('d-none', box.dataset.method !== method);

            // Require fields conditionally
            const fields = box.querySelectorAll('input');
            if (box.dataset.method === method) {
              fields.forEach(f => f.setAttribute('required', 'true'));
            } else {
              fields.forEach(f => f.removeAttribute('required'));
            }
          });

          if (payConfirmBtn && !state.paid) payConfirmBtn.disabled = false;
        });
      });

      if (payConfirmBtn) {
        payConfirmBtn.addEventListener('click', function() {
          if (!state.paymentMethod || !paymentOption.value) return;

          // Card details validation (only when card method chosen)
          if (state.paymentMethod === 'card') {
            const cardPane = document.querySelector('[data-method="card"].pay-detail-box');
            const cardFields = cardPane.querySelectorAll('input[required]');
            let valid = true;
            cardFields.forEach(function(f) {
              if (!f.checkValidity()) {
                valid = false;
                f.classList.add('is-invalid');
              } else {
                f.classList.remove('is-invalid');
              }
            });
            if (!valid) return;
          }

          payConfirmBtn.disabled = true;
          payConfirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing…';

          // Simulated payment processing delay
          setTimeout(function() {
            state.paid = true;
            const isCashPayment = state.paymentMethod === 'cash';
            state.referenceNo = (isCashPayment ? 'CASH-' : 'ENR-') + Date.now().toString().slice(-8);
            state.data.payment_method = state.paymentMethod;
            state.data.reference_no = state.referenceNo;
            state.data.amount_paid = isCashPayment ? 0 : state.paymentOptionAmount;

            if (paymentStatus) {
              paymentStatus.classList.remove('d-none');
              paymentStatus.querySelector('.ref-code').textContent = state.referenceNo;
              paymentStatus.querySelector('.pay-amount').textContent = '₱ ' + state.paymentOptionAmount.toLocaleString() + '.00';
            }

            payConfirmBtn.innerHTML = '✓ Payment Confirmed';
            document.querySelectorAll('.btn-next[data-step="4"]').forEach(function(b) {
              b.disabled = false;
            });

            // Add hidden inputs for payment data so backend receives them
            const form = document.getElementById('enrollmentForm');

            const refInput = document.createElement('input');
            refInput.type = 'hidden';
            refInput.name = 'payment_reference';
            refInput.value = state.referenceNo;
            form.appendChild(refInput);

            const amountInput = document.createElement('input');
            amountInput.type = 'hidden';
            amountInput.name = 'payment_amount';
            amountInput.value = isCashPayment ? 0 : state.paymentOptionAmount;
            form.appendChild(amountInput);

          }, 1400);
        });
      }

      /* ---------- Step 5: build review summary ---------- */
      function buildSummary() {
        const box = document.getElementById('summaryBox');
        if (!box) return;

        const d = state.data;
        const selectedCourseId = d.course_id;
        const selectedCourse = courseCatalog.find(c => String(c.id) === String(selectedCourseId));
        const courseName = selectedCourse ? selectedCourse.course_code : '';

        let sectionName = '';
        if (selectedCourse) {
          const sec = selectedCourse.sections.find(s => String(s.id) === String(d.section_id));
          if (sec) sectionName = sec.section_name;
        }

        const rows = [
          ['Full Name', [d.first_name, d.middle_name, d.last_name].filter(Boolean).join(' ')],
          ['Email', d.email],
          ['Phone', d.contact],
          ['Address', d.address],
          ['Course', courseName],
          ['Year Level', d.year_level + (d.year_level == 1 ? 'st' : d.year_level == 2 ? 'nd' : d.year_level == 3 ? 'rd' : 'th') + ' Year'],
          ['Section', sectionName],
          ['Subjects Selected', (d['subject_ids[]'] || []).length + ' Subjects'],
          ['Guardian', d.guardian + ' (' + d.guardian_contact + ')']
        ];

        box.innerHTML = rows.map(function(r) {
          return '<div class="summary-row"><span>' + r[0] + '</span><span>' + (r[1] || '—') + '</span></div>';
        }).join('');
      }

      function labelForPayment(v) {
        return {
          gcash: 'GCash',
          paymaya: 'Maya',
          card: 'Credit / Debit Card',
          bank: 'Bank Transfer'
        } [v] || '—';
      }

      /* ---------- Final submit ---------- */
      const submitBtn = document.getElementById('submitApplicationBtn');

      // Payments are collected from the student dashboard after approval.
      if (false && submitBtn) {
        submitBtn.addEventListener('click', function(e) {
          if (!state.paid) {
            e.preventDefault();
            Swal.fire({
              icon: 'info',
              title: 'Complete payment first',
              text: 'Please confirm your payment selection before submitting.',
              confirmButtonColor: '#1e5a96'
            });
            showStep(4);
            return;
          }
          // The form will submit natively to process_application.php
        });
      }

      /* ---------- PDF generation ---------- */
      function downloadFilledPDF() {
        const {
          jsPDF
        } = window.jspdf;
        const doc = new jsPDF();
        const d = state.data;

        doc.setFillColor(11, 37, 69);
        doc.rect(0, 0, 210, 28, 'F');
        doc.setTextColor(255, 199, 44);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(18);
        doc.text('EYYSAT', 14, 18);
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(10);
        doc.setFont('helvetica', 'normal');
        doc.text('Enrollment Form', 14, 24);
        doc.setTextColor(20, 20, 20);
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('Student Enrollment Receipt', 14, 40);
        doc.setDrawColor(255, 199, 44);
        doc.setLineWidth(1);
        doc.line(14, 44, 196, 44);

        let y = 56;
        const line = function(label, value) {
          doc.setFont('helvetica', 'bold');
          doc.setFontSize(10);
          doc.text(label + ':', 14, y);
          doc.setFont('helvetica', 'normal');
          doc.text(String(value || '—'), 70, y);
          y += 9;
        };

        const selectedCourse = courseCatalog.find(c => String(c.id) === String(d.course_id));
        const courseName = selectedCourse ? selectedCourse.course_code : '';
        let sectionName = '';
        if (selectedCourse) {
          const sec = selectedCourse.sections.find(s => String(s.id) === String(d.section_id));
          if (sec) sectionName = sec.section_name;
        }

        line('Full Name', [d.first_name, d.middle_name, d.last_name].filter(Boolean).join(' '));
        line('Email', d.email);
        line('Phone', d.contact);
        line('Address', d.address);
        line('Date of Birth', d.birthdate);
        line('Sex', d.sex);
        y += 2;
        line('Course', courseName);
        line('Year Level', d.year_level);
        line('Section', sectionName);
        y += 2;
        line('Guardian Name', d.guardian);
        line('Guardian Contact', d.guardian_contact);
        y += 2;
        line('Date Submitted', new Date().toLocaleString());

        doc.setFontSize(9);
        doc.setTextColor(120, 120, 120);
        doc.text('This document confirms your submitted enrollment details.', 14, 280);

        doc.save('Enrollment-' + (state.referenceNo || 'form') + '.pdf');
      }

      const downloadFilledBtn = document.getElementById('downloadFilledPdfBtn');
      if (downloadFilledBtn) downloadFilledBtn.addEventListener('click', downloadFilledPDF);

      /* ---------- Card number formatting (cosmetic only) ---------- */
      const cardNumberInput = document.getElementById('cardNumber');
      if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
          let v = this.value.replace(/\D/g, '').slice(0, 16);
          this.value = v.replace(/(.{4})/g, '$1 ').trim();
        });
      }
      const cardExpiryInput = document.getElementById('cardExpiry');
      if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', function() {
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