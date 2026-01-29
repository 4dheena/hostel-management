<?php
require_once 'database/db_connect.php';

/* ================= APPLICATION WINDOW ================= */
$result = $conn->query("SELECT start_date, end_date FROM application_settings WHERE id = 1");
$settings = $result->fetch_assoc();

$today = date('Y-m-d');
$editable = ($today >= $settings['start_date'] && $today <= $settings['end_date']);

/* ================= STUDENT IDENTIFIER ================= */
/* TODO: replace with session later */
$student_email = 'student@example.com';

/* ================= LOAD APPLICATION ================= */
$stmt = $conn->prepare("SELECT * FROM hostel_applications WHERE student_email = ?");
$stmt->bind_param("s", $student_email);
$stmt->execute();
$application = $stmt->get_result()->fetch_assoc();

/* ================= PASSWORD CHECK ================= */
$password_set = false;
$pwdStmt = $conn->prepare("SELECT password_hash FROM students WHERE email = ?");
$pwdStmt->bind_param("s", $student_email);
$pwdStmt->execute();
$pwdData = $pwdStmt->get_result()->fetch_assoc();

if (!empty($pwdData['password_hash'])) {
    $password_set = true;
}

$distance_km = $application['distance_km'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hostel Application</title>
<link rel="stylesheet" href="apply.css">
</head>
<body>

<div class="container">

<h1>Hostel Application</h1>
<p>Application Window:
<strong><?= $settings['start_date'] ?></strong> –
<strong><?= $settings['end_date'] ?></strong>
</p>

<?php if ($editable): ?>
<p class="status-open">✅ Application window is OPEN</p>
<?php else: ?>
<p class="status-closed">🔒 Application window is CLOSED</p>
<?php endif; ?>

<div class="steps">
  <div class="step active" onclick="showStep(1)">Personal Info</div>
  <div class="step" onclick="showStep(2)">Priority Data</div>
  <div class="step" onclick="showStep(3)">Documents</div>
  <div class="step" onclick="showStep(4)">Account Security</div>
</div>

<form method="POST"
      action="saveApplication.php"
      enctype="multipart/form-data"
      onsubmit="return validatePassword();">


<!-- ================= STEP 1 ================= -->
<div class="section active" id="step1">
  <div class="card">
    <h3>Personal Information</h3>

    <div class="form-grid">
      <div>
        <label>Full Name</label>
        <input type="text" name="full_name"
          value="<?= htmlspecialchars($application['full_name'] ?? '') ?>"
          <?= !$editable ? 'readonly' : '' ?>>
      </div>

      <div>
        <label>Register Number</label>
        <input type="text" name="register_number"
          value="<?= htmlspecialchars($application['register_number'] ?? '') ?>"
          <?= !$editable ? 'readonly' : '' ?>>
      </div>

      <div>
        <label>Email</label>
        <input type="email" name="personal_email"
          value="<?= htmlspecialchars($application['personal_email'] ?? '') ?>"
          <?= !$editable ? 'readonly' : '' ?>>
      </div>

      <div>
        <label>Phone</label>
        <input type="text" name="phone"
          value="<?= htmlspecialchars($application['phone'] ?? '') ?>"
          <?= !$editable ? 'readonly' : '' ?>>
      </div>

      <div>
        <label>Gender</label>
        <select name="gender" <?= !$editable ? 'disabled' : '' ?>>
          <option value="">Select</option>
          <option value="Male" <?= ($application['gender'] ?? '')==='Male'?'selected':'' ?>>Male</option>
          <option value="Female" <?= ($application['gender'] ?? '')==='Female'?'selected':'' ?>>Female</option>
          <option value="Other" <?= ($application['gender'] ?? '')==='Other'?'selected':'' ?>>Other</option>
        </select>
      </div>

      <div>
        <label>Department</label>
        <input type="text" name="department"
          value="<?= htmlspecialchars($application['department'] ?? '') ?>"
          <?= !$editable ? 'readonly' : '' ?>>
      </div>

      <div>
        <label>Year / Semester</label>
        <input type="text" name="year_semester"
          value="<?= htmlspecialchars($application['year_semester'] ?? '') ?>"
          <?= !$editable ? 'readonly' : '' ?>>
      </div>

      <div>
        <label>Date of Birth</label>
        <input type="date" name="dob"
          value="<?= $application['dob'] ?? '' ?>"
          <?= !$editable ? 'readonly' : '' ?>>
      </div>

      <div>
        <label>Pin Code</label>
       <input type="text" name="pincode" maxlength="6"
       onblur="fetchDistance()">
      </div>

      <div>
        <label>Distance (km)</label>
        <input type="text" value="<?= htmlspecialchars($distance_km) ?>" readonly>
        <input type="hidden" name="distance_km" value="<?= htmlspecialchars($distance_km) ?>">
      </div>
    </div>
  </div>
</div>

<!-- ================= STEP 2 ================= -->
<div class="section" id="step2">
  <div class="priority-layout">

    <div class="card">
      <h3>Priority Data</h3>

      <label>Annual Family Income (INR)</label>
      <input type="number" name="annual_income"
        value="<?= htmlspecialchars($application['annual_income'] ?? '') ?>"
        <?= !$editable ? 'readonly' : '' ?>>

      <br><br>

      <label>Physical Disability (PWD)</label>
      <select name="pwd_status" id="pwd_status" onchange="toggleDisability()"
        <?= !$editable ? 'disabled' : '' ?>>
        <option value="">Select</option>
        <option value="No" <?= ($application['pwd_status'] ?? '')==='No'?'selected':'' ?>>No</option>
        <option value="Yes" <?= ($application['pwd_status'] ?? '')==='Yes'?'selected':'' ?>>Yes</option>
        <option value="NA" <?= ($application['pwd_status'] ?? '')==='NA'?'selected':'' ?>>Not Applicable</option>
      </select>

      <div id="disabilityBox">
        <label>Disability Percentage (%)</label>
        <input type="number" name="disability_percentage"
          min="1" max="100"
          placeholder="Enter percentage"
          value="<?= htmlspecialchars($application['disability_percentage'] ?? '') ?>"
          <?= !$editable ? 'readonly' : '' ?>>
      </div>
    </div>

    <div class="card info-box">
      <h3>How Priority is Calculated</h3>
      <ul>
        <li>Distance from home</li>
        <li>Family income</li>
        <li>PWD & disability percentage</li>
      </ul>
    </div>

  </div>
</div>

<!-- ================= STEP 3 ================= -->
<div class="section" id="step3">
  <div class="card">
    <h3>Documents</h3>

    <label>Income Certificate (PDF)</label>
    <input type="file" name="income_certificate" <?= !$editable?'disabled':'' ?>><br><br>

    <label>PWD Certificate (PDF)</label>
    <input type="file" name="pwd_certificate" <?= !$editable?'disabled':'' ?>><br><br>

    <label>Identity Proof (PDF)</label>
    <input type="file" name="id_proof" <?= !$editable?'disabled':'' ?>>
  </div>
</div>

<!-- ================= STEP 4 ================= -->
<div class="section" id="step4">
  <div class="card security-card">
    <h3>🔐 Account Security</h3>

    <?php if ($password_set): ?>
      <div class="password-set">
        ✅ Password already set for this account.
        <p class="password-note">Contact admin to reset if required.</p>
      </div>
    <?php else: ?>
      <div class="form-grid">
        <div>
          <label>New Password</label>
          <input type="password" name="new_password" <?= !$editable?'disabled':'' ?>>
        </div>

        <div>
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" <?= !$editable?'disabled':'' ?>>
        </div>
      </div>
      <p class="password-hint">Password must contain atleast 8 characters , including uppercase ,lowercase ,number and special characters.</p>
    <?php endif; ?>
  </div>
</div>

<div class="actions">
  <button type="button" onclick="prevStep()">Back</button>
  <?php if ($editable): ?>
    <button type="submit">Save Application</button>
  <?php else: ?>
    <button disabled>Editing Disabled</button>
  <?php endif; ?>
</div>

</form>
</div>

<script>
let currentStep = 1;

function showStep(step){
  if(step<1 || step>4) return;
  currentStep = step;
  document.querySelectorAll('.section').forEach(s=>s.classList.remove('active'));
  document.querySelectorAll('.step').forEach(s=>s.classList.remove('active'));
  document.getElementById('step'+step).classList.add('active');
  document.querySelectorAll('.step')[step-1].classList.add('active');
}

function prevStep(){
  showStep(currentStep-1);
}

function toggleDisability(){
  const pwd = document.getElementById('pwd_status').value;
  const box = document.getElementById('disabilityBox');
  box.style.display = (pwd === 'Yes') ? 'block' : 'none';
}

document.addEventListener("DOMContentLoaded", toggleDisability);
</script>
<script>
function validatePassword() {
  const pwd = document.querySelector('input[name="new_password"]');
  const confirm = document.querySelector('input[name="confirm_password"]');

  if (!pwd || pwd.value === '') return true;

  const password = pwd.value;

  if (
    password.length < 8 ||
    !/[A-Z]/.test(password) ||
    !/[a-z]/.test(password) ||
    !/[0-9]/.test(password) ||
    !/[\W_]/.test(password)
  ) {
    alert(
      "Password must be at least 8 characters and include:\n" +
      "• Uppercase letter\n" +
      "• Lowercase letter\n" +
      "• Number\n" +
      "• Special character"
    );
    return false;
  }

  if (password !== confirm.value) {
    alert("Passwords do not match.");
    return false;
  }

  return true;
}
</script>
<script>
function fetchDistance() {
  const pin = document.querySelector('input[name="pincode"]').value;
  if (pin.length !== 6) return;

  fetch("get_distance.php?pincode=" + pin)
    .then(res => res.text())
    .then(distance => {
      if (distance) {
        document.querySelector('input[readonly]').value = distance;
        document.querySelector('input[name="distance_km"]').value = distance;
      }
    });
}
</script>



</body>
</html>
