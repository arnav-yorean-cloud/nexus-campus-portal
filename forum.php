<?php
require_once 'config/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Action Intercept A: Inserting a root main stream post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_forum'])) {
    $message = htmlspecialchars(trim($_POST['message']));
    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO forum_posts (user_id, message) VALUES (?, ?)");
        $stmt->execute([$user_id, $message]);
        header("Location: forum.php");
        exit;
    }
}

// Action Intercept B: Inserting a nested message thread reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply'])) {
    $parent_post_id = intval($_POST['post_id']);
    $reply_message = htmlspecialchars(trim($_POST['reply_message']));
    
    if (!empty($reply_message)) {
        $stmt = $pdo->prepare("INSERT INTO forum_replies (post_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$parent_post_id, $user_id, $reply_message]);
        header("Location: forum.php?expanded=" . $parent_post_id);
        exit;
    }
}

// Search Filtering Configuration
$search_query = '';
if (!empty($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $stmt = $pdo->prepare("SELECT fp.*, u.full_name, u.college_id, u.role FROM forum_posts fp JOIN users u ON fp.user_id = u.id WHERE fp.message LIKE ? OR u.full_name LIKE ? ORDER BY fp.id DESC");
    $stmt->execute(["%$search_query%", "%$search_query%"]);
    $posts = $stmt->fetchAll();
} else {
    $posts = $pdo->query("SELECT fp.*, u.full_name, u.college_id, u.role FROM forum_posts fp JOIN users u ON fp.user_id = u.id ORDER BY fp.id DESC")->fetchAll();
}

include 'includes/header.php';
?>
<div class="mb-6">
    <a href="dashboard.php" class="inline-flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-indigo-400 transition group">
        <span class="transform group-hover:-translate-x-1 transition duration-200">&larr;</span> <span>Back to Dashboard</span>
    </a>
</div>

<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-slate-900/30 border border-slate-800/80 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 backdrop-blur-md">
        <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider">💬 Scan Feed Stream</h3>
        <form action="forum.php" method="GET" class="w-full sm:w-auto flex items-center space-x-2">
            <input type="text" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="Filter posts..." class="bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-purple-500 w-full sm:w-64 transition">
            <?php if(!empty($search_query)): ?>
                <a href="forum.php" class="text-xs text-slate-500 hover:text-white transition">Clear</a>
            <?php endif; ?>
            <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs px-4 py-2 rounded-xl transition">Filter</button>
        </form>
    </div>

    <div class="bg-slate-900/40 border border-slate-800/80 p-6 rounded-2xl backdrop-blur-md">
        <h2 class="text-xl font-bold text-white mb-2 tracking-tight">Transmit Communication Stream</h2>
        <form action="forum.php" method="POST" class="space-y-3">
            <input type="hidden" name="post_forum" value="1">
            <textarea name="message" rows="3" required placeholder="What notices require campus visibility?" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-purple-500"></textarea>
            <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white px-5 py-2 rounded-xl font-bold text-xs shadow-lg shadow-purple-600/10 transition">Inject Stream Vector</button>
        </form>
    </div>

    <div class="space-y-4">
        <h2 class="text-2xl font-black text-white tracking-tight">Unified Async Feed (<?= count($posts) ?>)</h2>
        <?php foreach ($posts as $p): 
            // Pull any nested comments saved under this specific post row id
            $reply_stmt = $pdo->prepare("SELECT fr.*, u.full_name, u.college_id, u.role FROM forum_replies fr JOIN users u ON fr.user_id = u.id WHERE fr.post_id = ? ORDER BY fr.id ASC");
            $reply_stmt->execute([$p['id']]);
            $replies = $reply_stmt->fetchAll();
            
            // Retain open view state if the last action was targeting this comment box
            $is_force_expanded = (isset($_GET['expanded']) && intval($_GET['expanded']) === $p['id']);
        ?>
            <div class="bg-slate-900/30 border border-slate-800/60 p-5 rounded-2xl hover:border-slate-700/60 transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 font-extrabold text-sm flex items-center justify-center">
                                <?= strtoupper(substr($p['full_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm tracking-tight"><?= htmlspecialchars($p['full_name']) ?> <span class="text-[10px] text-purple-400 ml-1.5 font-semibold bg-purple-500/5 border border-purple-500/10 px-2 py-0.5 rounded-full uppercase tracking-widest"><?= $p['role'] ?></span></h4>
                                <span class="text-[10px] font-bold text-slate-500 tracking-wider uppercase"><?= htmlspecialchars($p['college_id']) ?></span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 uppercase tracking-wider"><?= $p['created_at'] ?></span>
                    </div>
                    <p class="text-slate-300 text-sm mt-4 leading-relaxed bg-slate-950/40 border border-slate-900 p-4 rounded-xl font-medium"><?= nl2br($p['message']) ?></p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-800/40 flex items-center justify-between">
                    <button onclick="toggleReplies(<?= $p['id'] ?>)" class="text-[11px] font-bold uppercase tracking-wider text-purple-400 hover:text-purple-300 transition flex items-center space-x-1.5">
                        <span id="icon-<?= $p['id'] ?>"><?= $is_force_expanded ? '▼' : '▶' ?></span> 
                        <span>Discussion Hub (<span id="count-<?= $p['id'] ?>"><?= count($replies) ?></span>)</span>
                    </button>
                </div>

                <div id="nested-container-<?= $p['id'] ?>" class="<?= $is_force_expanded ? '' : 'hidden' ?> mt-4 pt-4 border-t border-dashed border-slate-800 space-y-3">
                    <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
                        <?php if (count($replies) === 0): ?>
                            <p class="text-xs text-slate-600 italic pl-4">No active conversational nodes pinned here.</p>
                        <?php endif; ?>
                        <?php foreach ($replies as $r): ?>
                            <div class="bg-slate-950/40 border border-slate-800/40 rounded-xl p-3.5 ml-6">
                                <div class="flex items-center justify-between text-[10px] text-slate-500 font-bold mb-1.5">
                                    <span class="text-slate-300 font-extrabold"><?= htmlspecialchars($r['full_name']) ?> <span class="text-[8px] bg-slate-800 border border-slate-700 text-purple-400 px-1.5 py-0.5 rounded ml-1 uppercase"><?= $r['role'] ?></span></span>
                                    <span><?= $r['created_at'] ?></span>
                                </div>
                                <p class="text-xs text-slate-400 leading-relaxed font-medium"><?= nl2br($r['message']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <form action="forum.php" method="POST" class="mt-3 flex gap-2 ml-6">
                        <input type="hidden" name="submit_reply" value="1">
                        <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
                        <input type="text" name="reply_message" required placeholder="Type direct reply thread node..." class="flex-grow bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500 transition placeholder-slate-700">
                        <button type="submit" class="bg-purple-600/20 hover:bg-purple-600 border border-purple-500/30 text-purple-300 hover:text-white font-bold text-xs px-4 py-2 rounded-xl transition">Transmit</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function toggleReplies(id) {
    const container = document.getElementById('nested-container-' + id);
    const icon = document.getElementById('icon-' + id);
    
    if (container.classList.contains('hidden')) {
        container.classList.remove('hidden');
        icon.innerText = '▼';
    } else {
        container.classList.add('hidden');
        icon.innerText = '▶';
    }
}
</script>
<?php include 'includes/footer.php'; ?>