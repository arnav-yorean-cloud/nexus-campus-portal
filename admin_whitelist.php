<?php
require_once 'config/db.php';

// Strict Security Gate: Only authenticated Faculty can load this logic engine
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: dashboard.php");
    exit;
}

$error = ''; $success = '';

// Processing Mechanism A: Whitelist New Student ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $college_id = strtoupper(trim($_POST['college_id']));
    $branch = trim($_POST['branch']);
    $section = strtoupper(trim($_POST['section']));

    if (!empty($college_id) && !empty($branch) && !empty($section)) {
        $stmt = $pdo->prepare("SELECT college_id FROM allowed_students WHERE college_id = ?");
        $stmt->execute([$college_id]);
        if ($stmt->fetch()) {
            $error = "Token Conflict: This Student ERP ID is already whitelisted.";
        } else {
            $insert = $pdo->prepare("INSERT INTO allowed_students (college_id, branch, section) VALUES (?, ?, ?)");
            $insert->execute([$college_id, $branch, $section]);
            $success = "Student ID successfully injected into the security gateway.";
        }
    } else {
        $error = "Please fill out all student parsing parameters.";
    }
}

// Processing Mechanism B: Whitelist New Faculty Code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_faculty'])) {
    $faculty_code = strtoupper(trim($_POST['faculty_code']));
    $department = trim($_POST['department']);

    if (!empty($faculty_code) && !empty($department)) {
        $stmt = $pdo->prepare("SELECT faculty_code FROM allowed_faculty WHERE faculty_code = ?");
        $stmt->execute([$faculty_code]);
        if ($stmt->fetch()) {
            $error = "Token Conflict: This Faculty Code is already whitelisted.";
        } else {
            $insert = $pdo->prepare("INSERT INTO allowed_faculty (faculty_code, department) VALUES (?, ?)");
            $insert->execute([$faculty_code, $department]);
            $success = "Faculty Code successfully injected into the security gateway.";
        }
    } else {
        $error = "Please fill out all faculty parsing parameters.";
    }
}

include 'includes/header.php';
?>
<div class="mb-6">
    <a href="dashboard.php" class="inline-flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-indigo-400 transition group">
        <span class="transform group-hover:-translate-x-1 transition duration-200">&larr;</span> <span>Back to Dashboard</span>
    </a>
</div>

<div class="mb-8">
    <h1 class="text-3xl font-black text-white tracking-tight">Identity Whitelist System</h1>
    <p class="text-slate-400 text-sm mt-1">Authorize new credentials. Registered profiles must match these records to pass sign-up security validation loops.</p>
</div>

<?php if(!empty($error)): ?>
    <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs p-4 rounded-xl mb-6 font-semibold">✕ <?= $error ?></div>
<?php endif; ?>
<?php if(!empty($success)): ?>
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs p-4 rounded-xl mb-6 font-semibold">✓ <?= $success ?></div>
<?php endif; ?>

<div class="grid md:grid-cols-2 gap-8">
    <!-- Form Segment A: Student Token Configuration -->
    <div class="bg-slate-900/40 border border-slate-800/80 p-6 rounded-2xl backdrop-blur-md">
        <h2 class="text-xl font-bold text-white mb-1 tracking-tight">Whitelist Student ID</h2>
        <p class="text-slate-500 text-xs mb-6">Authorizes a single student profile register domain node</p>
        
        <form action="admin_whitelist.php" method="POST" class="space-y-4">
            <input type="hidden" name="add_student" value="1">
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Target Student ERP ID</label>
                <input type="text" name="college_id" required placeholder="e.g., ERP2026CS05" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-indigo-500 transition">
            </div>
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Academic Branch</label>
                <input type="text" name="branch" required placeholder="Computer Science Engineering" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-indigo-500 transition">
            </div>
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Section Designation</label>
                <input type="text" name="section" required placeholder="A" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-indigo-500 transition">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 rounded-xl font-bold text-xs transition">Authorize Student ID</button>
        </form>
    </div>

    <!-- Form Segment B: Faculty Token Configuration -->
    <div class="bg-slate-900/40 border border-slate-800/80 p-6 rounded-2xl backdrop-blur-md">
        <h2 class="text-xl font-bold text-white mb-1 tracking-tight">Whitelist Faculty Code</h2>
        <p class="text-slate-500 text-xs mb-6">Authorizes a single faculty administrative register node</p>
        
        <form action="admin_whitelist.php" method="POST" class="space-y-4">
            <input type="hidden" name="add_faculty" value="1">
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Target Faculty Code</label>
                <input type="text" name="faculty_code" required placeholder="e.g., FACULTY005" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-violet-500 transition">
            </div>
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase tracking-wider">Assigned Department</label>
                <input type="text" name="department" required placeholder="Department of Computer Science" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-violet-500 transition">
            </div>
            <button type="submit" class="w-full bg-violet-600 hover:bg-violet-500 text-white py-2.5 rounded-xl font-bold text-xs transition">Authorize Faculty Code</button>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>