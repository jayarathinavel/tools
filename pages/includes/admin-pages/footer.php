<div class="mt-4"></div>
<script>
    function goBack() {
    window.history.back();
    }

    function checkHistory() {
    if (window.history.length > 1) {
        document.querySelector('.back-button').style.display = 'inline-block';
    } else {
        document.querySelector('.back-button').style.display = 'none';
    }
    }

    window.onload = checkHistory;
</script>

<!-- Bootstrap JS and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
