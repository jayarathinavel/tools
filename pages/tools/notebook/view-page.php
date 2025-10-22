<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/notebook/notebook-utils.php');
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/Parsedown.php';
    $id = $_GET['id'];
    $page = executeQuery("SELECT * FROM notebook_page WHERE id=$id")->fetch_assoc();
    $parsedown = new Parsedown();
    
    initializePage($page['page_name'], "main", $_SERVER['REQUEST_URI']);
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div class="container">
    <div class="mb-3 d-flex justify-content-end gap-2">
        <a href="view.php" class="btn btn-secondary">Back to Notebook</a>
        <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning">
            <i class="bi bi-pencil-fill"></i> Edit
        </a>
        <button id="downloadPdf" class="btn btn-success">
            <i class="bi bi-file-earmark-arrow-down"></i> Export as PDF
        </button>
    </div>

    
    <h1><?php echo htmlspecialchars($page['page_name']); ?></h1>
    <small class="text-muted">Created: <?php echo date('M d, Y H:i', strtotime($page['created_at'])); ?></small>
    <small class="text-muted"> | Updated: <?php echo date('M d, Y H:i', strtotime($page['updated_at'])); ?></small>
    
    <hr>
    
    <div class="markdown-content" style="line-height: 1.6;">
        <?php echo $parsedown->text($page['markdown_content']); ?>
    </div>
</div>

<script>
    document.getElementById("downloadPdf").addEventListener("click", async () => {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF("p", "mm", "a4");
        const element = document.querySelector(".markdown-content");

        // Clone element for A4 export
        const clone = element.cloneNode(true);
        clone.style.width = "210mm";
        clone.style.minHeight = "297mm";
        clone.style.padding = "20mm";
        clone.style.margin = "0 auto";
        clone.style.background = "white";
        clone.style.fontFamily = "Arial, sans-serif";
        clone.style.lineHeight = "1.6";
        clone.style.boxSizing = "border-box";
        clone.style.position = "absolute";
        clone.style.left = "-9999px";
        document.body.appendChild(clone);

        // Capture to canvas
        const canvas = await html2canvas(clone, {
            backgroundColor: "#ffffff",
            scale: 2,
            useCORS: true
        });

        document.body.removeChild(clone);

        const imgData = canvas.toDataURL("image/png");
        const imgProps = pdf.getImageProperties(imgData);
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();

        const pdfWidth = pageWidth;
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        let heightLeft = pdfHeight;
        let position = 0;

        // Add first page
        pdf.addImage(imgData, "PNG", 0, position, pdfWidth, pdfHeight, "", "FAST");
        heightLeft -= pageHeight;

        // Add more pages only if content overflows
        while (heightLeft > 1) { // <-- the key fix: threshold avoids empty pages
            position = heightLeft - pdfHeight;
            pdf.addPage();
            pdf.addImage(imgData, "PNG", 0, position, pdfWidth, pdfHeight, "", "FAST");
            heightLeft -= pageHeight;
        }

        pdf.save("<?php echo preg_replace('/[^a-zA-Z0-9_-]/', '_', $page['page_name']); ?>.pdf");
    });
</script>


<?php
    initializePageFooter($rootPath, $moduleType);
?>
