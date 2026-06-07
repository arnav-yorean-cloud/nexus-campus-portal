<?php
require_once 'config/db.php';
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_code = strtoupper(trim($_POST['faculty_code']));
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($faculty_code) && !empty($full_name) && !empty($email) && !empty($password)) {
        // Step A: Crosscheck with administrative allowed records
        $check_whitelist = $pdo->prepare("SELECT * FROM allowed_faculty WHERE faculty_code = ?");
        $check_whitelist->execute([$faculty_code]);
        $whitelist_record = $check_whitelist->fetch();

        if (!$whitelist_record) {
            $error = "Access Denied: This Faculty Code is not whitelisted by the administration.";
        } else {
            // Step B: Block identifier collisions
            $check_user = $pdo->prepare("SELECT id FROM users WHERE college_id = ? OR email = ?");
            $check_user->execute([$faculty_code, $email]);
            if ($check_user->fetch()) {
                $error = "Identity Token Conflict: Code or Email is already registered.";
            } else {
                // Step C: Initialize Security Vectors
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (college_id, password, role, full_name, email, branch) VALUES (?, ?, 'faculty', ?, ?, ?)");
                $stmt->execute([$faculty_code, $hashed_password, $full_name, $email, $whitelist_record['department']]);
                
                $success = "Faculty node initialized successfully. Routing to access panel...";
                header("refresh:2;url=login.php");
            }
        }
    } else {
        $error = "Complete all processing fields.";
    }
}
include 'includes/header.php';
?>
<div class="max-w-md w-full mx-auto bg-slate-900/40 border border-slate-800/80 backdrop-blur-xl p-8 rounded-2xl shadow-2xl">
    <h2 class="text-2xl font-bold text-white mb-2 text-center tracking-tight">Initialize Faculty Node</h2>
    <p class="text-slate-400 text-xs text-center mb-6">Cross-checks entered identifiers with master Faculty whitelists</p>

    <?php if(!empty($error)): ?>
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs p-3 rounded-xl mb-4 font-semibold"><?= $error ?></div>
    <?php endif; ?>
    <?php if(!empty($success)): ?>
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs p-3 rounded-xl mb-4 font-semibold"><?= $success ?></div>
    <?php endif; ?>

    <form action="register_faculty.php" method="POST" class="space-y-4">
        <div>
            <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Authorized Faculty Code</label>
            <input type="text" name="faculty_code" required placeholder="e.g., FACULTY001" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-violet-500 transition">
        </div>
        <div>
            <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Full Name with Title</label>
            <input type="text" name="full_name" required placeholder="Dr. Sarah Jenkins" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-violet-500 transition">
        </div>
        <div>
            <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Institutional Email</label>
            <input type="email" name="email" required placeholder="sarah.j@college.edu" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-violet-500 transition">
        </div>
        <div>
            <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Establish Password</label>
            <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-violet-500 transition">
        </div>
        <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white py-3 rounded-xl font-bold text-sm shadow-xl shadow-violet-600/10 transition mt-2">Verify & Register</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>