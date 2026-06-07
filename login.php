<?php 
require_once 'config/db.php';
$error = '';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $college_id = strtoupper(trim($_POST['college_id']));
    $password = trim($_POST['password']);

    if (!empty($college_id) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE college_id = ?");
        $stmt->execute([$college_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['college_id'] = $user['college_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Invalid authorization tokens.';
        }
    } else {
        $error = 'Provide all authentication inputs.';
    }
}
include 'includes/header.php';
?>
<div class="max-w-md w-full mx-auto my-auto bg-slate-900/40 border border-slate-800/80 backdrop-blur-xl p-8 rounded-2xl shadow-2xl">
    <h2 class="text-2xl font-bold text-white mb-2 text-center tracking-tight">Welcome Back</h2>
    <p class="text-slate-400 text-xs text-center mb-6">Enter official campus credentials to initialize session variables</p>
    
    <?php if(!empty($error)): ?>
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs p-3 rounded-xl mb-4 font-semibold"><?= $error ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST" class="space-y-4">
        <div>
            <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Official ID / Faculty Code</label>
            <input type="text" name="college_id" required placeholder="e.g., ERP2026CS01 or FACULTY001" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
        </div>
        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Password</label>
                <a href="forgot_password.php" class="text-xs text-indigo-400 hover:underline">Forgot Password?</a>
            </div>
            <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
        </div>
        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white py-3 rounded-xl font-bold text-sm shadow-xl shadow-indigo-600/10 transition mt-2">Sign In</button>
    </form>
    <div class="text-center text-slate-500 text-xs mt-6 space-y-2">
        <p>New Student? <a href="register.php" class="text-indigo-400 hover:underline">Register Here</a></p>
        <p>New Faculty Member? <a href="register_faculty.php" class="text-violet-400 hover:underline">Verify Faculty Code</a></p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>