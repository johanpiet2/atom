<?php decorate_with('layout_1col.php'); ?>

<?php slot('title'); ?>
  <h1 class="multiline">
    <?php echo image_tag('/images/icons-large/icon-new.png', ['width' => '42', 'height' => '42']); ?>
    <?php echo __('Browse Authority Record/Actor Report'); ?>
  </h1>
<?php end_slot(); ?>

<?php slot('content'); ?>

<style>
  .authority-report-frame {
    width: 100%;
    height: calc(100vh - 250px);
    min-height: 600px;
    border: none;
    background: white;
  }
</style>

<iframe 
  class="authority-report-frame"
  src="/ext/reports/authority_report.php<?php echo !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>"
  title="<?php echo __('Browse Authority Record/Actor Report'); ?>">
</iframe>

<script>
// Pass through any form submissions from within the iframe
document.addEventListener('DOMContentLoaded', function() {
  var iframe = document.querySelector('.authority-report-frame');
  
  // Adjust iframe height if needed
  if (iframe) {
    iframe.onload = function() {
      try {
        // This might fail due to same-origin policy, but worth trying
        var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        if (iframeDoc.body.scrollHeight) {
          iframe.style.height = (iframeDoc.body.scrollHeight + 20) + 'px';
        }
      } catch(e) {
        // Cross-origin restriction, keep default height
      }
    };
  }
});
</script>

<?php end_slot(); ?>