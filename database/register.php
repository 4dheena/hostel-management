<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Hostel Registration</title>
<style>
  body { font-family: Arial, sans-serif; background:#f6f8fb; display:flex; align-items:center; justify-content:center; height:100vh; }
  .card { background:#fff; padding:20px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); width:420px; }
  label { display:block; margin-top:10px; font-size:14px; }
  input, select, button { width:100%; padding:8px; margin-top:6px; box-sizing:border-box; }
  .hidden { display:none; }
  .row { display:flex; gap:8px; }
  .row > input { flex:1; }
  .hint { font-size:12px; color:#666; margin-top:6px; }
</style>
<script>
function onDesignationChange() {
  const desig = document.getElementById('role').value;
  document.getElementById('studentFields').style.display = (desig === 'Student') ? 'block' : 'none';
  document.getElementById('staffFields').style.display = (desig === 'Matron' || desig === 'ChiefWarden') ? 'block' : 'none';
}
window.addEventListener('DOMContentLoaded', () => {
  document.getElementById('role').addEventListener('change', onDesignationChange);
  onDesignationChange();
});
</script>
</head>
<body>
  <div class="card">
    <h2>Register</h2>
    <form action="register_action.php" method="post" autocomplete="off">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required maxlength="50">

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required minlength="6">

      <label for="role">Designation / Role</label>
      <select id="role" name="role" required>
        <option value="">-- Select Role --</option>
        <option value="Student">Student</option>
        <option value="Matron">Matron</option>
        <option value="ChiefWarden">Chief Warden</option>
      </select>

      <!-- Student fields -->
      <div id="studentFields" class="hidden">
        <label for="s_name">Full Name</label>
        <input type="text" id="s_name" name="s_name" maxlength="100">

        <label for="s_email">Email</label>
        <input type="email" id="s_email" name="s_email" maxlength="100">

        <label for="s_phone">Phone</label>
        <input type="text" id="s_phone" name="s_phone" maxlength="15">

        <label for="s_hostel">Hostel</label>
        <select id="s_hostel" name="s_hostel">
          <option value="">-- Select Hostel (optional) --</option>
          <!-- Option values will be populated server-side or add manually -->
        </select>

        <label for="s_room">Room (optional)</label>
        <input type="text" id="s_room" name="s_room" maxlength="10" placeholder="Room number or ID">
      </div>

      <!-- Staff fields -->
      <div id="staffFields" class="hidden">
        <label for="st_name">Full Name</label>
        <input type="text" id="st_name" name="st_name" maxlength="100">

        <label for="st_email">Email</label>
        <input type="email" id="st_email" name="st_email" maxlength="100">

        <label for="st_phone">Phone</label>
        <input type="text" id="st_phone" name="st_phone" maxlength="15">

        <label for="st_hostel">Assigned Hostel (optional)</label>
        <select id="st_hostel" name="st_hostel">
          <option value="">-- Select Hostel (optional) --</option>
          <!-- Option values will be populated server-side or add manually -->
        </select>
      </div>

      <div style="margin-top:12px;">
        <button type="submit">Register</button>
      </div>

      <p class="hint">Note: Student details will be stored in <code>students</code>. Staff details are saved in <code>staff</code> (create the table if you followed recommendation).</p>
    </form>
  </div>
</body>
</html>
