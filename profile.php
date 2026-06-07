<?php
require_once 'config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$status_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_no = trim($_POST['contact_no']);
    $sex = trim($_POST['sex']);
    $age = !empty($_POST['age']) ? intval($_POST['age']) : null;
    $parents_name = trim($_POST['parents_name']);
    $bio = trim($_POST['bio']);
    $skills = trim($_POST['skills']);
    $achievements = trim($_POST['achievements']);

    $stmt = $pdo->prepare("UPDATE users SET contact_no = ?, sex = ?, age = ?, parents_name = ?, bio = ?, skills = ?, achievements = ? WHERE id = ?");
    $stmt->execute([$contact_no, $sex, $age, $parents_name, $bio, $skills, $achievements, $user_id]);
    $status_message = 'Profile data records updated inside core nodes.';
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
include 'includes/header.php';
?>

<!-- In-App Back Navigation Arrow Component -->
<div class="mb-6">
    <a href="dashboard.php" class="inline-flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-indigo-400 transition group">
        <span class="transform group-hover:-translate-x-1 transition duration-200">&larr;</span> <span>Back to Dashboard</span>
    </a>
</div>

<div class="grid lg:grid-cols-3 gap-8 items-start">
    <!-- Profile Visual Display Card -->
    <div class="lg:col-span-1 bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 backdrop-blur-md sticky top-24">
        <div class="flex flex-col items-center text-center">
            <div class="h-20 w-20 rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-500 text-white font-black text-2xl flex items-center justify-center shadow-xl shadow-indigo-500/10 mb-4">
                <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
            </div>
            <h3 class="text-xl font-bold text-white tracking-tight"><?= htmlspecialchars($user['full_name']) ?></h3>
            <span class="text-xs text-indigo-400 font-semibold mt-0.5"><?= htmlspecialchars($user['college_id']) ?></span>
            <p class="text-slate-500 text-xs mt-3 bg-slate-950 px-3 py-1 rounded-full border border-slate-800"><?= htmlspecialchars($user['branch']) ?> | Sec-<?= htmlspecialchars($user['section']) ?></p>
        </div>
        <div class="border-t border-slate-800/80 mt-6 pt-6 space-y-3 text-xs">
            <div><span class="text-slate-500 block uppercase font-bold tracking-wider mb-0.5">Contact Node</span> <span class="text-slate-300 font-medium"><?= htmlspecialchars($user['contact_no'] ?? 'Unassigned') ?></span></div>
            <div><span class="text-slate-500 block uppercase font-bold tracking-wider mb-0.5">Parent Reference</span> <span class="text-slate-300 font-medium"><?= htmlspecialchars($user['parents_name'] ?? 'Unassigned') ?></span></div>
            <div><span class="text-slate-500 block uppercase font-bold tracking-wider mb-0.5">Core Bio</span> <p class="text-slate-400 mt-1 italic"><?= nl2br(htmlspecialchars($user['bio'] ?? 'Write something...')) ?></p></div>
        </div>
    </div>

    <!-- ERP Input Modification Framework -->
    <div class="lg:col-span-2 bg-slate-900/40 border border-slate-800/80 rounded-2xl p-8 backdrop-blur-md">
        <h2 class="text-2xl font-black text-white tracking-tight mb-2">Modify Profile Parameters</h2>
        <p class="text-slate-400 text-xs mb-6">Update student record details for tracking matrices and portfolios.</p>

        <?php if(!empty($status_message)): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs p-3 rounded-xl mb-6 font-semibold">✓ <?= $status_message ?></div>
        <?php endif; ?>

        <form action="profile.php" method="POST" class="space-y-5">
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase">Contact Number</label>
                    <input type="text" name="contact_no" value="<?= htmlspecialchars($user['contact_no'] ?? '') ?>" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase">Sex / Gender</label>
                    <input type="text" name="sex" value="<?= htmlspecialchars($user['sex'] ?? '') ?>" placeholder="e.g., Male/Female" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase">Age</label>
                    <input type="number" name="age" value="<?= htmlspecialchars($user['age'] ?? '') ?>" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase">Father's / Guardian's Name</label>
                <input type="text" name="parents_name" value="<?= htmlspecialchars($user['parents_name'] ?? '') ?>" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-2.5 text-sm text-white focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase">Professional Bio</label>
                <textarea name="bio" rows="2" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-indigo-500"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            </div>
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase">Technical Skills Inventory</label>
                <input type="text" name="skills" value="<?= htmlspecialchars($user['skills'] ?? '') ?>" placeholder="e.g., PHP, Tailwind, MySQL, Java" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1.5 uppercase">Core Achievements / Credentials</label>
                <textarea name="achievements" rows="2" placeholder="List hackathons, certifications, or roles..." class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-indigo-500"><?= htmlspecialchars($user['achievements'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-lg shadow-indigo-600/10 transition">Save Data Arrays</button>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>