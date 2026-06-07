<?php
require_once 'config/db.php';
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $college_id = strtoupper(trim($_POST['college_id']));
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);

    if (!empty($college_id) && !empty($old_password) && !empty($new_password)) {
        // Step 1: Extract the user record matching the unique identifier
        $stmt = $pdo->prepare("SELECT * FROM users WHERE college_id = ?");
        $stmt->execute([$college_id]);
        $user = $stmt->fetch();

        // Step 2: Authenticate using the old password before granting overwrite rights
        if ($user && password_verify($old_password, $user['password'])) {
            
            // Step 3: Securely hash the new password and commit to the database
            $secure_new_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$secure_new_hash, $user['id']]);
            
            $success = "Credentials updated inside the ERP register. Routing to gateway...";
            header("refresh:2;url=login.php");
        } else {
            $error = "Verification Mismatch: Invalid Identifier or Current Password.";
        }
    } else {
        $error = "Please complete all validation checkpoints.";
    }
}
include 'includes/header.php';
?>
<div class="max-w-md w-full mx-auto my-auto bg-slate-900/40 border border-slate-800/80 backdrop-blur-xl p-8 rounded-2xl shadow-2xl">
    <h2 class="text-2xl font-bold text-white mb-2 text-center tracking-tight">Credential Sync Gateway</h2>
    <p class="text-slate-400 text-xs text-center mb-6">Verify current authentication tokens to establish a new security password</p>

    <?php if(!empty($error)): ?>
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs p-3 rounded-xl mb-4 font-semibold">✕ <?= $error ?></div>
    <?php endif; ?>
    <?php if(!empty($success)): ?>
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs p-3 rounded-xl mb-4 font-semibold">✓ <?= $success ?></div>
    <?php endif; ?>

    <form action="forgot_password.php" method="POST" class="space-y-4">
        <div>
            <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Official ID / Faculty Code</label>
            <input type="text" name="college_id" required placeholder="e.g., ERP2026CS01 or FACULTY001" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
        </div>
        <div>
            <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Current Password</label>
            <input type="password" name="old_password" required placeholder="••••••••" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
        </div>
        <div>
            <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Target New Password</label>
            <input type="password" name="new_password" required placeholder="••••••••" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
        </div>
        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white py-3 rounded-xl font-bold text-sm shadow-xl shadow-indigo-600/10 transition mt-2">Update Password</button>
    </form>
    <p class="text-center text-slate-500 text-xs mt-6"><a href="login.php" class="text-indigo-400 hover:underline">&larr; Return to Login Checkpoint</a></p>
</div>
<?php include 'includes/footer.php'; ?>