(function($) {
    new WPDOptimisedVideoEmbed({
        id: '<?php echo $id ?>',
        videoData: <?php echo json_encode($module->get_video_data()); ?>,
        moduleSettings: <?php echo json_encode($settings); ?>
    });
})(jQuery);
