<?php
require_once 'config/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'faculty' && isset($_POST['update_status'])) {
    $complaint_id = intval($_POST['complaint_id']);
    $next_state = $_POST['status'];
    $update_stmt = $pdo->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $update_stmt->execute([$next_state, $complaint_id]);
    header("Location: complaints.php");
    exit;
}

if ($role === 'student' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_complaint'])) {
    $title = htmlspecialchars(trim($_POST['title']));
    $category = htmlspecialchars($_POST['category']);
    $description = htmlspecialchars(trim($_POST['description']));

    if (!empty($title) && !empty($description)) {
        $insert_stmt = $pdo->prepare("INSERT INTO complaints (student_id, title, category, description) VALUES (?, ?, ?, ?)");
        $insert_stmt->execute([$user_id, $title, $category, $description]);
        header("Location: complaints.php");
        exit;
    }
}

if ($role === 'faculty') {
    $complaints = $pdo->query("SELECT c.*, u.full_name, u.college_id, u.branch, u.section FROM complaints c JOIN users u ON c.student_id = u.id ORDER BY c.id DESC")->fetchAll();
} else {
    $complaints = $pdo->prepare("SELECT c.*, u.full_name, u.college_id FROM complaints c JOIN users u ON c.student_id = u.id WHERE c.student_id = ? ORDER BY c.id DESC");
    $complaints->execute([$user_id]);
    $complaints = $complaints->fetchAll();
}

include 'includes/header.php';
?>
<div class="mb-6">
    <a href="dashboard.php" class="inline-flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-indigo-400 transition group">
        <span class="transform group-hover:-translate-x-1 transition duration-200">&larr;</span> <span>Back to Dashboard</span>
    </a>
</div>

<div class="grid lg:grid-cols-3 gap-8">
    <?php if ($role === 'student'): ?>
        <div class="lg:col-span-1 bg-slate-900/40 border border-slate-800/80 p-6 rounded-2xl backdrop-blur-md h-fit">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-bold text-white tracking-tight">Lodge Ticket</h2>
                <span class="text-[9px] bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 px-2 py-0.5 rounded font-extrabold uppercase">Smart Core Active</span>
            </div>
            <p class="text-slate-500 text-xs mb-6">Autonomous text modeling will assist categorization vectors below.</p>
            
            <form action="complaints.php" method="POST" class="space-y-4">
                <input type="hidden" name="file_complaint" value="1">
                <div>
                    <label class="block text-slate-400 text-xs font-semibold mb-1">Issue Headline</label>
                    <input type="text" name="title" required class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-semibold mb-1">Infrastructure Node (Category)</label>
                    <select id="categorySelect" name="category" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-2.5 text-xs text-slate-300 focus:outline-none focus:border-indigo-500 transition">
                        <option value="Hostel Maintenance">Hostel Maintenance</option>
                        <option value="Server Network / Wi-Fi">Server Network / Wi-Fi</option>
                        <option value="Hardware Lab Systems">Hardware Lab Systems</option>
                        <option value="Mess / Air Flow Unit">Mess / Air Flow Unit</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-semibold mb-1">Operational Description</label>
                    <textarea id="descriptionField" name="description" rows="4" required placeholder="Type issue metrics... (e.g., 'the system lab router fell offline')" class="w-full bg-slate-950/60 border border-slate-800 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:border-indigo-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 rounded-xl font-bold text-xs transition">Transmit Ticket</button>
            </form>
        </div>

        <script>
            document.getElementById('descriptionField').addEventListener('input', function(e) {
                const text = e.target.value.toLowerCase();
                const selector = document.getElementById('categorySelect');
                
                if (text.includes('wifi') || text.includes('network') || text.includes('internet') || text.includes('router')) {
                    selector.value = "Server Network / Wi-Fi";
                } else if (text.includes('pc') || text.includes('lab') || text.includes('computer') || text.includes('hardware') || text.includes('monitor')) {
                    selector.value = "Hardware Lab Systems";
                } else if (text.includes('food') || text.includes('mess') || text.includes('lunch') || text.includes('cafeteria')) {
                    selector.value = "Mess / Air Flow Unit";
                } else if (text.includes('hostel') || text.includes('room') || text.includes('fan') || text.includes('bed')) {
                    selector.value = "Hostel Maintenance";
                }
            });
        </script>
    <?php else: ?>
        <div class="lg:col-span-1 bg-gradient-to-br from-slate-950 to-indigo-950/60 border border-indigo-900/40 p-6 rounded-2xl h-fit">
            <h2 class="text-xl font-bold text-white tracking-tight mb-2">Faculty Intercept Mode</h2>
            <p class="text-slate-400 text-xs leading-relaxed">You are auditing student infrastructure logs across campus systems. Update resolution pipelines instantly to sync tracking indicators on student monitors.</p>
        </div>
    <?php endif; ?>

    <div class="lg:col-span-2 space-y-4">
        <h2 class="text-2xl font-black text-white tracking-tight mb-4">Active System Registers (<?= count($complaints) ?>)</h2>
        <?php foreach ($complaints as $c): ?>
            <div class="bg-slate-900/30 border border-slate-800/60 p-5 rounded-2xl flex flex-col justify-between hover:border-slate-700/60 transition">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] uppercase font-extrabold tracking-wider bg-slate-950 border border-slate-800 px-2.5 py-1 rounded-md text-indigo-400"><?= $c['category'] ?></span>
                            <h3 class="text-lg font-bold text-white mt-2 tracking-tight"><?= $c['title'] ?></h3>
                        </div>
                    </div>
                    <p class="text-slate-400 text-xs mt-3 leading-relaxed"><?= htmlspecialchars($c['description']) ?></p>
                    
                    <div class="mt-6 bg-slate-950/50 border border-slate-900 rounded-xl p-4">
                        <span class="text-[9px] uppercase font-extrabold tracking-widest text-slate-500 block mb-3">Live Processing Stream</span>
                        <div class="flex items-center justify-between text-[10px] font-bold uppercase">
                            <div class="flex flex-col items-center flex-1">
                                <div class="h-4 w-4 rounded-full border-2 border-indigo-500 bg-indigo-500 flex items-center justify-center text-[8px] text-white">✓</div>
                                <span class="text-indigo-400 mt-1 text-[9px]">Logged</span>
                            </div>
                            <div class="h-0.5 bg-slate-800 flex-grow -mt-3 <?= ($c['status'] === 'In Progress' || $c['status'] === 'Resolved') ? 'bg-indigo-500' : '' ?>"></div>
                            <div class="flex flex-col items-center flex-1">
                                <div class="h-4 w-4 rounded-full border-2 <?= ($c['status'] === 'In Progress' || $c['status'] === 'Resolved') ? 'border-sky-500 bg-sky-500 text-white' : 'border-slate-800 bg-slate-950 text-slate-700' ?> flex items-center justify-center text-[8px]">•</div>
                                <span class="<?= ($c['status'] === 'In Progress' || $c['status'] === 'Resolved') ? 'text-sky-400' : 'text-slate-600' ?> mt-1 text-[9px]">Review</span>
                            </div>
                            <div class="h-0.5 bg-slate-800 flex-grow -mt-3 <?= ($c['status'] === 'Resolved') ? 'bg-indigo-500' : '' ?>"></div>
                            <div class="flex flex-col items-center flex-1">
                                <div class="h-4 w-4 rounded-full border-2 <?= $c['status'] === 'Resolved' ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-800 bg-slate-950 text-slate-700' ?> flex items-center justify-center text-[8px]">•</div>
                                <span class="<?= $c['status'] === 'Resolved' ? 'text-emerald-400' : 'text-slate-600' ?> mt-1 text-[9px]">Resolved</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-800/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                    <span>Filer: <?= htmlspecialchars($c['full_name']) ?> (<?= htmlspecialchars($c['college_id']) ?>) <?= isset($c['branch']) ? " | {$c['branch']} [{$c['section']}]" : "" ?></span>
                    <span>System Mark: <?= $c['created_at'] ?></span>
                </div>

                <?php if ($role === 'faculty'): ?>
                    <form action="complaints.php" method="POST" class="mt-4 pt-3 border-t border-dashed border-slate-800/80 flex items-center justify-end space-x-2">
                        <input type="hidden" name="update_status" value="1">
                        <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                        <select name="status" class="bg-slate-950 border border-slate-800 rounded-lg text-[11px] font-bold text-slate-300 p-1.5 focus:outline-none">
                            <option value="Pending" <?= $c['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="In Progress" <?= $c['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Resolved" <?= $c['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                        </select>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1.5 rounded-lg text-[10px] font-extrabold uppercase transition">Update State</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>