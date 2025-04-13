// Initialize color picker.
jQuery(document).ready(function () {
  jQuery("input[type=color]").wpColorPicker();
});

// Initialize Select2.
jQuery(document).ready(function ($) {
  $("#gal_restricted_post_types").select2({
    data: galData.postTypes,
    width: "50%",
  });
});
