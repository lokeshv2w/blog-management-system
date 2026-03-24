<?php
require_once __DIR__ . '/includes/header.php';

if (!is_logged_in() || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    redirect('index.php');
}

$upload_dir = __DIR__ . '/../assets/images/uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Read and collect files
$files = array_diff(scandir($upload_dir), array('.', '..'));
$mediaList = [];

foreach ($files as $file) {
    $path = $upload_dir . $file;
    if (is_file($path)) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        if (in_array($ext, $allowed)) {
            $mediaList[] = [
                'name' => htmlspecialchars($file),
                'url' => '../assets/images/uploads/' . htmlspecialchars($file),
                'size' => round(filesize($path) / 1024, 2) . ' KB',
                'time' => filemtime($path),
                'date_formatted' => date('M j, Y', filemtime($path))
            ];
        }
    }
}
// Sort by newest first
usort($mediaList, function($a, $b) { return $b['time'] - $a['time']; });
?>

<style>
.dropzone {
    border: 2px dashed var(--primary);
    border-radius: var(--radius-lg);
    background: #F8FAFC;
    padding: 3rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}
.dropzone:hover, .dropzone.dragover {
    background: #EFF6FF;
    border-color: var(--primary-dark);
}
.dropzone i {
    font-size: 3rem;
    color: var(--primary);
    margin-bottom: 1rem;
}
.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}
.media-item {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    position: relative;
    transition: transform 0.2s ease, box-shadow 0.2s;
}
.media-item:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}
.media-preview {
    width: 100%;
    height: 150px;
    object-fit: cover;
    background: #f1f5f9;
}
.media-info {
    padding: 0.75rem;
    font-size: 0.85rem;
}
.media-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.media-meta {
    display: flex;
    justify-content: space-between;
    color: var(--text-muted);
}
.media-actions {
    position: absolute;
    top: 5px;
    right: 5px;
    display: flex;
    gap: 0.25rem;
    opacity: 0;
    transition: opacity 0.2s ease;
}
.media-item:hover .media-actions {
    opacity: 1;
}
.btn-icon {
    width: 32px;
    height: 32px;
    border-radius: var(--radius);
    background: rgba(255,255,255,0.9);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--text-main);
    transition: background 0.2s;
}
.btn-icon:hover { background: #fff; color: var(--primary); }
.btn-icon.danger:hover { color: var(--danger); }
</style>

<div class="dashboard-header card-header" style="margin-bottom: 2rem; background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between;">
    <div>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Media Library</h1>
        <p style="color: var(--text-muted);">Upload, copy links, and manage your website images seamlessly.</p>
    </div>
</div>

<!-- Drag and Drop Zone -->
<div id="dropzone" class="dropzone">
    <i class="fas fa-cloud-upload-alt"></i>
    <h3 style="margin-bottom: 0.5rem; color: var(--text-main);">Drag & Drop files here</h3>
    <p style="color: var(--text-muted); margin-bottom: 1rem;">or click to browse your computer (JPG, PNG, GIF, WEBP)</p>
    <input type="file" id="fileInput" multiple accept="image/*" style="display: none;">
    
    <!-- Progress Indicator hidden by default -->
    <div id="upload-progress" style="display: none; width: 60%; margin: 1rem auto 0; background: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden;">
        <div id="progress-bar" style="width: 0%; height: 100%; background: var(--primary); transition: width 0.2s;"></div>
    </div>
</div>

<h4 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border);">Uploaded Files (<span id="file-count"><?php echo count($mediaList); ?></span>)</h4>

<!-- Dynamic Media Grid -->
<div id="media-grid" class="media-grid">
    <?php foreach ($mediaList as $item): ?>
    <div class="media-item" data-filename="<?php echo $item['name']; ?>">
        <img src="<?php echo $item['url']; ?>" class="media-preview" alt="Library Image">
        <div class="media-info">
            <div class="media-name" title="<?php echo $item['name']; ?>"><?php echo $item['name']; ?></div>
            <div class="media-meta">
                <span><?php echo $item['date_formatted']; ?></span>
                <span><?php echo $item['size']; ?></span>
            </div>
        </div>
        <div class="media-actions">
            <button class="btn-icon" title="Copy URL" onclick="copyUrl('<?php echo 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'], 2).'/assets/images/uploads/'.$item['name']; ?>')"><i class="fas fa-link"></i></button>
            <button class="btn-icon danger" title="Delete" onclick="deleteMedia('<?php echo $item['name']; ?>', this)"><i class="fas fa-trash"></i></button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
// File Upload Logic
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('fileInput');
const progressBar = document.getElementById('progress-bar');
const progressContainer = document.getElementById('upload-progress');

// Prevent default drag behaviors
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, preventDefaults, false);
});
function preventDefaults (e) { e.preventDefault(); e.stopPropagation(); }

// Highlight dropzone
['dragenter', 'dragover'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'), false);
});
['dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'), false);
});

// Handle drop and click
dropzone.addEventListener('drop', handleDrop, false);
dropzone.addEventListener('click', () => fileInput.click());
fileInput.addEventListener('change', function() { handleFiles(this.files); });

function handleDrop(e) { handleFiles(e.dataTransfer.files); }

function handleFiles(files) {
    if (files.length === 0) return;
    
    let formData = new FormData();
    // For simplicity, handle first file. If you want batch, loop through them.
    // Let's implement batch processing seamlessly via a loop making individual AJAX requests.
    progressContainer.style.display = 'block';
    
    Array.from(files).forEach((file, index) => {
        uploadFile(file, Array.from(files).length);
    });
}

function uploadFile(file, totalFiles) {
    let url = 'ajax_upload_media.php';
    let formData = new FormData();
    formData.append('file', file);
    
    let xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    
    xhr.upload.addEventListener("progress", function(e) {
        if (e.lengthComputable) {
            let percentComplete = (e.loaded / e.total) * 100;
            progressBar.style.width = percentComplete + '%';
        }
    }, false);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
            progressContainer.style.display = 'none';
            progressBar.style.width = '0%';
            
            if (xhr.status == 200) {
                try {
                    let res = JSON.parse(xhr.responseText);
                    if (res.status === 'success') {
                        addMediaToGrid(res.file);
                    } else {
                        alert("Error: " + res.message);
                    }
                } catch(e) {
                    alert("Invalid server response.");
                }
            } else {
                alert("Server connection failed.");
            }
        }
    };
    xhr.send(formData);
}

function addMediaToGrid(fileInfo) {
    const grid = document.getElementById('media-grid');
    const fullUrl = window.location.origin + window.location.pathname.replace('/admin/media.php', '') + '/assets/images/uploads/' + fileInfo.name;
    
    const html = `
    <div class="media-item" data-filename="${fileInfo.name}">
        <img src="${fileInfo.url}" class="media-preview" alt="Library Image">
        <div class="media-info">
            <div class="media-name" title="${fileInfo.name}">${fileInfo.name}</div>
            <div class="media-meta">
                <span>${fileInfo.time_formatted}</span>
                <span>${fileInfo.size} Bytes</span>
            </div>
        </div>
        <div class="media-actions">
            <button class="btn-icon" title="Copy URL" onclick="copyUrl('${fullUrl}')"><i class="fas fa-link"></i></button>
            <button class="btn-icon danger" title="Delete" onclick="deleteMedia('${fileInfo.name}', this)"><i class="fas fa-trash"></i></button>
        </div>
    </div>`;
    
    // Insert at beginning
    grid.insertAdjacentHTML('afterbegin', html);
    updateCount(1);
}

function updateCount(diff) {
    let span = document.getElementById('file-count');
    span.innerText = parseInt(span.innerText) + diff;
}

// Global UI functions
window.copyUrl = function(url) {
    navigator.clipboard.writeText(url).then(() => {
        alert("Image link copied to clipboard: \n" + url);
    });
};

window.deleteMedia = function(filename, btnNode) {
    if (!confirm("Are you sure you want to permanently delete this media file?")) return;
    
    let itemDiv = btnNode.closest('.media-item');
    let formData = new FormData();
    formData.append('filename', filename);
    
    fetch('ajax_delete_media.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            itemDiv.remove();
            updateCount(-1);
        } else {
            alert("Error: " + res.message);
        }
    }).catch(e => alert("Network error deleting file."));
};
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
