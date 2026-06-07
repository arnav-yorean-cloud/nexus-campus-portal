<?php
require_once 'config/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$global_items_count = $pdo->query("SELECT COUNT(*) FROM lost_found")->fetchColumn();
$global_forum_count = $pdo->query("SELECT COUNT(*) FROM forum_posts")->fetchColumn();

if ($role === 'faculty') {
    $total_tickets = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
    $pending_tickets = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Pending'")->fetchColumn();
    $resolved_tickets = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Resolved'")->fetchColumn();
} else {
    $total_tickets = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE student_id = ?");
    $total_tickets->execute([$user_id]);
    $total_tickets = $total_tickets->fetchColumn();

    $pending_tickets = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE student_id = ? AND status = 'Pending'");
    $pending_tickets->execute([$user_id]);
    $pending_tickets = $pending_tickets->fetchColumn();

    $resolved_tickets = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE student_id = ? AND status = 'Resolved'");
    $resolved_tickets->execute([$user_id]);
    $resolved_tickets = $resolved_tickets->fetchColumn();
}

include 'includes/header.php';
?>
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-slate-900/30 border border-slate-800/80 p-6 rounded-2xl backdrop-blur-md">
    <div>
        <h1 class="text-3xl font-black text-white tracking-tight">Command Center</h1>
        <p class="text-slate-400 text-sm mt-1">Operational nodes active for user: <span class="text-indigo-400 font-semibold"><?= htmlspecialchars($_SESSION['full_name']) ?></span></p>
    </div>
    <div class="text-xs text-slate-500 font-bold bg-slate-950 px-4 py-2 rounded-xl border border-slate-800 h-fit self-start md:self-auto">SYSTEM DATE: <?= date('d M Y') ?></div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-slate-950/40 border border-slate-800/60 p-5 rounded-xl backdrop-blur-sm">
        <span class="text-slate-500 text-[10px] font-bold uppercase tracking-wider block"><?= $role === 'faculty' ? 'Total Campus Tickets' : 'My Filed Tickets' ?></span>
        <span class="text-2xl font-black text-white block mt-1"><?= $total_tickets ?></span>
    </div>
    <div class="bg-slate-950/40 border border-slate-800/60 p-5 rounded-xl backdrop-blur-sm">
        <span class="text-amber-500/80 text-[10px] font-bold uppercase tracking-wider block">Tickets Pending</span>
        <span class="text-2xl font-black text-amber-400 block mt-1"><?= $pending_tickets ?></span>
    </div>
    <div class="bg-slate-950/40 border border-slate-800/60 p-5 rounded-xl backdrop-blur-sm">
        <span class="text-emerald-500/80 text-[10px] font-bold uppercase tracking-wider block">Tickets Resolved</span>
        <span class="text-2xl font-black text-emerald-400 block mt-1"><?= $resolved_tickets ?></span>
    </div>
    <div class="bg-slate-950/40 border border-slate-800/60 p-5 rounded-xl backdrop-blur-sm">
        <span class="text-purple-500/80 text-[10px] font-bold uppercase tracking-wider block">Active Noticeboard Items</span>
        <span class="text-2xl font-black text-purple-400 block mt-1"><?= $global_items_count ?></span>
    </div>
</div>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Card 1 -->
    <div class="bg-slate-900/40 border border-slate-800/80 p-6 rounded-2xl flex flex-col justify-between hover:border-indigo-500/50 hover:shadow-2xl hover:shadow-indigo-500/5 transition duration-300">
        <div>
            <div class="h-10 w-10 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center font-bold mb-4 text-sm">🛡️</div>
            <h2 class="text-lg font-bold text-white mb-2 tracking-tight">Infrastructure Complaints</h2>
            <p class="text-slate-400 text-xs leading-relaxed">
                <?= $_SESSION['role'] === 'faculty' ? 'Audit student-filed logs across campus departments and modify tracking parameters.' : 'File verified infrastructure complaints directly to active faculty panels.'; ?>
            </p>
        </div>
        <a href="complaints.php" class="mt-6 w-full text-center bg-slate-950/80 hover:bg-indigo-600 border border-slate-800 hover:border-indigo-500 text-slate-300 hover:text-white py-2.5 rounded-xl text-xs font-bold transition">Launch Module &rarr;</a>
    </div>

    <!-- Card 2 -->
    <div class="bg-slate-900/40 border border-slate-800/80 p-6 rounded-2xl flex flex-col justify-between hover:border-emerald-500/50 hover:shadow-2xl hover:shadow-emerald-500/5 transition duration-300">
        <div>
            <div class="h-10 w-10 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center font-bold mb-4 text-sm">🔍</div>
            <h2 class="text-lg font-bold text-white mb-2 tracking-tight">Lost & Found Manifest</h2>
            <p class="text-slate-400 text-xs leading-relaxed">
                <?= $_SESSION['role'] === 'faculty' ? 'Monitor missing campus artifacts, contact reporting student cells, and audit lists.' : 'Post misplaced assets or claim found inventories directly inside student grids.'; ?>
            </p>
        </div>
        <a href="lostfound.php" class="mt-6 w-full text-center bg-slate-950/80 hover:bg-emerald-600 border border-slate-800 hover:border-emerald-500 text-slate-300 hover:text-white py-2.5 rounded-xl text-xs font-bold transition">Launch Module &rarr;</a>
    </div>

    <!-- Card 3 -->
    <div class="bg-slate-900/40 border border-slate-800/80 p-6 rounded-2xl flex flex-col justify-between hover:border-purple-500/50 hover:shadow-2xl hover:shadow-purple-500/5 transition duration-300">
        <div>
            <div class="h-10 w-10 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center justify-center font-bold mb-4 text-sm">💬</div>
            <h2 class="text-lg font-bold text-white mb-2 tracking-tight">Interactive Open Forum</h2>
            <p class="text-slate-400 text-xs leading-relaxed">Unified asynchronous discussion node for networking, cross-departmental messaging, and micro-posts.</p>
        </div>
        <a href="forum.php" class="mt-6 w-full text-center bg-slate-950/80 hover:bg-purple-600 border border-slate-800 hover:border-purple-500 text-slate-300 hover:text-white py-2.5 rounded-xl text-xs font-bold transition">Launch Module &rarr;</a>
    </div>

    <!-- Exclusive Card 4: Whitelist Management Node (Faculty Only) -->
    <?php if ($role === 'faculty'): ?>
        <div class="bg-slate-900/40 border border-violet-800/60 p-6 rounded-2xl flex flex-col justify-between hover:border-violet-500 hover:shadow-2xl hover:shadow-violet-500/5 transition duration-300">
            <div>
                <div class="h-10 w-10 rounded-xl bg-violet-500/10 text-violet-400 border border-violet-500/20 flex items-center justify-center font-bold mb-4 text-sm">🔑</div>
                <h2 class="text-lg font-bold text-white mb-2 tracking-tight">Identity Whitelist Manager</h2>
                <p class="text-slate-400 text-xs leading-relaxed">Authorize new registration tokens. Insert valid Student ERP IDs and Faculty Codes directly into database gates.</p>
            </div>
            <a href="admin_whitelist.php" class="mt-6 w-full text-center bg-slate-950/80 hover:bg-violet-600 border border-slate-800 hover:border-violet-500 text-slate-300 hover:text-white py-2.5 rounded-xl text-xs font-bold transition">Manage Whitelists &rarr;</a>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>