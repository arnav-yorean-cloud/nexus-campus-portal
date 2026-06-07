<?php
require_once 'config/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_item'])) {
    $item_name = htmlspecialchars(trim($_POST['item_name']));
    $type = htmlspecialchars($_POST['type']);
    $location = htmlspecialchars(trim($_POST['location']));
    $contact = htmlspecialchars(trim($_POST['contact']));

    if (!empty($item_name) && !empty($contact)) {
        $stmt = $pdo->prepare("INSERT INTO lost_found (user_id, item_name, type, location, contact) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $item_name, $type, $location, $contact]);
        header("Location: lostfound.php");
        exit;
    }
}

// Search Filtering Operational Module
$search_query = '';
if (!empty($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $stmt = $pdo->prepare("SELECT lf.*, u.full_name, u.role FROM lost_found lf JOIN users u ON lf.user_id = u.id WHERE lf.item_name LIKE ? OR lf.location LIKE ? ORDER BY lf.id DESC");
    $stmt->execute(["%$search_query%", "%$search_query%"]);
    $items = $stmt->fetchAll();
} else {
    $items = $pdo->query("SELECT lf.*, u.full_name, u.role FROM lost_found lf JOIN users u ON lf.user_id = u.id ORDER BY lf.id DESC")->fetchAll();
}

include 'includes/header.php';
?>
<div class="mb-6">
    <a href="dashboard.php" class="inline-flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-indigo-400 transition group">
        <span class="transform group-hover:-translate-x-1 transition duration-200">&larr;</span> <span>Back to Dashboard</span>
    </a>
</div>

<div class="bg-slate-900/30 border border-slate-800/80 rounded-2xl p-4 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4 backdrop-blur-md">
    <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider self-start sm:self-auto">🔍 Query Filter Engine</h3>
    <form action="lostfound.php" method="GET" class="w-full sm:w-auto flex items-center space-x-2">
        <input type="text" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search item or location..." class="bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 w-full sm:w-64 transition">
        <?php if(!empty($search_query)): ?>
            <a href="lostfound.php" class="text-xs text-slate-500 hover:text-white transition">Clear</a>
        <?php endif; ?>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs px-4 py-2 rounded-xl transition">Query</button>
    </form>
</div>

<div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1 bg-slate-900/40 border border-slate-800/80 p-6 rounded-2xl backdrop-blur-md h-fit">
        <h2 class="text-xl font-bold text-white mb-2 tracking-tight">Broadcast Asset</h2>
        <p class="text-slate-500 text-xs mb-6">Enter catalog details to pin items to the public noticeboard grid.</p>
        <form action="lostfound.php" method="POST" class="space-y-4">
            <input type="hidden" name="post_item" value="1">
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1">Item Identifier Name</label>
                <input type="text" name="item_name" required placeholder="e.g., Apple iPad Pro" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1">Operational Mode</label>
                <div class="flex gap-4 mt-1.5 text-xs font-bold text-slate-300">
                    <label class="flex items-center"><input type="radio" name="type" value="Lost" checked class="mr-2 accent-emerald-500"> Lost Manifest</label>
                    <label class="flex items-center"><input type="radio" name="type" value="Found" class="mr-2 accent-emerald-500"> Found Register</label>
                </div>
            </div>
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1">Geographical Vector (Location)</label>
                <input type="text" name="location" required placeholder="e.g., Block B Aud. Labs" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-slate-400 text-xs font-semibold mb-1">Contact Handlers</label>
                <input type="text" name="contact" required placeholder="Intercom, Phone or Email" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:border-emerald-500">
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white py-2.5 rounded-xl font-bold text-xs transition hover:bg-emerald-500">Publish Item</button>
        </form>
    </div>

    <div class="lg:col-span-2 space-y-4">
        <h2 class="text-2xl font-black text-white tracking-tight mb-4">Inventory Board Ledger (<?= count($items) ?>)</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <?php foreach ($items as $item): ?>
                <div class="bg-slate-900/30 border border-slate-800/60 p-5 rounded-2xl flex flex-col justify-between hover:border-slate-700/60 transition">
                    <div>
                        <span class="text-[9px] font-extrabold px-2.5 py-1 rounded-full border uppercase tracking-wider <?= $item['type'] === 'Lost' ? 'bg-rose-500/10 border-rose-500/20 text-rose-400' : 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' ?>"><?= $item['type'] ?></span>
                        <h3 class="text-lg font-bold text-white mt-3 tracking-tight"><?= $item['item_name'] ?></h3>
                        <div class="text-xs space-y-1.5 mt-3 text-slate-400 bg-slate-950/60 border border-slate-800/40 p-3 rounded-xl">
                            <p>📍 <strong class="text-slate-500 font-bold uppercase text-[10px]">Location Vector:</strong> <?= $item['location'] ?></p>
                            <p>📞 <strong class="text-slate-500 font-bold uppercase text-[10px]">Active Contact:</strong> <?= $item['contact'] ?></p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-800/60 flex items-center justify-between text-[9px] font-bold text-slate-500 uppercase tracking-wider">
                        <span>Reporter: <?= htmlspecialchars($item['full_name']) ?> [<?= $item['role'] ?>]</span>
                        <span><?= $item['created_at'] ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>