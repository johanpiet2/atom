<?php

function render_digital_object_viewer($resource, $obj)
{
    $fileName = strtolower($obj->name);
    $mime     = strtolower($obj->mimeType);

    $request = sfContext::getInstance()->getRequest();
    $root    = $request->getRelativeUrlRoot();

    // Build correct full link
    $siteBase = rtrim(QubitSetting::getByName('siteBaseUrl'), '/');
    $digitalObjectLink = $siteBase . $obj->path . '/' . $obj->name;

    // Fix duplicated install folder path (e.g. .../atom_psis/atom_psis/)
    $inst = basename($siteBase);
    $digitalObjectLink = preg_replace('#(' . preg_quote($inst, '#') . '/)+#', $inst . '/', $digitalObjectLink);

	$realObj = ($obj instanceof sfOutputEscaper) ? $obj->getRawValue() : $obj;

	$raw = ltrim($realObj->path, '/');  // ALWAYS correct path from database
	$clean = rtrim($raw, '/') . '/' . $realObj->name;

	$clean = preg_replace('#/+#', '/', $clean);
	$iiifIdentifier = str_replace('/', '_SL_', $clean);



	// Debug
	// echo "<script>alert('iiifIdentifier=$iiifIdentifier\npath=$path');</script>";

    // Extension
    $ext = strtolower(pathinfo($obj->name, PATHINFO_EXTENSION));

    // ================================
    // TYPE DETECTION
    // ================================

    // 3D model
    $threeDFormats = ['glb', 'gltf', 'obj', 'stl', 'ply', 'fbx', 'usdz'];

    $is3D = (
        in_array($ext, $threeDFormats) ||
        strpos($mime, 'model') !== false ||
        (strpos($mime, 'octet-stream') !== false && in_array($ext, $threeDFormats))
    );

    // IIIF formats (TIFF, JP2, etc.)
    $iiifFormats = ['tif', 'tiff', 'jp2', 'jpf', 'jpx', 'j2k', 'j2c'];

    $isIIIF = (
        in_array($ext, $iiifFormats) ||
        strpos($mime, 'tiff') !== false ||
        strpos($mime, 'jp2') !== false ||
        strpos($mime, 'jpeg2000') !== false
    );

    // PDF
    $isPDF = ($ext === 'pdf' || $mime === 'application/pdf');

    // Images
    $imageFormats = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'heic', 'heif'];

    $isImage = (
        !$is3D &&
        !$isIIIF &&    // "pure" images (non-IIIF)
        in_array($ext, $imageFormats)
    );

    // Audio
    $audioFormats = ['mp3', 'wav', 'flac', 'aac', 'ogg', 'm4a'];
    $isAudio = in_array($ext, $audioFormats);

    // Video
    $videoFormats = ['mp4', 'mov', 'avi', 'mkv', 'webm', 'wmv', 'flv'];
    $isVideo = in_array($ext, $videoFormats);

    // Text Docs
    $textFormats = ['txt', 'csv', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'rtf'];
    $isTextDoc = in_array($ext, $textFormats);


	// ============================
	// Detect multipage TIFF safely
	// ============================
	$isMultiPageTiff = false;
    $multiPageCount  = 0;

	if ($isIIIF && ($ext === 'tif' || $ext === 'tiff' || strpos($mime, 'tiff') !== false)) {

		if (class_exists('Imagick')) {

			$realObj = ($obj instanceof sfOutputEscaper)
				? $obj->getRawValue()
				: $obj;

			// getFullPath() returns: /uploads/... (web path)
			$filePath = $realObj->getFullPath();

			// Convert to actual filesystem path
			// ALWAYS fix path — no conditions.
			// AtoM stores uploads relative to web root, not filesystem.
			$filePath = sfConfig::get('sf_root_dir') . $filePath;

			error_log("TIFF CHECK FILEPATH = $filePath");

			if (file_exists($filePath)) {
				try {
					$imagick = new Imagick($filePath);
					if ($imagick->getNumberImages() > 1) {
						$isMultiPageTiff = true;
					}
				} catch (Exception $e) {
					error_log("TIFF check error: " . $e->getMessage());
				}
			} else {
				error_log("TIFF file not found: $filePath");
			}

		} else {
			error_log("Imagick NOT installed — skipping TIFF multipage check.");
		}
	}

    // ================================
    // TYPE LABEL (for heading)
    // ================================
    if ($is3D) {
        $typeLabel = '3D';
    } elseif ($isIIIF && $isMultiPageTiff) {
        $typeLabel = 'IIIF (Multi-page TIFF)';
    } elseif ($isIIIF) {
        $typeLabel = 'IIIF';
    } elseif ($isImage) {
        $typeLabel = 'Image';
    } elseif ($isPDF) {
        $typeLabel = 'PDF';
    } elseif ($isAudio) {
        $typeLabel = 'Audio';
    } elseif ($isVideo) {
        $typeLabel = 'Video';
    } elseif ($isTextDoc) {
        $typeLabel = 'Text Document';
    } else {
        $typeLabel = 'File';
    }

    ob_start();
    ?>

    <div class="digital-object-block" style="margin-bottom:40px;">

        <h3>
            <b><?php echo esc_entities($obj->name); ?></b>
            <span style="color:#008000; font-weight:600; font-size:0.9em;">
                (<?php echo $typeLabel; ?>)
            </span>
        </h3>

        <?php
        // ==============================
        // 3D VIEWER
        // ==============================
        if ($is3D) { ?>

            <div id="threejs-viewer-<?php echo $obj->id ?>" style="width:100%;height:600px;background:#000;"></div>

            <div class="viewer-help text-muted small mt-2">
                <strong>How to use:</strong><br>
                • Drag with mouse to rotate the object<br>
                • Scroll to zoom<br>
                • Right mouse button to pan<br>
                • Touch: pinch to zoom & drag to rotate
            </div>

            <script src="/atom_psis/3d/js/three.min.js"></script>
            <script src="/atom_psis/3d/js/GLTFLoader.js"></script>
            <script src="/atom_psis/3d/js/OrbitControls.js"></script>
            <script>
            document.addEventListener("DOMContentLoaded", function () {
                const container = document.getElementById("threejs-viewer-<?php echo $obj->id ?>");
                const scene = new THREE.Scene();
                scene.background = new THREE.Color(0x111111);

                const camera = new THREE.PerspectiveCamera(60, container.clientWidth / 600, 0.1, 1000);
                camera.position.set(2, 2, 3);

                const renderer = new THREE.WebGLRenderer({ antialias: true });
                renderer.setSize(container.clientWidth, 600);
                container.appendChild(renderer.domElement);

                const hemi = new THREE.HemisphereLight(0xffffff, 0x444444, 1.2);
                scene.add(hemi);

                const dir = new THREE.DirectionalLight(0xffffff, 1.0);
                dir.position.set(4, 10, 5);
                scene.add(dir);

                const controls = new THREE.OrbitControls(camera, renderer.domElement);
                controls.enableDamping = true;

                const loader = new THREE.GLTFLoader();
                loader.load("<?php echo $digitalObjectLink ?>", function (gltf) {
                    scene.add(gltf.scene);
                });

                function animate() {
                    requestAnimationFrame(animate);
                    controls.update();
                    renderer.render(scene, camera);
                }
                animate();
            });
            </script>

        <?php
		// ==============================
		// MULTI-PAGE TIFF via IIIF + OSD sequence
		// ==============================
		} elseif ($isIIIF && $isMultiPageTiff) {

$typeLabel = 'IIIF - Multi-page TIFF';

    // Count pages
    $pageCount = 1;
    if (class_exists('Imagick')) {
        $realObj = ($obj instanceof sfOutputEscaper) ? $obj->getRawValue() : $obj;
        $filePath = sfConfig::get('sf_root_dir') . $realObj->getFullPath();
        try {
            $imagick = new Imagick($filePath);
            $pageCount = $imagick->getNumberImages();
            $imagick->destroy();
        } catch (Exception $e) {
            error_log("Error counting TIFF pages: " . $e->getMessage());
        }
    }

    $viewerId = "iiif-seq-" . $obj->id;
    
    // Use the ALREADY BUILT identifier from the top of your function
    // (This is already correctly built at line 15-20 of your code)
    // $iiifIdentifier is already available and correct!
    
    ?>
    <div id="<?php echo $viewerId ?>" style="width:100%;height:600px;background:black;"></div>

    <div class="viewer-help text-muted small mt-2">
        <strong>How to use:</strong><br>
        • Use arrow buttons or number keys to navigate pages<br>
        • Scroll to zoom, drag to pan<br>
        • Navigator window (top-right) shows overview<br>
        • Total pages: <?php echo $pageCount; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/openseadragon@3.1.0/build/openseadragon/openseadragon.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const baseUrl = "<?php echo $request->getUriPrefix() . $root ?>/iiif/2";
        const identifier = "<?php echo $iiifIdentifier ?>";
        const totalPages = <?php echo (int)$pageCount ?>;
        
        console.log("Multi-page TIFF viewer initialization:");
        console.log("- Base URL:", baseUrl);
        console.log("- Identifier:", identifier);
        console.log("- Total pages:", totalPages);
        
        // For multi-page TIFFs, we need to create separate identifiers for each page
        // Cantaloupe expects the page to be part of the identifier, not a parameter
        const tileSources = [];
        
        for (let page = 0; page < totalPages; page++) {
            // Build the full URL for each page's info.json
            // Format: /iiif/2/{identifier};{page}/info.json
            // Using semicolon as the meta-identifier delimiter (as configured in Cantaloupe)
            const pageIdentifier = identifier + ";" + (page + 1);
            const infoUrl = baseUrl + "/" + pageIdentifier + "/info.json";
            
            console.log("Page " + (page + 1) + " URL:", infoUrl);
            tileSources.push(infoUrl);
        }
        
        const viewer = OpenSeadragon({
            id: "<?php echo $viewerId ?>",
            prefixUrl: "https://cdn.jsdelivr.net/npm/openseadragon@3.1.0/build/openseadragon/images/",
            tileSources: tileSources,
            sequenceMode: true,
            showSequenceControl: true,
            showNavigator: true,
            showRotationControl: true,
            showReferenceStrip: true,
            referenceStripScroll: 'horizontal',
            referenceStripHeight: 120,
            referenceStripSizeRatio: 0.2,
            initialPage: 0,
            visibilityRatio: 1,
            constrainDuringPan: true
        });
        
        // Add event handlers
        viewer.addHandler('open', function(event) {
            console.log('Viewer opened successfully');
        });
        
        viewer.addHandler('open-failed', function(event) {
            console.error('Failed to open tile source:', event);
            // Log more details about the failure
            if (event.source) {
                console.error('Failed source:', event.source);
            }
        });
        
        viewer.addHandler('page', function(event) {
            console.log('Switched to page:', event.page + 1);
        });
    });
    </script>

        <?php
        // ==============================
        // SINGLE-PAGE IIIF IMAGE
        // ==============================
        } elseif ($isIIIF) {

            $viewerId = 'iiif-viewer-' . $obj->id;
            ?>

            <div id="<?php echo $viewerId ?>" style="width:100%;height:600px;background:black;"></div>

            <div class="viewer-help text-muted small mt-2">
                <strong>How to use:</strong><br>
                • Scroll to zoom<br>
                • Click and drag to move<br>
                • SHIFT + drag to rotate (if enabled)<br>
                • Navigator window (top-right) shows overview
            </div>

            <script src="https://cdn.jsdelivr.net/npm/openseadragon@3.1.0/build/openseadragon/openseadragon.min.js"></script>
            <script>
            document.addEventListener("DOMContentLoaded", function () {
                OpenSeadragon({
                    id: "<?php echo $viewerId ?>",
                    prefixUrl: "https://cdn.jsdelivr.net/npm/openseadragon@3.1.0/build/openseadragon/images/",
                    tileSources: "<?php echo $request->getUriPrefix() . $root ?>/iiif/2/<?php echo $iiifIdentifier ?>/info.json",
                    showRotationControl: true,
                    showNavigator: true
                });
            });
            </script>

        <?php
        // ==============================
        // ZOOMPAN IMAGE (non-IIIF images)
        // ==============================
        } elseif ($isImage) { ?>

            <div id="zoom-pan-viewer-<?php echo $obj->id ?>"
                 class="zoom-pan-container"
                 style="width:100%;height:600px;background:#000;position:relative;">

                <div class="zoom-pan-stage"
                     style="width:100%;height:100%;overflow:auto;position:relative;">
                    <img src="<?php echo $digitalObjectLink; ?>"
                         style="max-width:none;display:block;">
                </div>

                <div class="zoom-pan-toolbar"
                     style="position:absolute;top:10px;right:10px;z-index:10;">
                    <button class="zp-btn zoom-in" data-action="zoom-in"></button>
                    <button class="zp-btn zoom-out" data-action="zoom-out"></button>
                    <button class="zp-btn rotate-left" data-action="rotate-left"></button>
                    <button class="zp-btn rotate-right" data-action="rotate-right"></button>
                    <button class="zp-btn reset-btn" data-action="reset"></button>
                    <button class="zp-btn fullscreen-btn" data-action="fullscreen"></button>
                </div>
            </div>

            <div class="viewer-help text-muted small mt-2">
                <strong>How to use:</strong><br>
                • Scroll to zoom<br>
                • Click and drag to move<br>
                • Buttons top-right: zoom, rotate, reset, fullscreen
            </div>

            <link rel="stylesheet" href="/atom-extensions/extensions/zoom-pan/public/zoom-pan.css">
            <script src="/atom-extensions/extensions/zoom-pan/public/zoom-pan.js"></script>
            <script>
            document.addEventListener("DOMContentLoaded", function () {
                new ZoomPanViewer("#zoom-pan-viewer-<?php echo $obj->id ?>", {
                    mimeType: "<?php echo $mime; ?>",
                    height: 600
                });
            });
            </script>

        <?php
        // ==============================
        // PDF
        // ==============================
        } elseif ($isPDF) { ?>

            <iframe src="<?php echo $digitalObjectLink ?>" width="100%" height="800px" style="border:0;"></iframe>

            <div class="viewer-help text-muted small mt-2">
                <strong>How to use:</strong><br>
                • Scroll to view pages<br>
                • Use built-in toolbar to download or print<br>
                • Right-click → “Save as…” to download the file
            </div>

        <?php
        // ==============================
        // AUDIO
        // ==============================
        } elseif ($isAudio) { ?>

            <audio controls style="width:100%;">
                <source src="<?php echo $digitalObjectLink ?>" type="<?php echo $mime; ?>">
                Your browser does not support audio playback.
            </audio>

            <div class="viewer-help text-muted small mt-2">
                <strong>How to use:</strong><br>
                • Press Play to listen<br>
                • Right-click → “Save as…” to download the audio file
            </div>

        <?php
        // ==============================
        // VIDEO
        // ==============================
        } elseif ($isVideo) { ?>

            <video controls style="width:100%; max-height:600px; background:black;">
                <source src="<?php echo $digitalObjectLink ?>" type="<?php echo $mime; ?>">
                Your browser does not support video playback.
            </video>

            <div class="viewer-help text-muted small mt-2">
                <strong>How to use:</strong><br>
                • Press Play to watch<br>
                • Double-click video to full-screen<br>
                • Right-click → “Save as…” to download the video
            </div>

        <?php
        // ==============================
        // TEXT DOCUMENTS
        // ==============================
        } elseif ($isTextDoc) { ?>

            <div class="alert alert-info">
                <strong>Document:</strong>
                <a href="<?php echo $digitalObjectLink ?>" target="_blank">Download / Open file</a>
            </div>

            <div class="viewer-help text-muted small mt-2">
                <strong>How to use:</strong><br>
                • Click above to open the file<br>
                • Will download or open in your default application (Word, Excel, etc.)
            </div>

        <?php
        // ==============================
        // OTHER FILES
        // ==============================
        } else { ?>

            <div class="alert alert-info">
                <strong>Download:</strong>
                <a href="<?php echo $digitalObjectLink ?>" target="_blank">
                    <?php echo $obj->name ?>
                </a>
            </div>

            <div class="viewer-help text-muted small mt-2">
                <strong>How to use:</strong><br>
                • Click the link above to download the file<br>
                • Open it locally with the appropriate application
            </div>

        <?php } ?>

    </div>

    <?php
    return ob_get_clean();
}
