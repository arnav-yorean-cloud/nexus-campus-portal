<?php
require_once 'config/db.php';
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $college_id = strtoupper(trim($_POST['college_id']));
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($college_id) && !empty($full_name) && !empty($email) && !empty($password)) {
        // Step A: Audit against the allowed_students whitelisted database
        $check_whitelist = $pdo->prepare("SELECT * FROM allowed_students WHERE college_id = ?");
        $check_whitelist->execute([$college_id]);
        $whitelist_record = $check_whitelist->fetch();

        if (!$whitelist_record) {
            $error = "Access Denied: This College ID is not whitelisted in the ERP register.";
        } else {
            // Step B: Ensure identity duplication is barred
            $check_user = $pdo->prepare("SELECT id FROM users WHERE college_id = ? OR email = ?");
            $check_user->execute([$college_id, $email]);
            if ($check_user->fetch()) {
                $error = "Identity Token Conflict: ID or Email is already initialized.";
            } else {
                // Step C: Initialize Profile Map and Save
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (college_id, password, role, full_name, email, branch, section) VALUES (?, ?, 'student', ?, ?, ?, ?)");
                $stmt->execute([$college_id, $hashed_password, $full_name, $email, $whitelist_record['branch'], $whitelist_record['section']]);
                $success = "Identity generated successfully. Redirecting to access nodes...";
                header("refresh:2;url=login.php");
            }
        }
    } else {
        $error = "Complete all mandatory registration nodes.";
    }
}
include 'includes/header.php';
?>
<div class="max-w-md w-full mx-auto bg-slate-900/40 border border-slate-800/80 backdrop-blur-xl p-8 rounded-2xl shadow-2xl">
    <h2 class="text-2xl font-bold text-white mb-2 text-center tracking-tight">Initialize Student Node</h2>
    <p class="text-slate-400 text-xs text-center mb-6">Cross-checks entered tokens with master ERP whitelists</p>

    <?php if(!empty($error)): ?>
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs p-3 rounded-xl mb-4 font-semibold"><?= $error ?></div>
    <?php endif; ?>
    <?php if(!empty($success)): ?>
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs p-3 rounded-xl mb-4 font-semibold"><?= $success ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST" class="space-y-4">
        <div>
            <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Predefined ERP College ID</label>
            <input type="text" name="college_id" required placeholder="e.g., ERP2026CS01" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
        </div>
        <div>
            <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Full Name</label>
            <input type="text" name="full_name" required placeholder="John Doe" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
        </div>
        <div>
            <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Institutional Email</label>
            <input type="email" name="email" required placeholder="john.doe@college.edu" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
        </div>
        <div>
            <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Establish Password</label>
            <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
        </div>
        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white py-3 rounded-xl font-bold text-sm shadow-xl shadow-indigo-600/10 transition mt-2">Verify & Register</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>